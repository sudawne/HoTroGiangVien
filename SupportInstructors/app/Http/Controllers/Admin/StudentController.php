<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $student = Student::with(['user', 'class', 'relatives', 'debts' => function ($q) {
            $q->where('status', 'owed');
        }])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'student_code' => 'required|unique:students,student_code',
            'fullname' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'dob' => 'nullable|date',
            'status' => 'required|in:studying,reserved,dropped,graduated',
        ]);

        DB::beginTransaction();

        try {
            // 2. Xử lý Logic tạo Email và Mật khẩu tự động
            // Tách tên để lấy chữ cái cuối (VD: "Nguyễn Thị Lan" -> lấy "Lan")
            $parts = explode(' ', trim($request->fullname));
            $firstName = array_pop($parts);
            $slugName = Str::slug($firstName, ''); // Chuyển thành "lan"

            // Username đăng nhập: Dùng MSSV (VD: 20110001)
            $username = $request->student_code;

            // Password mặc định: Tên + MSSV (VD: lan20110001)
            $rawPassword = $slugName . $request->student_code;

            // Email: Tên + MSSV + Domain (VD: lan20110001@vnkgu.edu.vn)
            // Nếu trên form có nhập email thì lấy, không thì tự sinh
            $emailPrefix = $slugName . $request->student_code;
            $email = $request->input('email', $emailPrefix . '@vnkgu.edu.vn');

            $user = User::create([
                'name' => $request->fullname,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($rawPassword),
                'role_id' => 3, // Role Student
                'is_active' => true,
            ]);

            // 3. Tạo Student
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
                'student_code' => $request->student_code,
                'fullname' => $request->fullname,
                'dob' => $request->dob,
                'status' => $request->status,
                'enrollment_year' => now()->year,
            ]);

            DB::commit();

            // 4. Trả về JSON cho AJAX
            if ($request->ajax()) {
                $html = view('admin.classes.partials.student_rows', ['students' => collect([$student])])->render();

                return response()->json([
                    'success' => true,
                    'message' => 'Thêm sinh viên thành công!',
                    'html' => $html,
                    'new_id' => $student->id
                ]);
            }

            return redirect()->back()->with('success', 'Thêm sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        return view('admin.students.edit');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
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

            // 2. Cập nhật bảng users
            if ($student->user_id) {
                $user = User::find($student->user_id);
                if ($user) {
                    $user->name = $request->fullname;
                    if ($request->filled('email')) {
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

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $userId = $student->user_id;

            // Xóa mềm sinh viên
            $student->delete();

            // Xóa mềm user tương ứng
            if ($userId) {
                User::where('id', $userId)->delete();
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Đã xóa sinh viên vào thùng rác!']);
            }

            return redirect()->back()->with('success', 'Đã xóa sinh viên vào thùng rác!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // --- 2. XÓA NHIỀU SINH VIÊN (Soft Delete) ---
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:students,id',
        ]);

        $ids = $request->ids;

        DB::beginTransaction();
        try {
            // Lấy danh sách user_id liên quan để xóa account
            $userIds = Student::whereIn('id', $ids)->pluck('user_id')->filter()->toArray();

            // Xóa mềm Students
            Student::whereIn('id', $ids)->delete();

            // Xóa mềm Users
            if (!empty($userIds)) {
                User::whereIn('id', $userIds)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($ids) . ' sinh viên vào thùng rác.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
