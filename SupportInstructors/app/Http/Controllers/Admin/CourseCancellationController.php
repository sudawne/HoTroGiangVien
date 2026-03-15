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
use Illuminate\Support\Facades\Auth;

class CourseCancellationController extends Controller
{
    private function getRoutePrefix()
    {
        return (Auth::user()->role_id == 1) ? 'admin.' : 'lecturer.';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $routePrefix = $this->getRoutePrefix();

        // 1. Phân quyền: Lấy danh sách lớp học được phép truy cập
        if ($user->role_id == 1) {
            // Nếu là Admin: Lấy tất cả các lớp
            $myClasses = \App\Models\Classes::orderBy('code', 'asc')->get();
        } else {
            // Nếu là Cố vấn học tập: Chỉ lấy các lớp do giảng viên này phụ trách
            $lecturer = \App\Models\Lecturer::where('user_id', $user->id)->first();
            $myClasses = \App\Models\Classes::where('advisor_id', $lecturer->id)->orderBy('code', 'asc')->get();
        }

        $myClassIds = $myClasses->pluck('id')->toArray();

        // 2. Xử lý bộ lọc theo Lớp học
        $selectedClass = $request->input('class_id', 'all');
        $filterClassIds = $selectedClass === 'all' ? $myClassIds : [$selectedClass];

        // 3. Khởi tạo Query lấy danh sách Xóa học phần
        $query = CourseCancellation::with(['student.studentClass', 'semester', 'subject']);

        // BẢO MẬT: Ép buộc Query chỉ lấy sinh viên thuộc các lớp được phép xem
        $query->whereHas('student', function ($q) use ($filterClassIds) {
            $q->whereIn('class_id', $filterClassIds);
        });

        // 4. Các bộ lọc khác (Học kỳ, Tìm kiếm)
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        // 5. Xử lý dữ liệu Thống kê & Biểu đồ
        $totalStudents = Student::whereIn('class_id', $filterClassIds)->count();

        // Lấy số sinh viên đang nợ học phí trong (các) lớp đang chọn
        $debtStudentsCount = Student::whereIn('class_id', $filterClassIds)
            ->whereHas('courseCancellations', function ($q) use ($request) {
                $q->where('reason', 'like', '%Nợ học phí%');
                if ($request->filled('semester_id')) {
                    $q->where('semester_id', $request->semester_id);
                }
            })->count();

        // Tính toán phần trăm (Quy đổi ra %)
        $debtPercentage = $totalStudents > 0 ? round(($debtStudentsCount / $totalStudents) * 100, 2) : 0;
        $cleanPercentage = 100 - $debtPercentage;

        $cancellations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('admin.course_cancellations.partials.table_rows', compact('cancellations'))->render();
        }

        $semesters = Semester::orderBy('start_date', 'desc')->get();

        $stats = [
            'total' => $cancellations->total(),
            'unique_students' => CourseCancellation::whereIn('student_id', Student::whereIn('class_id', $filterClassIds)->pluck('id'))->distinct('student_id')->count(),
        ];

        return view('admin.course_cancellations.index', compact(
            'cancellations',
            'semesters',
            'stats',
            'routePrefix',
            'myClasses',
            'selectedClass',
            'totalStudents',
            'debtStudentsCount',
            'debtPercentage',
            'cleanPercentage'
        ));
    }

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
            $mssv = $row['student_code'];
            $subjectCode = $row['subject_code'];

            $student = Student::where('student_code', $mssv)->first();
            $subject = Subject::where('code', $subjectCode)->first();

            if ($student && $subject) {
                $exists = CourseCancellation::where('student_id', $student->id)
                    ->where('semester_id', $semester_id)
                    ->where('subject_id', $subject->id)
                    ->exists();

                if (!$exists) {
                    CourseCancellation::create([
                        'student_id'  => $student->id,
                        'semester_id' => $semester_id,
                        'subject_id'  => $subject->id,
                        'reason'      => $row['reason'] ?? 'Nợ học phí',
                    ]);
                    $count++;
                }
            }
        }

        $routePrefix = $this->getRoutePrefix();
        return redirect()->route($routePrefix . 'course_cancellations.index')
            ->with('success', "Đã import thành công $count dòng dữ liệu.");
    }

    // --- EXPORT ---
    public function export(Request $request)
    {
        $user = Auth::user();

        // Phân quyền cho Export (Không cho phép GV xuất báo cáo của lớp khác)
        if ($user->role_id == 1) {
            $myClassIds = \App\Models\Classes::pluck('id')->toArray();
        } else {
            $lecturer = \App\Models\Lecturer::where('user_id', $user->id)->first();
            $myClassIds = \App\Models\Classes::where('advisor_id', $lecturer->id)->pluck('id')->toArray();
        }

        $selectedClass = $request->input('class_id', 'all');
        $filterClassIds = $selectedClass === 'all' ? $myClassIds : [$selectedClass];

        $query = CourseCancellation::with(['student.studentClass', 'semester', 'subject']);

        // Lọc dữ liệu xuất theo lớp
        $query->whereHas('student', function ($q) use ($filterClassIds) {
            $q->whereIn('class_id', $filterClassIds);
        });

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu nào phù hợp với bộ lọc hiện tại để xuất.');
        }

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('admin.course_cancellations.pdf_export', compact('data'));
            $pdf->setOption('defaultFont', 'DejaVu Serif');
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('ds-huy-hoc-phan.pdf');
        }

        if ($request->format === 'excel') {
            return Excel::download(new CourseCancellationsExport($data), 'ds-huy-hoc-phan.xlsx');
        }

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
            if (!$headerFound) {
                $col1 = trim($row[1] ?? '');
                if (stripos($col1, 'Mã sinh viên') !== false || stripos($col1, 'MSSV') !== false) {
                    $headerFound = true;
                }
                continue;
            }

            $mssv = trim($row[1] ?? '');
            $classCode = trim($row[3] ?? '');
            $subjectCode = trim($row[4] ?? '');

            if (empty($mssv) || empty($classCode)) continue;
            if (!preg_match('/(TT|PT)/i', $classCode)) {
                continue;
            }

            $student = Student::where('student_code', $mssv)->first();
            $subject = Subject::where('code', $subjectCode)->first();
            $credits = (int)($row[7] ?? 0);

            $previewData[] = [
                'student_code' => $mssv,
                'student_name' => $row[2] ?? '',
                'class_code'   => $classCode,
                'subject_code' => $subjectCode,
                'subject_name' => $row[5] ?? 'Chưa xác định',
                'credits'      => $credits,
                'reason'       => 'Nợ học phí',

                'student_exists' => $student ? true : false,
                'subject_exists' => $subject ? true : false,

                'student_id'   => $student ? $student->id : null,
                'subject_id'   => $subject ? $subject->id : null,
            ];
        }

        return view('admin.course_cancellations.preview', compact('previewData', 'semester_id', 'semesters'));
    }

    public function quickStore(Request $request)
    {
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $subject = Subject::create([
                'code' => trim($request->code),
                'name' => trim($request->name),
                'credits' => $request->credits
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm môn học thành công!',
                'code' => $subject->code,
                'data' => $subject
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
}
