<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicResult;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Classes; // [QUAN TRỌNG] Nhớ import Model Classes
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class AcademicResultController extends Controller
{
    /**
     * 1. Hiển thị danh sách (Index)
     */
    public function index(Request $request)
    {
        $query = AcademicResult::with(['student.studentClass', 'semester']);

        // --- BỘ LỌC ---
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        // --- TÍNH TOÁN THỐNG KÊ (STATS) ---
        // Clone query để thống kê theo bộ lọc hiện tại (nếu muốn) hoặc thống kê toàn bộ
        // Ở đây mình thống kê toàn bộ học kỳ hiện tại cho nhanh
        $statsQuery = AcademicResult::query();
        if ($request->filled('semester_id')) {
            $statsQuery->where('semester_id', $request->semester_id);
        }

        $allStats = $statsQuery->get();
        $stats = [
            'total'   => $allStats->count(),
            'xuatsac' => $allStats->whereIn('classification', ['Xuất sắc', 'Giỏi'])->count(),
            'kha'     => $allStats->where('classification', 'Khá')->count(),
            'tb'      => $allStats->where('classification', 'Trung bình')->count(),
            'yeu'     => $allStats->whereIn('classification', ['Yếu', 'Kém', 'Học lại'])->count(),
        ];

        $results = $query->paginate(10)->withQueryString();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = Classes::all();

        // Trả về JSON nếu là AJAX Search
        if ($request->ajax()) {
            return view('admin.academic_results.partials.table_rows', compact('results'))->render();
        }

        return view('admin.academic_results.index', compact('results', 'semesters', 'classes', 'stats'));
    }

    /**
     * 2. Hiển thị form Import
     */
    public function import()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = Classes::all(); // Thêm dòng này

        return view('admin.academic_results.import', compact('semesters', 'classes'));
    }

    /**
     * 3. Xử lý file Excel và hiển thị Preview
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required',
            // 'class_id' => 'required',
        ]);

        $array = Excel::toArray([], $request->file('file'));
        $rows = isset($array[0]) ? array_slice($array[0], 4) : []; // Data từ dòng 4

        $previewData = [];
        $selectedClassId = $request->class_id; 

        foreach ($rows as $row) {
            $mssv = $row[1] ?? null; 
            if (!$mssv) continue; 

            $student = Student::where('student_code', $mssv)->first();
            
            $status = 'valid';
            $message = 'Hợp lệ';

            if (!$student) {
                $status = 'error';
                $message = 'Sinh viên chưa có trong hệ thống';
            } elseif ($selectedClassId && $student->class_id != $selectedClassId) {
                // Cảnh báo nếu sinh viên trong file không thuộc lớp đã chọn
                $status = 'warning'; 
                $message = 'Sinh viên thuộc lớp khác (' . ($student->studentClass->code ?? 'N/A') . ')';
            }

            $previewData[] = [
                'mssv' => $mssv,
                'fullname' => ($row[2] ?? '') . ' ' . ($row[3] ?? ''),
                'class_code' => $row[6] ?? '',
                
                'gpa_10' => floatval($row[9] ?? 0),
                'gpa_4' => floatval($row[10] ?? 0),
                'classification' => $row[11] ?? 'Chưa xét',
                
                'student_id' => $student ? $student->id : null,
                'status' => $status, // valid, error, warning
                'message' => $message,
            ];
        }

        return view('admin.academic_results.preview', [
            'previewData' => $previewData,
            'semester_id' => $request->semester_id,
            'class_id' => $request->class_id, // Truyền lại class_id để lưu sau này
        ]);
    }

    /**
     * 4. Lưu chính thức vào CSDL
     */
    public function storeImport(Request $request)
    {
        $data = json_decode($request->data, true);
        $semester_id = $request->semester_id;
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                // Lưu nếu hợp lệ hoặc chỉ là cảnh báo (vẫn cho lưu nhưng warning)
                if (($row['status'] == 'valid' || $row['status'] == 'warning') && $row['student_id']) {
                    AcademicResult::updateOrCreate(
                        [
                            'student_id' => $row['student_id'],
                            'semester_id' => $semester_id,
                        ],
                        [
                            'gpa_10' => $row['gpa_10'],
                            'gpa_4' => $row['gpa_4'],
                            'classification' => $row['classification'],
                        ]
                    );
                    $count++;
                }
            }
            DB::commit();
            
            return redirect()->route('admin.academic_results.index')
                             ->with('success', "Đã nhập thành công kết quả học tập cho $count sinh viên!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }
}