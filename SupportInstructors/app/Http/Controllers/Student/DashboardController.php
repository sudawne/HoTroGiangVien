<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationLike;
use App\Models\NotificationComment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy thông tin sinh viên từ user đang đăng nhập
        $student = Student::where('user_id', $user->id)->first();
        $classId = $student ? $student->class_id : null;

        // Truy vấn thông báo: Chỉ lấy 'approved' VÀ (gửi 'all' HOẶC gửi cho 'class_id' của sinh viên này)
        $notifications = Notification::with(['sender', 'class', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->where('status', 'approved')
            ->where(function ($query) use ($classId) {
                $query->where('target_audience', 'all');

                if ($classId) {
                    $query->orWhere(function ($q) use ($classId) {
                        $q->where('target_audience', 'class')
                            ->where('class_id', $classId);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Mỗi trang 10 bài viết

        return view('student.dashboard', compact('notifications', 'student'));
    }

    // Hàm xử lý Like cho sinh viên
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

    // Hàm xử lý Comment cho sinh viên
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
}
