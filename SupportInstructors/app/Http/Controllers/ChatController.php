<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ConversationParticipant;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getContacts()
    {
        $user = User::find(Auth::id());
        $user->last_active_at = now();
        $user->save();

        $contacts = collect();
        $admin = User::find(1);
        if ($admin && $user->id != 1) $contacts->push($admin);

        if ($user->hasRole('STUDENT')) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student && $student->class_id) {
                try {
                    $class = Classes::with('advisor.user')->find($student->class_id);
                    if ($class && $class->advisor && $class->advisor->user) {
                        $contacts->push($class->advisor->user);
                    }
                } catch (\Exception $e) {
                }
            }
        } elseif ($user->hasRole('LECTURER')) {
            $lecturer = \App\Models\Lecturer::where('user_id', $user->id)->first();
            if ($lecturer) {
                try {
                    $classIds = Classes::where('advisor_id', $lecturer->id)->pluck('id');
                    $studentUserIds = Student::whereIn('class_id', $classIds)->pluck('user_id');
                    $students = User::whereIn('id', $studentUserIds)->get();
                    foreach ($students as $st) $contacts->push($st);
                } catch (\Exception $e) {
                }
            }
        } elseif ($user->hasRole('ADMIN')) {
            $allUsers = User::where('id', '!=', 1)->get();
            foreach ($allUsers as $u) $contacts->push($u);
        }

        $uniqueContacts = $contacts->unique('id')->values();
        $formattedContacts = [];

        foreach ($uniqueContacts as $contact) {
            $conversation = Conversation::where('type', 'private')
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('participants', function ($q) use ($contact) {
                    $q->where('user_id', $contact->id);
                })
                ->with(['messages' => function ($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                }])
                ->first();

            $latestMessage = $conversation ? $conversation->messages->first() : null;
            $unreadCount = 0;
            if ($conversation) {
                $unreadCount = Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', $contact->id)
                    ->whereNull('read_at')
                    ->count();
            }

            $contactData = $contact->toArray();
            $contactData['is_online'] = $contact->last_active_at && \Carbon\Carbon::parse($contact->last_active_at)->diffInMinutes(now()) <= 3;
            $contactData['unread_count'] = $unreadCount;

            if ($latestMessage) {
                $contactData['latest_message'] = ($latestMessage->sender_id == $user->id ? 'Bạn: ' : '') . $latestMessage->content;
                $contactData['latest_message_time'] = $latestMessage->created_at;
            } else {
                $contactData['latest_message'] = 'Bắt đầu trò chuyện';
                $contactData['latest_message_time'] = null;
            }

            $formattedContacts[] = $contactData;
        }

        usort($formattedContacts, function ($a, $b) {
            return strtotime($b['latest_message_time'] ?? 0) - strtotime($a['latest_message_time'] ?? 0);
        });

        return response()->json($formattedContacts);
    }

    public function getMessages($userId)
    {
        $currentUserId = Auth::id();

        $conversation = Conversation::whereHas('participants', function ($q) use ($currentUserId) {
            $q->where('user_id', $currentUserId);
        })->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('type', 'private')->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'private']);
            ConversationParticipant::insert([
                ['conversation_id' => $conversation->id, 'user_id' => $currentUserId],
                ['conversation_id' => $conversation->id, 'user_id' => $userId],
            ]);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $messages = Message::where('conversation_id', $conversation->id)
            ->where(function ($query) use ($currentUserId) {
                $query->where(function ($sub) use ($currentUserId) {
                    $sub->where('sender_id', $currentUserId)->where('deleted_by_sender', false);
                })
                    ->orWhere(function ($sub) use ($currentUserId) {
                        $sub->where('sender_id', '!=', $currentUserId)->where('deleted_by_receiver', false);
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages
        ]);
    }
    public function sendMessage(Request $request)
    {
        $type = 'text';
        $content = $request->content ?? '';

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $type = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $path = $file->store('chat_files', 'public');
            $content = json_encode(['url' => '/storage/' . $path, 'name' => $file->getClientOriginalName()]);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'content' => $content,
            'type' => $type
        ]);

        Conversation::where('id', $request->conversation_id)->update(['last_message_at' => now()]);

        return response()->json($message);
    }

    public function recallMessage($messageId)
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id === Auth::id()) {
            if (now()->diffInMinutes($message->created_at) <= 60) {
                $message->is_recalled = true;
                $message->save();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Đã quá 60 phút'], 403);
        }
        return response()->json(['success' => false], 403);
    }

    public function deleteForMe($messageId)
    {
        $message = Message::find($messageId);
        if ($message) {
            if ($message->sender_id == Auth::id()) {
                $message->deleted_by_sender = true;
            } else {
                $message->deleted_by_receiver = true;
            }
            $message->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
