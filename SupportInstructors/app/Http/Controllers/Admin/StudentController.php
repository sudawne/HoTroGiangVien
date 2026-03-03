<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // THÊM: withTrashed() để lấy cả sinh viên đã ẩn
        // THÊM: user => withTrashed() để lấy thông tin user dù user đó đã bị ẩn
        $query = Student::with(['class', 'user' => function ($q) {
            $q->withTrashed();
        }])->withTrashed();

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

        $students = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $classes = Classes::all();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function show(string $id)
    {
        // Lấy sinh viên (kể cả đã ẩn) và eager load các quan hệ cần thiết
        $student = Student::withTrashed()
            ->with([
                'user' => function ($q) {
                    $q->withTrashed();
                },
                'class',
                'relatives',
                'debts' => function ($q) {
                    $q->where('status', 'owed');
                },
                'academic_warnings.semester', // Cảnh báo học vụ kèm kỳ học
                'academic_results' => function ($q) {
                    $q->orderBy('semester_id', 'desc')->with('semester'); // Bảng điểm mới nhất lên đầu
                },
                'consultation_logs.advisor.user', // Lịch sử tư vấn kèm người tư vấn
                'consultation_logs.semester'
            ])
            ->findOrFail($id);

        // Lấy kết quả học tập mới nhất để tính toán các chỉ số trên cùng
        $latestResult = $student->academic_results->first();

        return view('admin.students.show', compact('student', 'latestResult'));
    }

    public function create()
    {
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'student_code' => 'required|unique:students,student_code',
            'fullname'     => 'required|string|max:255',
            'class_id'     => 'required|exists:classes,id',
            'dob'          => 'nullable|date',
            'status'       => 'required|in:studying,reserved,dropped,graduated',
            'email'        => 'nullable|email|unique:users,email'
        ], [
            'required' => ':attribute không được để trống.',
            'unique'   => ':attribute đã tồn tại trên hệ thống.',
            'exists'   => ':attribute không hợp lệ.',
            'date'     => ':attribute không đúng định dạng ngày tháng.',
            'in'       => ':attribute chọn không đúng danh mục.',
            'email'    => ':attribute phải là một địa chỉ email hợp lệ.',
            'max'      => ':attribute không được vượt quá :max ký tự.',
        ], [
            'student_code' => 'Mã sinh viên',
            'fullname'     => 'Họ và tên',
            'class_id'     => 'Lớp học',
            'dob'          => 'Ngày sinh',
            'status'       => 'Trạng thái',
            'email'        => 'Địa chỉ email',
        ]);

        DB::beginTransaction();

        try {
            // 2. Logic tạo Email
            $parts = explode(' ', trim($request->fullname));
            $firstName = array_pop($parts);
            $slugName = Str::slug($firstName, '');

            $username = $request->student_code;
            $rawPassword = $slugName . $request->student_code;

            $emailPrefix = strtolower($slugName . $request->student_code);

            if ($request->filled('email')) {
                $email = trim($request->email);
            } else {
                $email = $emailPrefix . '@vnkgu.edu.vn';
                if (User::where('email', $email)->exists()) {
                    $email = $emailPrefix . rand(10, 99) . '@vnkgu.edu.vn';
                }
            }

            $user = User::create([
                'name' => $request->fullname,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($rawPassword),
                'role_id' => 3,
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

            if ($request->ajax()) {
                $student->load(['user', 'class']);
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
                return response()->json(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $userId = $student->user_id;

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'dob' => 'nullable|date',
            'status' => 'required|in:studying,reserved,dropped,graduated',
        ], [
            'email.unique' => 'Email này đã tồn tại trong hệ thống. Vui lòng nhập email khác.',
        ]);

        DB::beginTransaction();
        try {
            $student->update([
                'fullname' => $request->fullname,
                'dob' => $request->dob,
                'status' => $request->status,
            ]);

            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->name = $request->fullname;
                    if ($request->filled('email')) {
                        $user->email = $request->email;
                    }
                    $user->save();
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thông tin sinh viên thành công!'
                ]);
            }

            return redirect()->back()->with('success', 'Cập nhật thông tin sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa mềm (Ẩn) một sinh viên
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $userId = $student->user_id;

            // Xóa mềm Student
            $student->delete();

            // Xóa mềm User liên quan (nếu có)
            if ($userId) {
                User::where('id', $userId)->delete();
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Đã ẩn sinh viên thành công!']);
            }

            return redirect()->back()->with('success', 'Đã ẩn sinh viên!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Khôi phục một sinh viên
     */
    public function restore($id)
    {
        try {
            // Tìm cả trong thùng rác
            $student = Student::withTrashed()->findOrFail($id);

            // Khôi phục User trước
            if ($student->user_id) {
                User::withTrashed()->where('id', $student->user_id)->restore();
            }

            // Khôi phục Student
            $student->restore();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Đã khôi phục sinh viên thành công!']);
            }
            return redirect()->back()->with('success', 'Đã khôi phục sinh viên!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa nhiều (Ẩn nhiều)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        DB::beginTransaction();
        try {
            $ids = $request->ids;

            // Lấy danh sách user_id để xóa
            $userIds = Student::whereIn('id', $ids)->pluck('user_id')->filter()->toArray();

            Student::whereIn('id', $ids)->delete();

            if (!empty($userIds)) {
                User::whereIn('id', $userIds)->delete();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã ẩn ' . count($ids) . ' sinh viên.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Khôi phục nhiều
     */
    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        DB::beginTransaction();
        try {
            $ids = $request->ids;

            // Lấy danh sách user_id đã xóa để khôi phục
            $students = Student::withTrashed()->whereIn('id', $ids)->get();
            $userIds = $students->pluck('user_id')->filter()->toArray();

            Student::withTrashed()->whereIn('id', $ids)->restore();

            if (!empty($userIds)) {
                User::withTrashed()->whereIn('id', $userIds)->restore();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã khôi phục ' . count($ids) . ' sinh viên.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
