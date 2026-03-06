<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationLike;
use App\Models\NotificationComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student ?? null;

        $filter = $request->get('filter', 'all');
        $timeFilter = $request->get('time', 'all');
        $search = $request->get('search', '');

        $query = Notification::with([
            'sender',
            'classes', // Load kèm mảng các lớp để View không bị lỗi
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
                // 1. Lấy thông báo gửi toàn trường
                $q->where('target_audience', 'all');

                // 2. Hoặc lấy thông báo gửi riêng cho lớp của sinh viên này
                if ($student && $student->class_id) {
                    $q->orWhere(function ($sub) use ($student) {
                        $sub->where('target_audience', 'class')
                            // Cập nhật logic: Kiểm tra lớp thông qua quan hệ bảng trung gian
                            ->whereHas('classes', function ($classQuery) use ($student) {
                                $classQuery->where('classes.id', $student->class_id);
                            });
                    });
                }
            });

        // Xử lý Search bằng chữ
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('message', 'LIKE', '%' . $search . '%')
                    ->orWhere('attachment_name', 'LIKE', '%' . $search . '%');
            });
        }

        // Xử lý Filter loại thông báo
        if ($filter === 'urgent') {
            $query->where('type', 'urgent');
        } elseif ($filter === 'warning') {
            $query->where('type', 'warning');
        } elseif ($filter === 'info') {
            $query->where('type', 'info');
        }

        // Xử lý Filter thời gian
        if ($timeFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($timeFilter === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($timeFilter === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        return view('student.index', compact('notifications', 'filter', 'timeFilter', 'search'));
    }

    // API TRẢ VỀ DỮ LIỆU TÌM KIẾM NHANH (LIVE SEARCH)
    public function searchApi(Request $request)
    {
        $query = trim($request->get('q'));
        if (empty($query)) {
            return response()->json([]);
        }

        $user = Auth::user();
        $student = $user->student ?? null;
        $classId = $student ? $student->class_id : null;

        $notifications = Notification::with('sender')
            ->where('status', 'approved')
            ->where(function ($q) use ($classId) {
                $q->where('target_audience', 'all');
                if ($classId) {
                    $q->orWhere(function ($sub) use ($classId) {
                        $sub->where('target_audience', 'class')->whereHas('classes', function ($classQuery) use ($classId) {
                            $classQuery->where('classes.id', $classId);
                        });
                    });
                }
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('message', 'LIKE', "%{$query}%")
                    ->orWhere('attachment_name', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->take(8) // Lấy tối đa 8 kết quả để popup không bị lag
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'snippet' => \Illuminate\Support\Str::limit(strip_tags($item->message), 60),
                    'file' => $item->attachment_name,
                    'sender' => $item->sender->name ?? 'Hệ thống',
                    'time' => $item->created_at->diffForHumans(),
                    'url' => url('/student/?filter=all#notification-' . $item->id)
                ];
            });

        return response()->json($notifications);
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
