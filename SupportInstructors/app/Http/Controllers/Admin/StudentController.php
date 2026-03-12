<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Student::with(['class', 'user' => function ($q) {
            $q->withTrashed();
        }])->withTrashed();

        if ($user->role_id == 2) {
            $lecturer = Lecturer::where('user_id', $user->id)->first();
            if ($lecturer) {
                $classIds = Classes::where('advisor_id', $lecturer->id)->pluck('id')->toArray();
                $query->whereIn('class_id', $classIds);
                $classes = Classes::where('advisor_id', $lecturer->id)->get();
            } else {
                $query->where('id', '<', 0); 
                $classes = collect();
            }
        } else {
            $classes = Classes::all();
        }
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = trim($request->search);
            $searchPattern = preg_replace('/\s+/', '%', $search);

            $query->where(function ($q) use ($search, $searchPattern) {
                $q->where('fullname', 'LIKE', "%{$searchPattern}%")
                    ->orWhere('student_code', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function show(string $id)
    {
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
                'academic_warnings.semester',
                'academic_results' => function ($q) {
                    $q->orderBy('semester_id', 'desc')->with('semester');
                },
                'consultation_logs.advisor.user',
                'consultation_logs.semester'
            ])
            ->findOrFail($id);
        if (Auth::user()->role_id == 2) {
            $lecturer = Lecturer::where('user_id', Auth::id())->first();
            $class = Classes::find($student->class_id);
            if (!$lecturer || !$class || $class->advisor_id != $lecturer->id) {
                abort(403, 'BẠN KHÔNG CÓ QUYỀN XEM THÔNG TIN SINH VIÊN NÀY.');
            }
        }

        $latestResult = $student->academic_results->first();
        return view('admin.students.show', compact('student', 'latestResult'));
    }

    public function create()
    {
        if (Auth::user()->role_id != 1) {
            abort(403, 'CHỈ ADMIN MỚI ĐƯỢC THÊM SINH VIÊN MỚI.');
        }
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thêm sinh viên.'], 403);
        }
        $request->validate([
            'student_code' => 'required|unique:students,student_code',
            'fullname'     => 'required|string|max:255',
            'class_id'     => 'required|exists:classes,id',
            'dob'          => 'nullable|date',
            'status'       => 'required|in:studying,reserved,dropped,graduated',
            'email'        => 'nullable|email|unique:users,email'
        ]);

        DB::beginTransaction();

        try {
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
        if (Auth::user()->role_id != 1) {
            abort(403, 'CHỈ ADMIN MỚI CÓ QUYỀN SỬA TOÀN BỘ HỒ SƠ SINH VIÊN.');
        }
        $student = Student::findOrFail($id);
        $classes = Classes::orderBy('code', 'asc')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền sửa.'], 403);
        }

        $student = Student::findOrFail($id);
        $userId = $student->user_id;

        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'dob' => 'nullable|date',
            'status' => 'required|in:studying,reserved,dropped,graduated',
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
                return response()->json(['success' => true, 'message' => 'Cập nhật thông tin thành công!']);
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

    public function destroy($id)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền Ẩn sinh viên.'], 403);
        }

        try {
            $student = Student::findOrFail($id);
            $userId = $student->user_id;

            $student->delete();

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

    public function restore($id)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền Khôi phục.'], 403);
        }

        try {
            $student = Student::withTrashed()->findOrFail($id);

            if ($student->user_id) {
                User::withTrashed()->where('id', $student->user_id)->restore();
            }

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

    public function bulkDestroy(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền.'], 403);
        }

        $request->validate(['ids' => 'required|array']);

        DB::beginTransaction();
        try {
            $ids = $request->ids;
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

    public function bulkRestore(Request $request)
    {
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền.'], 403);
        }

        $request->validate(['ids' => 'required|array']);

        DB::beginTransaction();
        try {
            $ids = $request->ids;
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
