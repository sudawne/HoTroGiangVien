<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationLike;
use App\Models\NotificationComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student ?? null;
        $filter = $request->get('filter', 'all');

        $query = Notification::with([
            'sender',
            'comments' => function ($q) {
                // Sắp xếp comment tăng dần (cũ nằm trên, mới nằm dưới)
                $q->whereNull('parent_id')
                    ->with(['user', 'replies' => function ($rq) {
                        $rq->orderBy('created_at', 'asc')->with(['user', 'parent.user']);
                    }])
                    ->orderBy('created_at', 'asc');
            },
            'likes'
        ])
            ->withCount(['likes', 'comments'])
            ->where('status', 'approved')
            ->where(function ($q) use ($student) {
                $q->where('target_audience', 'all');
                if ($student && $student->class_id) {
                    $q->orWhere(function ($sub) use ($student) {
                        $sub->where('target_audience', 'class')
                            ->where('class_id', $student->class_id);
                    });
                }
            });

        if ($filter === 'urgent') {
            $query->where('type', 'urgent');
        } elseif ($filter === 'warning') {
            $query->where('type', 'warning');
        } elseif ($filter === 'info') {
            $query->where('type', 'info');
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        return view('student.index', compact('notifications', 'filter'));
    }

    public function toggleLike($id)
    {
        $userId = Auth::id();
        $like = NotificationLike::where('notification_id', $id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
        } else {
            NotificationLike::create(['notification_id' => $id, 'user_id' => $userId]);
        }

        if (request()->wantsJson()) {
            $count = NotificationLike::where('notification_id', $id)->count();
            return response()->json(['success' => true, 'likes_count' => $count]);
        }

        return back();
    }

    public function storeComment(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        if (!$notification->allow_comments) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Bình luận bị tắt'], 403);
            }
            return back()->with('error', 'Bài viết này đã tắt tính năng bình luận.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:notification_comments,id',
            'content_prefix' => 'nullable|string'
        ]);

        $finalContent = $request->content;
        if ($request->filled('content_prefix')) {
            $finalContent = $request->content_prefix . $finalContent;
        }

        $comment = NotificationComment::create([
            'notification_id' => $id,
            'user_id' => Auth::id(),
            'content' => $finalContent,
            'parent_id' => $request->parent_id
        ]);

        // Load để lấy đúng relation cho Response JSON
        $comment->load('user', 'parent.user');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => nl2br(e(trim($comment->content))),
                    'user_name' => $comment->user->name,
                    'user_initial' => mb_substr($comment->user->name, 0, 1),
                    'user_role' => $comment->user->role_id,
                    'parent_id' => $comment->parent_id,
                    'parent_user_name' => $comment->parent && $comment->parent->user ? $comment->parent->user->name : null,
                    'created_at_human' => $comment->created_at->diffForHumans(),
                ]
            ], 201);
        }

        return back()->with('success', 'Đã thêm bình luận!');
    }
}
