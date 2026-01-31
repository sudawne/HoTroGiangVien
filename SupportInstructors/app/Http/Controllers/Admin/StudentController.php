<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('class');

        // Lọc theo lớp
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15);
        $classes = Classes::all(); // Lấy danh sách lớp để fill vào dropdown lọc

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function show(string $id)
    {
        // Eager Load: Lấy Sinh viên kèm theo User, Lớp, Người thân, Nợ môn
        $student = Student::with([
            'user',
            'class',
            'relatives',
            'debts' => function ($q) {
                $q->where('status', 'owed'); // Chỉ lấy môn đang nợ cho Tab Học vụ
            }
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    // Các hàm create, store, edit... giữ nguyên hoặc code sau
    public function create()
    {
        return view('admin.students.create');
    }
    public function store(Request $request)
    {
        return redirect()->route('admin.students.index');
    }
    public function edit(string $id)
    {
        return view('admin.students.edit');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255', // Email có thể null nếu chưa cấp
            'dob' => 'nullable|date',
            'status' => 'required|in:studying,reserved,dropped,graduated',
        ]);

        DB::beginTransaction();
        try {
            $student = Student::findOrFail($id);

            // 1. Cập nhật bảng students
            $student->update([
                'fullname' => $request->fullname,
                'dob' => $request->dob,
                'status' => $request->status,
            ]);

            // 2. Cập nhật bảng users (nếu có thay đổi tên hoặc email)
            if ($student->user_id) {
                $user = User::find($student->user_id);
                if ($user) {
                    $user->name = $request->fullname;
                    if ($request->filled('email')) {
                        // Kiểm tra email trùng lặp (trừ chính user này)
                        $exists = User::where('email', $request->email)->where('id', '!=', $user->id)->exists();
                        if (!$exists) {
                            $user->email = $request->email;
                        }
                    }
                    $user->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật thông tin sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // Xóa sinh viên khỏi hệ thống
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $userId = $student->user_id;

            // Xóa sinh viên
            $student->delete();

            // Tùy chọn: Xóa luôn User account liên kết để sạch dữ liệu
            if ($userId) {
                User::where('id', $userId)->delete();
            }

            return redirect()->back()->with('success', 'Đã xóa sinh viên khỏi lớp!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}
