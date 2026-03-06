<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingPoint;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Semester;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class TrainingPointController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingPoint::with(['student.studentClass', 'semester']);

        // 2. Bộ lọc (Filters)
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        
        if ($request->filled('class_id')) {
            // Lọc sinh viên thuộc lớp đó
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rank')) {
            switch ($request->rank) {
                case 'xuatsac': // 90 - 100
                    $query->where('final_score', '>=', 90);
                    break;
                case 'tot': 
                    $query->whereBetween('final_score', [80, 89]);
                    break;
                case 'kha': 
                    $query->whereBetween('final_score', [65, 79]);
                    break;
                case 'trungbinh': 
                    $query->whereBetween('final_score', [50, 64]);
                    break;
                case 'yeu': 
                    $query->where('final_score', '<', 50)->whereNotNull('final_score');
                    break;
                case 'chuaxet': 
                    $query->whereNull('final_score');
                    break;
            }
        }

        // 3. Thực thi Query & Phân trang
        // Lưu ý: Đổi 'first_name' thành 'fullname' nếu bảng students chưa tách tên
        $trainingPoints = $query->join('students', 'training_points.student_id', '=', 'students.id')
                                ->orderBy('students.class_id')
                                ->orderBy('students.fullname', 'asc')
                                ->select('training_points.*') 
                                ->paginate(10)
                                ->withQueryString();
        if ($request->ajax()) {
            return view('admin.training_points.partials.table_rows', compact('trainingPoints'))->render();
        }
        // 4. Thống kê (Stats)
        $statsQuery = TrainingPoint::query();
        
        // Áp dụng lại các filter cho stats (để thống kê chính xác theo bộ lọc hiện tại)
        if ($request->filled('semester_id')) $statsQuery->where('semester_id', $request->semester_id);
        if ($request->filled('class_id')) {
            $statsQuery->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        // Không cần filter search cho stats, thường stats sẽ tính tổng quát hơn

        $allScores = $statsQuery->get(); 

        $stats = [
            'total'   => $allScores->count(),
            'xuatsac' => $allScores->where('final_score', '>=', 90)->count(),
            'tot'     => $allScores->whereBetween('final_score', [80, 89])->count(),
            'kha'     => $allScores->whereBetween('final_score', [65, 79])->count(),
            'yeu'     => $allScores->where('final_score', '<', 65)->whereNotNull('final_score')->count(),
            'chuaxet' => $allScores->whereNull('final_score')->count(),
        ];

        // 5. Lấy dữ liệu cho Filter Box
        $classes = Classes::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.training_points.index', compact('trainingPoints', 'stats', 'classes', 'semesters'));
    }

    public function import()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = Classes::all(); // Lấy danh sách lớp để chọn
        return view('admin.training_points.import', compact('semesters', 'classes'));
    }

    // 2. Xử lý Preview (Đọc file và kiểm tra)
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required',
            'class_id' => 'required', // Bắt buộc chọn lớp
        ]);

        // Đọc file Excel
        $data = Excel::toArray([], $request->file('file'));
        $rows = isset($data[0]) ? array_slice($data[0], 5) : [];

        $previewData = [];
        $selectedClassId = $request->class_id;

        foreach ($rows as $row) {
            $mssv = $row[1] ?? null; // Cột B là MSSV
            
            // Bỏ qua dòng trống hoặc không có MSSV
            if (!$mssv) continue;

            // 1. Tìm sinh viên trong DB
            $student = Student::where('student_code', $mssv)->first();
            
            // 2. Logic kiểm tra
            $status = 'valid';
            $message = 'Hợp lệ';
            $student_id = null;

            if (!$student) {
                $status = 'error';
                $message = 'Sinh viên chưa có trong hệ thống';
            } elseif ($student->class_id != $selectedClassId) {
                // (Tùy chọn) Cảnh báo nếu sinh viên không thuộc lớp đã chọn
                $status = 'warning';
                $message = 'Sinh viên thuộc lớp khác: ' . ($student->studentClass->code ?? 'N/A');
                $student_id = $student->id; // Vẫn cho phép nhập nhưng cảnh báo
            } else {
                $student_id = $student->id;
            }

            // Lấy điểm từ file
            $selfScore = is_numeric($row[5]) ? $row[5] : 0; // Cột F: SV tự đánh giá
            $classScore = is_numeric($row[6]) ? $row[6] : 0; // Cột G: Lớp đánh giá

            $previewData[] = [
                'mssv' => $mssv,
                'fullname' => $row[2] ?? 'N/A', // Cột C: Tên
                'dob' => $row[3] ?? '',         // Cột D: Ngày sinh
                'self_score' => $selfScore,
                'class_score' => $classScore,
                'student_id' => $student_id,
                'status' => $status, 
                'message' => $message,
            ];
        }

        return view('admin.training_points.preview', [
            'previewData' => $previewData,
            'semester_id' => $request->semester_id,
            'class_id' => $request->class_id
        ]);
    }

    // 3. Lưu dữ liệu chính thức
    public function storeImport(Request $request)
    {
        $data = json_decode($request->data, true);
        $semester_id = $request->semester_id;
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if ($row['student_id']) {
                    TrainingPoint::updateOrCreate(
                        [
                            'student_id' => $row['student_id'],
                            'semester_id' => $semester_id,
                        ],
                        [
                            'self_score' => $row['self_score'],
                            'class_score' => $row['class_score'],
                            'advisor_score' => $row['class_score'], // Mặc định điểm khoa = điểm lớp
                            'final_score' => $row['class_score'],   // Chốt điểm luôn
                        ]
                    );
                    $count++;
                }
            }
            DB::commit();
            return redirect()->route('admin.training_points.index')
                             ->with('success', "Đã nhập thành công $count bản ghi điểm rèn luyện.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}