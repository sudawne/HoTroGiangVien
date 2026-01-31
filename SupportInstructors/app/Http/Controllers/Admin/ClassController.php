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
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

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

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classes,code',
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
            'student_file' => 'nullable|mimes:xlsx,csv,xls|max:10240',
        ], [
            'code.required' => 'Vui lòng nhập mã lớp.',
            'code.unique' => 'Mã lớp này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên lớp.',
            'advisor_id.required' => 'Vui lòng chọn cố vấn.',
            'academic_year.required' => 'Vui lòng nhập niên khóa.',
            'student_file.mimes' => 'File phải có định dạng .xlsx, .xls hoặc .csv.',
            'student_file.max' => 'File không được quá 10MB.',
        ]);

        DB::beginTransaction();

        try {
            $class = new Classes();
            $class->code = $request->code;
            $class->name = $request->name;
            $class->department_id = 1;
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            if ($request->hasFile('student_file')) {
                $shouldSendEmail = $request->boolean('send_email');
                Excel::import(new StudentsImport($class->id, $shouldSendEmail), $request->file('student_file'));
            }

            DB::commit();
            return redirect()->route('admin.classes.index')->with('success', 'Tạo lớp và import danh sách thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
    }

    public function show(Request $request, string $id)
    {
        // 1. Load thông tin lớp và tổng sĩ số
        $class = Classes::with(['advisor.user', 'department'])
            ->withCount('students') // Lấy sĩ số gốc
            ->findOrFail($id);

        // 2. Lấy TOÀN BỘ sinh viên trong lớp ra trước (get() thay vì paginate())
        $allStudents = $class->students()->with('user')->orderBy('student_code', 'asc')->get();

        // 3. Lọc bằng PHP (Convert cả 2 về không dấu rồi so sánh)
        if ($request->has('search') && !empty($request->search)) {
            // Chuyển từ khóa tìm kiếm về dạng không dấu, thường (vd: "Đình" -> "dinh")
            $keyword = $this->vn_to_str($request->search);

            $allStudents = $allStudents->filter(function ($student) use ($keyword) {
                // Chuyển tên và MSSV trong Database về dạng không dấu
                $name = $this->vn_to_str($student->fullname);
                $code = $this->vn_to_str($student->student_code);

                // So sánh: chỉ cần tên hoặc MSSV chứa từ khóa là giữ lại
                return str_contains($name, $keyword) || str_contains($code, $keyword);
            });
        }

        // 4. Phân trang thủ công (Manual Pagination) cho danh sách đã lọc
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20; // Số dòng trên 1 trang
        $currentResults = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $students = new LengthAwarePaginator(
            $currentResults,
            $allStudents->count(), // Tổng số bản ghi SAU KHI LỌC
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // 5. Trả về JSON nếu là Ajax (Tìm kiếm Live)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.classes.partials.student_rows', compact('students'))->render(),
                'pagination' => (string) $students->links(),
                'total' => $class->students_count, // Sĩ số lớp cố định
                'total_found' => $students->total() // Số lượng tìm thấy (để hiển thị trên thanh tìm kiếm)
            ]);
        }

        return view('admin.classes.show', compact('class', 'students'));
    }

    public function edit(Request $request, string $id)
    {
        $class = Classes::findOrFail($id);
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();

        // 1. Lấy toàn bộ sinh viên của lớp (Kèm user để lấy email)
        $allStudents = $class->students()->with('user')->orderBy('student_code', 'asc')->get();

        // 2. Xử lý Tìm kiếm (PHP Filter - Chính xác tuyệt đối về dấu/hoa thường)
        if ($request->has('search') && !empty($request->search)) {
            $keyword = $this->vn_to_str($request->search); // Chuyển từ khóa về không dấu, chữ thường

            $allStudents = $allStudents->filter(function ($student) use ($keyword) {
                // Chuyển tên và MSSV trong DB về không dấu, chữ thường
                $name = $this->vn_to_str($student->fullname);
                $code = $this->vn_to_str($student->student_code);

                // So sánh: Chỉ cần chứa từ khóa là lấy
                return str_contains($name, $keyword) || str_contains($code, $keyword);
            });
        }

        // 3. Phân trang thủ công (Manual Pagination) cho Collection sau khi lọc
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50; // Số lượng hiển thị 1 trang
        $currentResults = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $students = new LengthAwarePaginator(
            $currentResults,
            $allStudents->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Nếu là request AJAX (khi gõ phím), chỉ trả về phần bảng và phân trang
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.classes.partials.student_rows', compact('students'))->render(),
                'pagination' => (string) $students->links(),
                'total' => $students->total()
            ]);
        }

        return view('admin.classes.edit', compact('class', 'lecturers', 'department', 'students'));
    }

    // Hàm hỗ trợ chuyển Tiếng Việt có dấu thành không dấu (Helper)
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
        return strtolower(str_replace(' ', '', $str)); // Xóa khoảng trắng để tìm kiếm linh hoạt hơn
    }

    public function exportStudents($id)
    {
        $class = Classes::findOrFail($id);
        return Excel::download(new ClassStudentsExport($id), 'Danh_sach_lop_' . $class->code . '.xlsx');
    }

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

            if ($request->hasFile('student_file')) {
                $shouldSendEmail = $request->boolean('send_email');

                // Khởi tạo đối tượng import
                $import = new StudentsImport($class->id, $shouldSendEmail);
                Excel::import($import, $request->file('student_file'));

                DB::commit();

                // Lấy danh sách ID mới thêm
                $newIds = $import->newStudentIds;

                // Redirect về trang Edit của lớp đó, kèm theo flash data
                return redirect()->route('admin.classes.edit', $id)
                    ->with('success', 'Cập nhật lớp và thêm sinh viên thành công!')
                    ->with('new_student_ids', $newIds);
            }

            DB::commit();
            return redirect()->route('admin.classes.edit', $id)->with('success', 'Cập nhật thông tin lớp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
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

        // Vì đã dùng Queue trong Mailable, vòng lặp này sẽ chạy rất nhanh
        // Nó chỉ đẩy job vào hàng đợi chứ không đợi gửi mail xong mới chạy tiếp
        $students = Student::with('user')->whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            if ($student->user) {
                $parts = explode(' ', $student->fullname);
                $firstName = array_pop($parts);
                $slugName = \Illuminate\Support\Str::slug($firstName, '');
                // Lưu ý: Logic password này nên đồng nhất với lúc Import
                $rawPassword = $slugName . $student->student_code;

                try {
                    Mail::to($student->user->email)->send(new StudentAccountCreated($student->fullname, $student->student_code, $rawPassword));
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to queue email for {$student->user->email}: " . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => "Đã thêm $count email vào hàng đợi gửi đi!"]);
    }
}
