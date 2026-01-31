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
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();

        return view('admin.classes.create', compact('lecturers', 'department'));
    }

    // --- HÀM PREVIEW UPLOAD GIỮ NGUYÊN ---
    public function previewUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240',
        ]);

        try {
            $data = Excel::toArray(new StudentsImport(0), $request->file('file'));
            $dataRows = $data[0] ?? [];
            $previewData = [];
            $hasError = false;

            foreach ($dataRows as $row) {
                // Bỏ qua dòng tiêu đề hoặc dòng trống
                if (!isset($row[1]) || empty(trim($row[1]))) continue;
                // Kiểm tra nếu dòng đó là header (STT, Mã SV...)
                if (trim($row[1]) == 'Mã SV' || trim($row[1]) == 'MSSV') continue;

                $mssv = trim($row[1]);
                $name = trim($row[2]) . ' ' . trim($row[3]);
                $exists = \App\Models\Student::where('student_code', $mssv)->exists();

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
                'hasError' => $hasError
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file: ' . $e->getMessage()], 500);
        }
    }

    // --- HÀM STORE CẦN SỬA ---
    public function store(Request $request)
    {
        // 1. SỬA VALIDATE: Chỉ validate thông tin LỚP, không validate thông tin SINH VIÊN
        $request->validate([
            'code' => 'required|unique:classes,code',
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
            'student_file' => 'nullable|mimes:xlsx,csv,xls|max:10240',
        ], [
            'code.required' => 'Vui lòng nhập Mã lớp.',
            'code.unique' => 'Mã lớp này đã tồn tại.',
            'name.required' => 'Vui lòng nhập Tên lớp.',
            'advisor_id.required' => 'Vui lòng chọn Cố vấn học tập.',
            'academic_year.required' => 'Vui lòng nhập Niên khóa.',
            'student_file.mimes' => 'File phải có định dạng .xlsx, .xls hoặc .csv.'
        ]);

        DB::beginTransaction();

        try {
            // 2. Tạo Lớp Mới
            $class = new Classes();
            $class->code = $request->code;
            $class->name = $request->name;
            $class->department_id = $request->department_id ?? 1; // Mặc định khoa 1 nếu ko có
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            $newIds = [];

            // 3. Import Sinh viên (Nếu có file)
            if ($request->hasFile('student_file')) {
                // Luôn set false để Server KHÔNG gửi mail (để Client JS gửi)
                $shouldSendEmail = false;

                $import = new StudentsImport($class->id, $shouldSendEmail);
                Excel::import($import, $request->file('student_file'));

                // Lấy danh sách ID vừa thêm để trả về cho JS
                if (property_exists($import, 'newStudentIds')) {
                    $newIds = $import->newStudentIds;
                }
            }

            DB::commit();

            // 4. Trả về JSON cho AJAX (để JS bên view create.blade.php xử lý tiếp việc gửi mail)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tạo lớp và import thành công!',
                    'redirect_url' => route('admin.classes.index'),
                    'new_student_ids' => $newIds // Mảng ID sinh viên mới
                ]);
            }

            return redirect()->route('admin.classes.index')->with('success', 'Tạo lớp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
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
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();

        // Logic lấy sinh viên tương tự show
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

    // Hàm update (Cập nhật thông tin LỚP + Import thêm SV)
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:classes,code,' . $id,
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
            'student_file' => 'nullable|mimes:xlsx,csv,xls|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $class = Classes::findOrFail($id);

            $class->update([
                'code' => $request->code,
                'name' => $request->name,
                'advisor_id' => $request->advisor_id,
                'academic_year' => $request->academic_year,
            ]);

            $newIds = [];

            if ($request->hasFile('student_file')) {
                $shouldSendEmail = false; // Luôn để false, JS lo gửi mail
                $import = new StudentsImport($class->id, $shouldSendEmail);
                Excel::import($import, $request->file('student_file'));

                if (property_exists($import, 'newStudentIds')) {
                    $newIds = $import->newStudentIds;
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật lớp thành công!',
                    'redirect_url' => route('admin.classes.edit', $id),
                    'new_student_ids' => $newIds
                ]);
            }

            return redirect()->route('admin.classes.edit', $id)->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
    }

    // Các hàm helper và sendEmails giữ nguyên
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
                // Tái tạo lại mật khẩu thô để gửi mail (Logic phải khớp với StudentController@store)
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
