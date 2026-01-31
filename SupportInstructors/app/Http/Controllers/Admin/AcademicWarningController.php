<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicWarning;
use App\Models\Student;
use App\Models\User;
use App\Models\Semester;
use App\Models\ImportBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Maatwebsite\Excel\Facades\Excel;

class AcademicWarningController extends Controller
{
    public function index(Request $request)
    {
        // 1. Khởi tạo Query
        $query = AcademicWarning::query()->with(['student', 'student.class', 'semester']);

        // 2. Xử lý Bộ lọc
        // Lọc theo Học kỳ
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        
        // Lọc theo Mức cảnh báo
        if ($request->filled('level')) {
            $query->where('warning_level', $request->level);
        }

        // Lọc theo Lớp
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Tìm kiếm (Tên hoặc MSSV)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('fullname', 'like', "%$search%")
                ->orWhere('student_code', 'like', "%$search%");
            });
        }

        // 3. Lấy dữ liệu phân trang
        $warnings = $query->latest('id')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            // Chỉ trả về các dòng <tr> thay vì cả trang web
            return view('admin.academic_warnings.partials.table_rows', compact('warnings'))->render();
        }

        $statsQuery = clone $query; 
        // Bỏ phân trang để đếm tổng
        $allWarnings = $statsQuery->get(); 
        
        $stats = [
            'total' => $allWarnings->count(),
            'level_1' => $allWarnings->where('warning_level', 1)->count(),
            'level_2' => $allWarnings->where('warning_level', 2)->count(),
            'dropout' => $allWarnings->where('warning_level', '>=', 3)->count(),
        ];

        // 5. Lấy dữ liệu cho các Select box bộ lọc
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = \App\Models\Classes::all(); // Hoặc lấy Classes::select('id', 'code')->get();

        return view('admin.academic_warnings.index', compact('warnings', 'stats', 'semesters', 'classes'));
    }

    public function showImport()
    {
        // Lấy danh sách học kỳ để chọn
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('admin.academic_warnings.import', compact('semesters'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required',
        ]);

        try {
            // 1. Dùng thư viện Excel để đọc file thành mảng (Hỗ trợ cả .xlsx và .csv)
            $array = Excel::toArray([], $request->file('file'));
            
            if (empty($array)) {
                return back()->with('error', 'File rỗng hoặc không đọc được dữ liệu.');
            }
            $data = $array[0]; 
            $headerIndex = null;
            $previewData = [];

            // 2. Tìm dòng header (chứa chữ "Mã sinh viên" hoặc "MSSV")
            foreach ($data as $index => $row) {
                $rowString = implode(' ', array_map(function($item) { return (string)$item; }, $row));
                if (mb_stripos($rowString, 'Mã sinh viên') !== false || mb_stripos($rowString, 'MSSV') !== false) {
                    $headerIndex = $index;
                    break;
                }
            }

            if ($headerIndex === null) {
                return back()->with('error', 'Không tìm thấy cột "Mã sinh viên" trong file. Vui lòng kiểm tra lại file Excel.');
            }

            // 3. Map dữ liệu
            for ($i = $headerIndex + 1; $i < count($data); $i++) {
                $row = $data[$i];

                // Kiểm tra nếu cột Mã SV (index 1) bị rỗng thì bỏ qua dòng này
                if (!isset($row[1]) || trim($row[1]) == '') continue;

                $khoa = isset($row[5]) ? trim((string)$row[5]) : '';
                
                // So sánh: Chuyển về chữ hoa để chắc chắn (TT&TT, tt&tt đều nhận)
                // Nếu KHÔNG PHẢI là TT&TT thì bỏ qua vòng lặp này
                if (mb_strtoupper($khoa) !== 'TT&TT') {
                    continue; 
                }

                $mssv = trim((string)$row[1]); 
                $student = Student::where('student_code', $mssv)->first();

                // Xử lý điểm số (chuyển đổi nếu là text "Không ĐKHP")
                $gpa = (isset($row[6]) && is_numeric($row[6])) ? $row[6] : 0;
                $gpa_acc = (isset($row[7]) && is_numeric($row[7])) ? $row[7] : 0;
                $credits_failed = (isset($row[9]) && is_numeric($row[9])) ? $row[9] : 0;

                $previewData[] = [
                    'mssv' => $mssv,
                    'fullname' => $row[2] ?? '',
                    'dob' => $row[3] ?? '',
                    'class_code' => $row[4] ?? '',
                    'department' => $row[5] ?? '',
                    'gpa_term' => $gpa,
                    'gpa_cumulative' => $gpa_acc,
                    'credits_accumulated' => $row[8] ?? 0,
                    'credits_failed' => $credits_failed,
                    'reason' => $row[10] ?? '',
                    'warning_level' => $this->parseWarningLevel($row[11] ?? ''),
                    'note' => $row[11] ?? '',
                    'exists' => $student ? true : false,
                    'student_id' => $student ? $student->id : null,
                    // Lưu lại dòng raw để dùng ở bước store nếu cần
                    'raw_row' => $row 
                ];
            }

            if (empty($previewData)) {
                return back()->with('error', 'Không đọc được dòng dữ liệu nào hợp lệ (Có thể do sai cột Mã SV).');
            }

            return view('admin.academic_warnings.import', [
                'semesters' => Semester::orderBy('start_date', 'desc')->get(), 
                'previewData' => $previewData,
                'semester_id' => $request->semester_id,
                'selected_file_name' => $request->file('file')->getClientOriginalName() 
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi đọc file: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $data = json_decode($request->input('data'), true);
        $semesterId = $request->input('semester_id');

        // Lấy ID người dùng hiện tại, nếu chưa login hoặc lỗi thì lấy ID mặc định là 1 (Admin)
        $importerId = Auth::id() ?? 1; 

        DB::beginTransaction();
        try {
            // 1. Tạo Lô Import (ImportBatch)
            $batch = ImportBatch::create([
                'semester_id' => $semesterId,
                'imported_by' => $importerId, // <--- Đã sửa lỗi tại đây
                'name' => 'Import Cảnh báo ' . now()->format('d/m/Y H:i'),
                'type' => 'warning',
                'status' => 'published',
                'total_records' => count($data)
            ]);

            // 2. Lưu chi tiết cảnh báo
            foreach ($data as $item) {
                // Tìm lại sinh viên trong DB (đề phòng vừa thêm nhanh)
                $student = Student::where('student_code', $item['mssv'])->first();

                if ($student) {
                    AcademicWarning::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'semester_id' => $semesterId,
                        ],
                        [
                            'batch_id' => $batch->id,
                            'warning_level' => $item['warning_level'],
                            'gpa_term' => floatval($item['gpa_term']),
                            'gpa_cumulative' => floatval($item['gpa_cumulative']),
                            'credits_owed' => intval($item['credits_failed']),
                            'warning_count' => $item['warning_level'],
                            'reason' => $item['reason'],
                            'status' => 'pending'
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('admin.academic_warnings.index')->with('success', 'Đã import dữ liệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }

    public function quickAddStudent(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // 1. Tạo User cho sinh viên
            $user = User::create([
                'name' => $request->fullname,
                'email' => $request->mssv . '@student.domain.edu.vn', // Email giả định
                'password' => Hash::make($request->mssv), // Mật khẩu mặc định là MSSV
                'role_id' => 3, // Giả sử 3 là Role Student
                'username' => $request->mssv,
                'is_active' => 1
            ]);

            // 2. Tạo thông tin Sinh viên
            // Xử lý ngày sinh format Excel (có thể là d/m/Y hoặc Y-m-d)
            $dob = null;
            if (!empty($request->dob)) {
                try {
                    $dob = \Carbon\Carbon::parse($request->dob)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Nếu lỗi format ngày thì để null hoặc xử lý sau
                    $dob = null; 
                }
            }

            $student = Student::create([
                'user_id' => $user->id,
                'student_code' => $request->mssv,
                'fullname' => $request->fullname,
                'dob' => $dob,
                'status' => 'studying'
            ]);
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã thêm sinh viên ' . $request->fullname]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function parseWarningLevel($text)
    {
        if (mb_stripos($text, 'Lần 1') !== false) return 1;
        if (mb_stripos($text, 'Lần 2') !== false) return 2;
        if (mb_stripos($text, 'Thôi học') !== false || mb_stripos($text, 'Buộc thôi học') !== false) return 3;
        return 0; 
    }
}