<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicResult;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Classes; 
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class AcademicResultController extends Controller
{
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
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        // --- TÍNH TOÁN THỐNG KÊ---
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

        if ($request->ajax()) {
            return view('admin.academic_results.partials.table_rows', compact('results'))->render();
        }

        return view('admin.academic_results.index', compact('results', 'semesters', 'classes', 'stats'));
    }

    public function import()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = Classes::all(); 

        return view('admin.academic_results.import', compact('semesters', 'classes'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required',
        ]);

        $array = Excel::toArray([], $request->file('file'));
        $rows = isset($array[0]) ? array_slice($array[0], 4) : []; // từ dòng 4

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
                'status' => $status, 
                'message' => $message,
            ];
        }

        return view('admin.academic_results.preview', [
            'previewData' => $previewData,
            'semester_id' => $request->semester_id,
            'class_id' => $request->class_id, 
        ]);
    }

    public function storeImport(Request $request)
    {
        $data = json_decode($request->data, true);
        $semester_id = $request->semester_id;
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
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
    public function show($id)
    {
        $result = AcademicResult::with(['student', 'semester'])->findOrFail($id);
        return back()->with('info', 'Chức năng xem chi tiết đang được cập nhật!');
    }
    public function export(Request $request)
    {
        // Khởi tạo query y hệt bộ lọc của hàm index
        $query = AcademicResult::with(['student.studentClass', 'semester']);

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }
        $data = $query->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu nào phù hợp với bộ lọc hiện tại để xuất.');
        }
        if ($request->format === 'excel') {
            return Excel::download(new \App\Exports\AcademicResultsExport($data), 'ket-qua-hoc-tap-sv.xlsx');
        }
        elseif ($request->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.academic_results.pdf_export', compact('data'));
            $pdf->setOption('defaultFont', 'DejaVu Sans');
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('ket-qua-hoc-tap-sv.pdf');
        }
        return back();
    }
}
