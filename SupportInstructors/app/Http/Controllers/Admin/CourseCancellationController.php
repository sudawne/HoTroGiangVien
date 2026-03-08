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
use Illuminate\Support\Facades\Validator;

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
            // 1. Lấy mã từ dữ liệu gửi lên
            $mssv = $row['student_code'];
            $subjectCode = $row['subject_code'];

            // 2. Tra cứu lại trong Database (Đây là bước QUAN TRỌNG để lấy ID mới nhất)
            $student = Student::where('student_code', $mssv)->first();
            $subject = Subject::where('code', $subjectCode)->first();

            // 3. Chỉ lưu khi tìm thấy cả Sinh viên và Môn học trong DB
            if ($student && $subject) {
                // Kiểm tra trùng lặp để tránh lưu 2 lần (Optional)
                $exists = CourseCancellation::where('student_id', $student->id)
                            ->where('semester_id', $semester_id)
                            ->where('subject_id', $subject->id)
                            ->exists();

                if (!$exists) {
                    CourseCancellation::create([
                        'student_id'  => $student->id,
                        'semester_id' => $semester_id,
                        'subject_id'  => $subject->id, // Sử dụng ID vừa tìm thấy
                        'reason'      => $row['reason'] ?? 'Nợ học phí',
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('admin.course_cancellations.index')
                        ->with('success', "Đã import thành công $count dòng dữ liệu.");
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
        $semesters = Semester::orderBy('start_date', 'desc')->get();
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
            // 1. Tìm dòng tiêu đề (Logic cũ)
            if (!$headerFound) {
                $col1 = trim($row[1] ?? ''); 
                if (stripos($col1, 'Mã sinh viên') !== false || stripos($col1, 'MSSV') !== false) {
                    $headerFound = true;
                }
                continue;
            }

            // 2. Lấy dữ liệu thô
            $mssv = trim($row[1] ?? ''); 
            $classCode = trim($row[3] ?? ''); // Cột Mã Lớp
            $subjectCode = trim($row[4] ?? ''); 
            
            if (empty($mssv) || empty($classCode)) continue;

            // 3. [MỚI] LỌC THEO KHOA (CHỈ LẤY LỚP CÓ 'TT' HOẶC 'PT')
            // Sử dụng Regex để tìm chuỗi TT hoặc PT trong mã lớp (VD: B022TT2, B025PT2)
            if (!preg_match('/(TT|PT)/i', $classCode)) {
                continue; // Bỏ qua dòng này nếu không thuộc khoa CNTT
            }

            // 4. Tìm dữ liệu trong DB
            $student = Student::where('student_code', $mssv)->first();
            $subject = Subject::where('code', $subjectCode)->first();

            // Lấy số tín chỉ từ file Excel (Giả sử cột thứ 7 - Index 7 hoặc 8 tùy file)
            // Trong file bạn gửi: Col H (Index 7) là "Đơn vị học trình/Tín chỉ"
            $credits = (int)($row[7] ?? 0); 

            $previewData[] = [
                'student_code' => $mssv,
                'student_name' => $row[2] ?? '',
                'class_code'   => $classCode,
                'subject_code' => $subjectCode,
                'subject_name' => $row[5] ?? 'Chưa xác định',
                'credits'      => $credits, // Lưu tạm để dùng cho Quick Add Subject
                'reason'       => 'Nợ học phí',
                
                // Trạng thái kiểm tra
                'student_exists' => $student ? true : false,
                'subject_exists' => $subject ? true : false,
                
                // ID để lưu
                'student_id'   => $student ? $student->id : null,
                'subject_id'   => $subject ? $subject->id : null,
            ];
        }

        return view('admin.course_cancellations.preview', compact('previewData', 'semester_id', 'semesters'));
    }
    public function quickStore(Request $request)
    {
        // 1. Validate dữ liệu thủ công để trả về JSON
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
        ], [
            'code.required' => 'Vui lòng nhập mã môn học.',
            'code.unique' => 'Mã môn học này đã tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên môn học.',
            'credits.required' => 'Vui lòng nhập số tín chỉ.',
        ]);

        // Nếu lỗi validate -> Trả về JSON lỗi
        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first() // Lấy lỗi đầu tiên để hiển thị alert
            ]);
        }

        try {
            // 2. Tạo môn học mới
            $subject = Subject::create([
                'code' => trim($request->code),
                'name' => trim($request->name),
                'credits' => $request->credits
            ]);

            // 3. Trả về JSON thành công
            return response()->json([
                'success' => true,
                'message' => 'Thêm môn học thành công!',
                'code' => $subject->code, // Trả về mã để JS cập nhật giao diện (đổi màu đỏ -> đen)
                'data' => $subject
            ]);

        } catch (\Exception $e) {
            // 4. Xử lý lỗi hệ thống
            return response()->json([
                'success' => false, 
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
}