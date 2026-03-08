<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCancellation;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CourseCancellationsExport; 
use Illuminate\Support\Str;

class CourseCancellationController extends Controller
{
    public function index(Request $request)
    {
        // Eager load thêm 'subject'
        $query = CourseCancellation::with(['student.studentClass', 'semester', 'subject']);

        $cancellations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // THÊM ĐOẠN NÀY:
        if ($request->ajax()) {
            return view('admin.course_cancellations.partials.table_rows', compact('cancellations'))->render();
        }
        
        if ($request->filled('semester_id')) $query->where('semester_id', $request->semester_id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $cancellations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        
        // Thống kê đơn giản (Bỏ phần tiền)
        $stats = [
            'total' => CourseCancellation::count(),
            'unique_students' => CourseCancellation::distinct('student_id')->count(),
        ];

        return view('admin.course_cancellations.index', compact('cancellations', 'semesters', 'stats'));
    }

    // --- IMPORT FLOW ---
    public function showImportForm()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('admin.course_cancellations.import', compact('semesters'));
    }

    public function storeImport(Request $request)
    {
        $data = json_decode($request->data, true);
        $semester_id = $request->semester_id;
        $count = 0;

        foreach ($data as $row) {
            // Chỉ lưu khi CẢ Sinh viên VÀ Môn học đều tồn tại trong DB
            if ($row['student_exists'] && $row['subject_exists']) {
                CourseCancellation::create([
                    'student_id'  => $row['student_id'],
                    'semester_id' => $semester_id,
                    'subject_id'  => $row['subject_id'], // Lưu ID môn học
                    'reason'      => $row['reason'],
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.course_cancellations.index')->with('success', "Đã import thành công $count bản ghi.");
    }

    // --- EXPORT ---
    public function export(Request $request)
    {
        $query = CourseCancellation::with(['student.studentClass', 'semester']);
        // ... (Áp dụng các filter giống index) ...
        if ($request->filled('semester_id')) $query->where('semester_id', $request->semester_id);
        
        $data = $query->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('admin.course_cancellations.pdf_export', compact('data'));
            $pdf->setOption('defaultFont', 'DejaVu Serif');
            return $pdf->download('ds-xoa-hoc-phan.pdf');
        }
        
        // Excel: Bạn tự tạo class Export tương tự Warning nhé
        // return Excel::download(new CourseCancellationsExport($data), 'ds-xoa-hoc-phan.xlsx');
        
        return back();
    }

    public function destroy($id)
    {
        CourseCancellation::destroy($id);
        return back()->with('success', 'Đã xóa bản ghi.');
    }
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required'
        ]);

        $data = Excel::toArray(new \stdClass(), $request->file('file'));
        $rows = $data[0] ?? [];
        
        $previewData = [];
        $semester_id = $request->semester_id;
        $headerFound = false;

        foreach ($rows as $row) {
            // Tìm dòng tiêu đề (như cũ)
            if (!$headerFound) {
                $col1 = trim($row[1] ?? ''); 
                if (stripos($col1, 'Mã sinh viên') !== false || stripos($col1, 'MSSV') !== false) {
                    $headerFound = true;
                }
                continue;
            }

            $mssv = trim($row[1] ?? ''); // Cột Mã SV
            $subjectCode = trim($row[4] ?? ''); // Cột Mã Học Phần (Điều chỉnh index nếu file khác)

            if (empty($mssv) || empty($subjectCode)) continue;

            // 1. Tìm Sinh viên
            $student = Student::where('student_code', $mssv)->first();
            
            // 2. Tìm Môn học (Quan trọng: Phải tìm thấy trong bảng subjects)
            $subject = Subject::where('code', $subjectCode)->first();

            $previewData[] = [
                'student_code' => $mssv,
                'student_name' => $row[2] ?? '',
                'subject_code' => $subjectCode,
                'subject_name' => $row[5] ?? 'Chưa xác định', // Tên môn từ Excel (chỉ để hiển thị preview)
                'reason'       => 'Nợ học phí',
                
                // Trạng thái kiểm tra
                'student_exists' => $student ? true : false,
                'subject_exists' => $subject ? true : false,
                
                // ID để lưu
                'student_id'   => $student ? $student->id : null,
                'subject_id'   => $subject ? $subject->id : null,
            ];
        }

        return view('admin.course_cancellations.preview', compact('previewData', 'semester_id'));
    }
}