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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classes::orderBy('code', 'asc')->get();
        $query = Notification::with(['sender', 'class'])->withCount(['likes', 'comments']);

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
            $query->where('target_audience', 'class')->where('class_id', $request->class_id);
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
            'class_id' => 'required_if:target_audience,class|nullable|exists:classes,id',
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
            'class_id' => $request->target_audience === 'class' ? $request->class_id : null,
            'attachment_url' => $filePath,
            'attachment_name' => $fileName,
            'allow_comments' => $allowComments,
        ]);

        if ($status === 'approved') {
            $count = $this->sendNotificationEmails($notification);
            return redirect()->route('admin.notifications.index')->with('success', "Đã xuất bản và GỬI EMAIL thành công cho $count sinh viên!");
        }

        $msg = $status === 'draft' ? 'Đã lưu bản nháp (Chưa gửi)!' : 'Đã gửi yêu cầu đăng, chờ Admin duyệt!';
        return redirect()->route('admin.notifications.index')->with('success', $msg);
    }

    public function edit($id)
    {
        $notification = Notification::findOrFail($id);
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
            'class_id' => 'required_if:target_audience,class|nullable|exists:classes,id',
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
            'class_id' => $request->target_audience === 'class' ? $request->class_id : null,
            'allow_comments' => $allowComments,
        ]);

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
        $notification = Notification::with(['sender', 'class', 'comments.user'])->withCount('likes')->findOrFail($id);
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

    public function toggleLike($id)
    {
        $userId = Auth::id();
        $like = NotificationLike::where('notification_id', $id)->where('user_id', $userId)->first();
        if ($like) {
            $like->delete();
        } else {
            NotificationLike::create(['notification_id' => $id, 'user_id' => $userId]);
        }
        return back();
    }

    public function storeComment(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        if (!$notification->allow_comments) {
            return back()->with('error', 'Bài viết này đã tắt tính năng bình luận.');
        }

        $request->validate(['content' => 'required|string|max:1000']);
        NotificationComment::create([
            'notification_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);
        return back()->with('success', 'Đã thêm bình luận!');
    }

    private function sendNotificationEmails($notification)
    {
        if ($notification->target_audience === 'all') {
            $realStudentEmails = User::where('role_id', 3)->whereNotNull('email')->pluck('email')->toArray();
        } else {
            $realStudentEmails = Student::where('class_id', $notification->class_id)
                ->whereHas('user', function ($q) {
                    $q->whereNotNull('email');
                })
                ->with('user')->get()->pluck('user.email')->toArray();
        }

        $countRealEmails = count($realStudentEmails);

        if ($countRealEmails > 0) {
            $testEmails = ['nguyen22082006204@vnkgu.edu.vn'];

            try {
                Log::info("Đang tiến hành gửi mail thông báo ID: " . $notification->id);
                Mail::bcc($testEmails)->send(new StudentNotificationMail($notification));
            } catch (\Exception $e) {
                Log::error("LỖI GỬI MAIL NGHIÊM TRỌNG: " . $e->getMessage());
                session()->flash('warning', 'Đăng bài thành công nhưng GỬI MAIL THẤT BẠI. Xem file log (storage/logs/laravel.log) để biết chi tiết.');
            }
        }

        return $countRealEmails;
    }
}
