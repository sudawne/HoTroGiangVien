<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClassStudentsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentAccountCreated;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::where('department_id', 1)
            ->with(['advisor.user', 'monitor'])
            ->withCount('students')
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        // CHỈ LẤY GIẢNG VIÊN MÀ USER CHƯA BỊ XÓA MỀM
        $lecturers = Lecturer::whereHas('user', function ($query) {
            $query->whereNull('deleted_at'); // Đảm bảo user chưa bị xóa mềm
        })->with('user')->get();

        $department = Department::where('code', 'CNTT')->first();
        return view('admin.classes.create', compact('lecturers', 'department'));
    }

    public function previewUpload(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,xls|max:10240']);

        try {
            $data = Excel::toArray(new StudentsImport(0), $request->file('file'));
            $dataRows = $data[0] ?? [];
            $previewData = [];
            $hasError = false;

            foreach ($dataRows as $row) {
                if (!isset($row[1]) || empty(trim($row[1]))) continue;
                if (trim($row[1]) == 'Mã SV' || trim($row[1]) == 'MSSV') continue;

                $mssv = trim($row[1]);
                $name = trim($row[2]) . ' ' . trim($row[3]);
                $exists = Student::where('student_code', $mssv)->exists();

                if ($exists) $hasError = true;

                $previewData[] = [
                    'mssv' => $mssv,
                    'name' => $name,
                    'dob' => $row[5] ?? '',
                    'status' => $row[6] ?? '',
                    'is_duplicate' => $exists
                ];
            }

            $html = view('admin.classes.partials.preview_table', compact('previewData'))->render();

            return response()->json([
                'html' => $html,
                'hasError' => $hasError,
                'data' => $previewData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classes,code',
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
        ], [
            'code.required' => 'Vui lòng nhập Mã lớp.',
            'code.unique' => 'Mã lớp này đã tồn tại.',
            'name.required' => 'Vui lòng nhập Tên lớp.',
            'advisor_id.required' => 'Vui lòng chọn Cố vấn học tập.',
            'academic_year.required' => 'Vui lòng nhập Niên khóa.',
        ]);

        DB::beginTransaction();

        try {
            $class = new Classes();
            $class->code = $request->code;
            $class->name = $request->name;
            $class->department_id = $request->department_id ?? 1;
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            $newIds = $this->processStudentList($request->students_list, $class->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('admin.classes.index'),
                'new_student_ids' => $newIds
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:classes,code,' . $id,
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
        ], [
            'code.required' => 'Vui lòng nhập Mã lớp.',
            'code.unique' => 'Mã lớp này đã tồn tại.',
            'name.required' => 'Vui lòng nhập Tên lớp.',
            'advisor_id.required' => 'Vui lòng chọn Cố vấn học tập.',
            'academic_year.required' => 'Vui lòng nhập Niên khóa.',
        ]);

        DB::beginTransaction();

        try {
            $class = Classes::findOrFail($id);
            $class->code = $request->code;
            $class->name = $request->name;
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            $newIds = [];
            if ($request->filled('students_list')) {
                $newIds = $this->processStudentList($request->students_list, $class->id);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật lớp học thành công!',
                    'redirect_url' => null,
                    'new_student_ids' => $newIds
                ]);
            }

            return redirect()->route('admin.classes.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    private function processStudentList($jsonList, $classId)
    {
        $newIds = [];
        $studentsData = json_decode($jsonList, true);

        if (is_array($studentsData)) {
            foreach ($studentsData as $s) {
                $mssv = trim($s['mssv']);

                if (Student::where('student_code', $mssv)->exists()) continue;

                $parts = explode(' ', trim($s['name']));
                $firstName = array_pop($parts);
                $slugName = Str::slug($firstName, '');

                $user = User::create([
                    'name' => trim($s['name']),
                    'email' => $slugName . $mssv . '@vnkgu.edu.vn',
                    'username' => $mssv,
                    'password' => Hash::make($slugName . $mssv),
                    'role_id' => 3,
                    'is_active' => true,
                ]);

                $dob = null;
                if (!empty($s['dob']) && $s['dob'] !== '-') {
                    $parsedDate = strtotime(str_replace('/', '-', $s['dob']));
                    if ($parsedDate) $dob = date('Y-m-d', $parsedDate);
                }

                $rawStatus = mb_strtolower(trim($s['status'] ?? ''), 'UTF-8');
                $dbStatus = 'studying';
                if (str_contains($rawStatus, 'bảo lưu')) $dbStatus = 'reserved';
                elseif (str_contains($rawStatus, 'thôi học') || str_contains($rawStatus, 'nghỉ')) $dbStatus = 'dropped';
                elseif (str_contains($rawStatus, 'tốt nghiệp')) $dbStatus = 'graduated';

                $student = Student::create([
                    'user_id' => $user->id,
                    'class_id' => $classId,
                    'student_code' => $mssv,
                    'fullname' => trim($s['name']),
                    'dob' => $dob,
                    'status' => $dbStatus,
                    'enrollment_year' => now()->year,
                ]);

                $newIds[] = $student->id;
            }
        }
        return $newIds;
    }

    public function show(Request $request, string $id)
    {
        $class = Classes::with(['advisor.user', 'department'])
            ->withCount('students')
            ->findOrFail($id);

        $lecturers = Lecturer::with('user')->get();

        $allStudents = $class->students()->with('user')->orderBy('student_code', 'asc')->get();

        if ($request->has('search') && !empty($request->search)) {
            $keyword = $this->vn_to_str($request->search);
            $allStudents = $allStudents->filter(function ($student) use ($keyword) {
                $name = $this->vn_to_str($student->fullname);
                $code = $this->vn_to_str($student->student_code);
                return str_contains($name, $keyword) || str_contains($code, $keyword);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentResults = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $students = new LengthAwarePaginator(
            $currentResults,
            $allStudents->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.classes.partials.student_rows', compact('students'))->render(),
                'pagination' => (string) $students->links(),
                'total' => $class->students_count,
                'total_found' => $students->total()
            ]);
        }

        return view('admin.classes.show', compact('class', 'students', 'lecturers'));
    }

    public function edit(Request $request, string $id)
    {
        $class = Classes::findOrFail($id);

        // CHỈ LẤY GIẢNG VIÊN MÀ USER CHƯA BỊ XÓA MỀM
        $lecturers = Lecturer::whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        })->with('user')->get();

        $department = Department::where('code', 'CNTT')->first();

        $allStudents = $class->students()->with('user')->orderBy('student_code', 'asc')->get();
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $this->vn_to_str($request->search);
            $allStudents = $allStudents->filter(function ($student) use ($keyword) {
                $name = $this->vn_to_str($student->fullname);
                $code = $this->vn_to_str($student->student_code);
                return str_contains($name, $keyword) || str_contains($code, $keyword);
            });
        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $currentResults = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $students = new LengthAwarePaginator($currentResults, $allStudents->count(), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.classes.partials.student_rows', compact('students'))->render(),
                'pagination' => (string) $students->links(),
                'total' => $students->total()
            ]);
        }

        return view('admin.classes.edit', compact('class', 'lecturers', 'department', 'students'));
    }

    public function updateStudent(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'status' => 'required|in:studying,reserved,dropped,graduated',
        ], [
            'fullname.required' => 'Họ và tên không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        DB::beginTransaction();
        try {
            $student = Student::findOrFail($id);

            $student->update([
                'fullname' => $request->fullname,
                'dob' => $request->dob,
                'status' => $request->status,
            ]);

            if ($student->user_id) {
                $user = User::find($student->user_id);
                if ($user) {
                    $user->name = $request->fullname;
                    if ($request->filled('email')) {
                        $exists = User::where('email', $request->email)
                            ->where('id', '!=', $user->id)
                            ->exists();

                        if ($exists) {
                            throw ValidationException::withMessages([
                                'email' => ['Email này đã được sử dụng bởi tài khoản khác.']
                            ]);
                        }
                        $user->email = $request->email;
                    }
                    $user->save();
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thông tin thành công!'
                ]);
            }

            return redirect()->back()->with('success', 'Cập nhật thành công!');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Lỗi dữ liệu đầu vào',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    private function vn_to_str($str)
    {
        $str = $str ?? '';
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return strtolower(str_replace(' ', '', $str));
    }

    public function exportStudents($id)
    {
        $class = Classes::findOrFail($id);
        return Excel::download(new ClassStudentsExport($id), 'Danh_sach_lop_' . $class->code . '.xlsx');
    }

    public function destroy(string $id)
    {
        $class = Classes::findOrFail($id);
        if ($class->students()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa lớp này vì đang có sinh viên!');
        }
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Đã xóa lớp học!');
    }

    public function sendEmails(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $request->input('student_ids');
        $count = 0;
        $errors = 0;

        $students = Student::with('user')->whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            if ($student->user) {
                $parts = explode(' ', $student->fullname);
                $firstName = array_pop($parts);
                $slugName = Str::slug($firstName, '');
                $rawPassword = $slugName . $student->student_code;

                try {
                    Mail::to($student->user->email)->send(new StudentAccountCreated($student->fullname, $student->student_code, $rawPassword));
                    $count++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Error sending mail to {$student->user->email}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'sent_count' => $count,
            'error_count' => $errors
        ]);
    }
}
