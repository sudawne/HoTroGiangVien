<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Lecturer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
     */
    public function index()
    {
        // Lấy danh sách lớp thuộc khoa CNTT (id=1), sắp xếp mới nhất
        $classes = Classes::where('department_id', 1)
            ->with(['advisor.user', 'monitor']) // Eager load thông tin GV và Lớp trưởng
            ->withCount('students') // Đếm số sinh viên
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Hiển thị form tạo lớp mới
     */
    public function create()
    {
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first(); // Mặc định khoa CNTT

        return view('admin.classes.create', compact('lecturers', 'department'));
    }

    public function previewUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240',
        ]);

        try {
            // 1. Đọc dữ liệu từ file
            // Class StudentsImport đã có startRow() = 8, nên $data lấy được đã tự động bắt đầu từ dòng 8 rồi.
            $data = Excel::toArray(new StudentsImport(0), $request->file('file'));

            // Lấy Sheet đầu tiên. KHÔNG DÙNG array_slice nữa.
            $dataRows = $data[0] ?? [];

            $previewData = [];
            $hasError = false;

            foreach ($dataRows as $row) { // Bỏ $index ở đây để tránh nhầm lẫn
                // Bỏ qua dòng trống hoặc không có mã SV (cột 1)
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

                $mssv = trim($row[1]);
                // Cột 2 là Họ, Cột 3 là Tên -> Ghép lại
                $name = trim($row[2]) . ' ' . trim($row[3]);

                // Kiểm tra trùng mã SV
                $exists = \App\Models\Student::where('student_code', $mssv)->exists();

                if ($exists) $hasError = true;

                $previewData[] = [
                    'mssv' => $mssv,
                    'name' => $name,
                    'dob' => $row[5] ?? '',    // Cột Ngày sinh
                    'status' => $row[6] ?? '', // Cột Tình trạng
                    'is_duplicate' => $exists
                ];
            }

            // Trả về HTML
            $html = view('admin.classes.partials.preview-table', compact('previewData'))->render();

            return response()->json([
                'html' => $html,
                'hasError' => $hasError
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi đọc file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xử lý lưu lớp mới (Kèm transaction để import file an toàn)
     */
    public function store(Request $request)
    {
        // 1. Validate Form
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

        // 2. Bắt đầu Giao dịch (Transaction)
        DB::beginTransaction();

        try {
            // Tạo Lớp
            $class = new Classes();
            $class->code = $request->code;
            $class->name = $request->name;
            $class->department_id = 1; // Mặc định Khoa CNTT
            $class->advisor_id = $request->advisor_id;
            $class->academic_year = $request->academic_year;
            $class->save();

            // Import Sinh viên (Nếu có file)
            if ($request->hasFile('student_file')) {
                // Class StudentsImport đã có logic check trùng mã SV -> sẽ ném Exception nếu trùng
                Excel::import(new StudentsImport($class->id), $request->file('student_file'));
            }

            // Nếu mọi thứ OK -> Lưu vào DB
            DB::commit();
            return redirect()->route('admin.classes.index')->with('success', 'Tạo lớp và import danh sách thành công!');
        } catch (\Exception $e) {
            // Nếu có lỗi (Ví dụ: Trùng mã SV) -> Hủy toàn bộ thao tác tạo lớp
            DB::rollBack();

            // Trả về lỗi gắn vào ô input file để hiện màu đỏ
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
    }

    /**
     * Xem chi tiết lớp và danh sách sinh viên của lớp đó
     */
    public function show(string $id)
    {
        $class = Classes::with(['advisor.user', 'department'])->findOrFail($id);

        // Lấy danh sách sinh viên của lớp, phân trang 20 dòng
        $students = $class->students()->orderBy('student_code', 'asc')->paginate(20);

        return view('admin.classes.show', compact('class', 'students'));
    }

    /**
     * Hiển thị form chỉnh sửa lớp
     */
    public function edit(string $id)
    {
        $class = Classes::findOrFail($id);
        $lecturers = Lecturer::with('user')->get();
        $department = Department::where('code', 'CNTT')->first();

        return view('admin.classes.edit', compact('class', 'lecturers', 'department'));
    }

    /**
     * Cập nhật thông tin lớp (Kèm logic import thêm SV)
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:classes,code,' . $id,
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
        ]);

        // Sử dụng Transaction để đảm bảo nếu Import lỗi thì không cập nhật thông tin lớp (tránh dữ liệu không đồng nhất)
        DB::beginTransaction();

        try {
            $class = Classes::findOrFail($id);

            // Cập nhật thông tin lớp
            $class->update([
                'code' => $request->code,
                'name' => $request->name,
                'advisor_id' => $request->advisor_id,
                'academic_year' => $request->academic_year,
            ]);

            // Import thêm Sinh viên (Nếu có file)
            if ($request->hasFile('student_file')) {
                Excel::import(new StudentsImport($class->id), $request->file('student_file'));

                DB::commit();
                return redirect()->route('admin.classes.index')->with('success', 'Cập nhật lớp và thêm sinh viên thành công!');
            }

            DB::commit();
            return redirect()->route('admin.classes.index')->with('success', 'Cập nhật thông tin lớp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Nếu lỗi import -> Trả về thông báo lỗi
            return redirect()->back()->withInput()->withErrors(['student_file' => $e->getMessage()]);
        }
    }

    /**
     * Xóa lớp học
     */
    public function destroy(string $id)
    {
        $class = Classes::findOrFail($id);

        // Kiểm tra ràng buộc: Không xóa lớp nếu đã có sinh viên
        if ($class->students()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa lớp này vì đang có sinh viên!');
        }

        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Đã xóa lớp học!');
    }
}
