<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationLike;
use App\Models\NotificationComment;
use App\Models\Classes;
use App\Models\User;
use App\Models\Student;
use App\Mail\StudentNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classes::orderBy('code', 'asc')->get();
        $query = Notification::with(['sender', 'classes'])->withCount(['likes', 'comments']);

        $roleFilter = $request->input('role_filter', 'all');
        if ($roleFilter === 'admin') {
            $query->whereHas('sender', function ($q) {
                $q->where('role_id', 1);
            });
        } elseif ($roleFilter === 'lecturer') {
            $query->whereHas('sender', function ($q) {
                $q->where('role_id', 2);
            });
        }

        $statusFilter = $request->input('status_filter', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('class_id')) {
            $query->where('target_audience', 'class')->whereHas('classes', function ($q) use ($request) {
                $q->where('classes.id', $request->class_id);
            });
        }

        $notifications = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.notifications.index', compact('notifications', 'classes', 'roleFilter', 'statusFilter'));
    }

    public function create()
    {
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.notifications.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,urgent',
            'target_audience' => 'required|in:all,class',
            'class_ids' => 'required_if:target_audience,class|array',
            'class_ids.*' => 'exists:classes,id',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('notifications_files', time() . '_' . $fileName, 'public');
        }

        $status = 'draft';
        if ($request->input('action') === 'send') {
            $status = (Auth::user()->role_id == 1) ? 'approved' : 'pending';
        }

        $allowComments = $request->has('allow_comments') ? true : false;

        $notification = Notification::create([
            'sender_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'status' => $status,
            'target_audience' => $request->target_audience,
            'attachment_url' => $filePath,
            'attachment_name' => $fileName,
            'allow_comments' => $allowComments,
        ]);

        // Lưu mảng các lớp vào bảng trung gian
        if ($request->target_audience === 'class' && $request->has('class_ids')) {
            $notification->classes()->sync($request->class_ids);
        }

        if ($status === 'approved') {
            $count = $this->sendNotificationEmails($notification);
            return redirect()->route('admin.notifications.index')->with('success', "Đã xuất bản và GỬI EMAIL thành công cho $count sinh viên!");
        }

        $msg = $status === 'draft' ? 'Đã lưu bản nháp (Chưa gửi)!' : 'Đã gửi yêu cầu đăng, chờ Admin duyệt!';
        return redirect()->route('admin.notifications.index')->with('success', $msg);
    }

    public function edit($id)
    {
        $notification = Notification::with('classes')->findOrFail($id);
        if ($notification->status === 'approved') {
            return redirect()->route('admin.notifications.index')->with('error', 'Không thể sửa thông báo đã xuất bản!');
        }
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.notifications.edit', compact('notification', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->status === 'approved') {
            return redirect()->back()->with('error', 'Không thể sửa thông báo đã xuất bản!');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,urgent',
            'target_audience' => 'required|in:all,class',
            'class_ids' => 'required_if:target_audience,class|array',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            if ($notification->attachment_url) {
                Storage::disk('public')->delete($notification->attachment_url);
            }
            $file = $request->file('attachment');
            $notification->attachment_name = $file->getClientOriginalName();
            $notification->attachment_url = $file->storeAs('notifications_files', time() . '_' . $notification->attachment_name, 'public');
        }

        $status = 'draft';
        if ($request->input('action') === 'send') {
            $status = (Auth::user()->role_id == 1) ? 'approved' : 'pending';
        }

        $allowComments = $request->has('allow_comments') ? true : false;

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'status' => $status,
            'target_audience' => $request->target_audience,
            'allow_comments' => $allowComments,
        ]);

        // Cập nhật lại danh sách lớp nhận thông báo
        if ($request->target_audience === 'class' && $request->has('class_ids')) {
            $notification->classes()->sync($request->class_ids);
        } else {
            $notification->classes()->detach(); // Nếu chọn Toàn trường thì xóa sạch các liên kết lớp
        }

        if ($status === 'approved') {
            $count = $this->sendNotificationEmails($notification);
            return redirect()->route('admin.notifications.index')->with('success', "Đã xuất bản và GỬI EMAIL thành công cho $count sinh viên!");
        }

        $msg = $status === 'draft' ? 'Đã cập nhật bản nháp!' : 'Đã gửi yêu cầu đăng, chờ Admin duyệt!';
        return redirect()->route('admin.notifications.index')->with('success', $msg);
    }

    public function approve($id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->status !== 'pending') {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ!');
        }

        $notification->update(['status' => 'approved']);
        $count = $this->sendNotificationEmails($notification);
        return redirect()->back()->with('success', "Đã duyệt bài và GỬI EMAIL thành công cho $count sinh viên!");
    }

    public function show($id)
    {
        $notification = Notification::with([
            'sender',
            'classes',
            'comments' => function ($q) {
                $q->whereNull('parent_id')
                    ->with(['user', 'replies.user', 'replies.parent.user']);
            }
        ])->withCount(['likes', 'comments'])->findOrFail($id);

        return view('admin.notifications.show', compact('notification'));
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->attachment_url) {
            Storage::disk('public')->delete($notification->attachment_url);
        }
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Đã xóa thông báo!');
    }

    public function toggleLike(Request $request, $id)
    {
        $userId = Auth::id();
        $like = NotificationLike::where('notification_id', $id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            NotificationLike::create(['notification_id' => $id, 'user_id' => $userId]);
            $liked = true;
        }

        $likesCount = NotificationLike::where('notification_id', $id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);
        }

        return back();
    }

    public function storeComment(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        if (!$notification->allow_comments) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bài viết này đã tắt tính năng bình luận.'], 422);
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

        $comment->load('user', 'parent');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'user_name' => $comment->user->name,
                    'user_initial' => substr($comment->user->name, 0, 1),
                    'content' => nl2br(e(trim($comment->content))),
                    'created_at' => $comment->created_at->diffForHumans(),
                    'parent_id' => $comment->parent_id,
                ],
                'comments_count' => NotificationComment::where('notification_id', $id)->count()
            ]);
        }

        return back()->with('success', 'Đã thêm bình luận!');
    }

    private function sendNotificationEmails($notification)
    {
        if ($notification->target_audience === 'all') {
            $realStudentEmails = User::where('role_id', 3)->whereNotNull('email')->pluck('email')->toArray();
        } else {
            // Lấy ID của tất cả các lớp đã chọn
            $classIds = $notification->classes->pluck('id')->toArray();
            $realStudentEmails = Student::whereIn('class_id', $classIds)
                ->whereHas('user', function ($q) {
                    $q->whereNotNull('email');
                })
                ->with('user')->get()->pluck('user.email')->toArray();
        }

        $countRealEmails = count($realStudentEmails);

        if ($countRealEmails > 0) {
            // EMAIL TEST - Thay bằng $realStudentEmails nếu hệ thống chạy thật
            $testEmails = ['nguyen22082006204@vnkgu.edu.vn'];

            try {
                Log::info("Đang tiến hành gửi mail thông báo ID: " . $notification->id);
                Mail::bcc($testEmails)->send(new StudentNotificationMail($notification));
            } catch (\Exception $e) {
                Log::error("LỖI GỬI MAIL NGHIÊM TRỌNG: " . $e->getMessage());
                session()->flash('warning', 'Đăng bài thành công nhưng GỬI MAIL THẤT BẠI.');
            }
        }

        return $countRealEmails;
    }

    public function markRead(Request $request)
    {
        if (Auth::check() && $request->filled('alert_id')) {
            $alertId = $request->input('alert_id');
            $userId = Auth::id();

            DB::table('user_read_alerts')->upsert([
                [
                    'user_id' => $userId,
                    'alert_id' => $alertId,
                    'read_at' => now()
                ]
            ], ['user_id', 'alert_id'], ['read_at']);
        }
        return response()->json(['success' => true]);
    }

    public function markReadAll(Request $request)
    {
        if (Auth::check() && $request->filled('alert_ids')) {
            $userId = Auth::id();
            $alertIds = $request->input('alert_ids');
            $data = [];

            foreach ($alertIds as $id) {
                $data[] = [
                    'user_id' => $userId,
                    'alert_id' => $id,
                    'read_at' => now()
                ];
            }

            if (!empty($data)) {
                DB::table('user_read_alerts')->upsert($data, ['user_id', 'alert_id'], ['read_at']);
            }
        }
        return response()->json(['success' => true]);
    }
}
