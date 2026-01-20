<?php

namespace App\Http\Controllers\Admin;

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

    public function show(string $id)
    {
        $class = Classes::with(['advisor.user', 'department'])->findOrFail($id);
        $students = $class->students()->orderBy('student_code', 'asc')->paginate(20);

        return view('admin.classes.show', compact('class', 'students'));
    }

    public function edit(string $id)
    {
        $class = Classes::findOrFail($id);
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();

        $students = $class->students()->with('user')->orderBy('student_code', 'asc')->get();

        return view('admin.classes.edit', compact('class', 'lecturers', 'department', 'students'));
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

        foreach ($studentIds as $id) {
            $student = Student::with('user')->find($id);
            if ($student && $student->user) {
                $parts = explode(' ', $student->fullname);
                $firstName = array_pop($parts);
                $slugName = \Illuminate\Support\Str::slug($firstName, '');
                $rawPassword = $slugName . $student->student_code;

                try {
                    Mail::to($student->user->email)->send(new StudentAccountCreated($student->fullname, $student->student_code, $rawPassword));
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$student->user->email}: " . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => "Đã gửi email thành công cho $count sinh viên!"]);
    }
}
