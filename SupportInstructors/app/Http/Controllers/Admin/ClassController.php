<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Lecturer;
// Import thêm thư viện Excel và Class Import
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    // ... index, create, edit giữ nguyên ...
    public function index()
    {
        // ... (Giữ nguyên code cũ của bạn)
        $classes = Classes::where('department_id', 1)
            ->with(['advisor.user', 'monitor'])
            ->withCount('students')
            ->orderBy('id', 'desc')
            ->paginate(9);
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        // ... (Giữ nguyên code cũ của bạn)
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();
        return view('admin.classes.create', compact('lecturers', 'department'));
    }

    public function edit(string $id)
    {
        // ... (Giữ nguyên code cũ của bạn)
        $class = Classes::findOrFail($id);
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();
        return view('admin.classes.edit', compact('class', 'lecturers', 'department'));
    }

    // --- CẬP NHẬT HÀM STORE ---
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:classes,code',
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
            'student_file' => 'nullable|mimes:xlsx,csv,xls|max:10240', // Validate file
        ]);

        DB::beginTransaction(); // Dùng transaction để rollback nếu lỗi import

        try {
            $class = new Classes();
            $class->code = $request->code;
            $class->name = $request->name;
            $class->department_id = 1;
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            // Nếu có file thì tiến hành import
            if ($request->hasFile('student_file')) {
                Excel::import(new StudentsImport($class->id), $request->file('student_file'));
            }

            DB::commit(); // Lưu vào DB nếu mọi thứ OK
            return redirect()->route('admin.classes.index')->with('success', 'Thêm lớp và danh sách sinh viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack(); // Hủy bỏ tạo lớp nếu import lỗi
            return redirect()->back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // --- CẬP NHẬT HÀM UPDATE ---
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:classes,code,' . $id,
            'name' => 'required',
            'advisor_id' => 'required|exists:lecturers,id',
            'academic_year' => 'required',
            'student_file' => 'nullable|mimes:xlsx,csv,xls|max:10240',
        ]);

        try {
            $class = Classes::findOrFail($id);
            $class->update([
                'code' => $request->code,
                'name' => $request->name,
                'advisor_id' => $request->advisor_id,
                'academic_year' => $request->academic_year,
            ]);

            // Nếu có file thì import thêm vào lớp này
            if ($request->hasFile('student_file')) {
                Excel::import(new StudentsImport($class->id), $request->file('student_file'));
                return redirect()->route('admin.classes.index')->with('success', 'Cập nhật lớp và import thêm sinh viên thành công!');
            }

            return redirect()->route('admin.classes.index')->with('success', 'Cập nhật thông tin lớp thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Lỗi Import: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        // ... (Giữ nguyên code cũ của bạn)
        $class = Classes::findOrFail($id);
        if ($class->students()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa lớp này vì đang có sinh viên!');
        }
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Đã xóa lớp học!');
    }

    // app/Http/Controllers/Admin/ClassController.php

    public function show(string $id)
    {
        // Lấy thông tin lớp
        $class = Classes::with(['advisor.user', 'department'])->findOrFail($id);

        // Lấy danh sách sinh viên của lớp đó (để hiển thị bảng trong view show)
        $students = $class->students()->orderBy('student_code', 'asc')->paginate(20);

        return view('admin.classes.show', compact('class', 'students'));
    }
}
