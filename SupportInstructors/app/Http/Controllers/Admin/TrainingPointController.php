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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TrainingPointsExport;

class TrainingPointController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingPoint::with(['student.studentClass', 'semester']);

        // bộ lọc 
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rank')) {
            switch ($request->rank) {
                case 'xuatsac':
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

        // thực thi Query
        $trainingPoints = $query->join('students', 'training_points.student_id', '=', 'students.id')
            ->orderBy('students.class_id')
            ->orderBy('students.fullname', 'asc')
            ->select('training_points.*')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.training_points.partials.table_rows', compact('trainingPoints'))->render();
        }

        // thống kê
        $statsQuery = TrainingPoint::query();
        if ($request->filled('semester_id')) $statsQuery->where('semester_id', $request->semester_id);
        if ($request->filled('class_id')) {
            $statsQuery->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $allScores = $statsQuery->get();

        $stats = [
            'total'   => $allScores->count(),
            'xuatsac' => $allScores->where('final_score', '>=', 90)->count(),
            'tot'     => $allScores->whereBetween('final_score', [80, 89])->count(),
            'kha'     => $allScores->whereBetween('final_score', [65, 79])->count(),
            'yeu'     => $allScores->where('final_score', '<', 65)->whereNotNull('final_score')->count(),
            'chuaxet' => $allScores->whereNull('final_score')->count(),
        ];

        $classes = Classes::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.training_points.index', compact('trainingPoints', 'stats', 'classes', 'semesters'));
    }

    public function import()
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $classes = Classes::all();
        return view('admin.training_points.import', compact('semesters', 'classes'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required',
            'class_id' => 'required',
        ]);

        $data = Excel::toArray([], $request->file('file'));
        $rows = isset($data[0]) ? array_slice($data[0], 5) : [];

        $previewData = [];
        $selectedClassId = $request->class_id;

        foreach ($rows as $row) {
            $mssv = $row[1] ?? null;
            if (!$mssv) continue;

            $student = Student::where('student_code', $mssv)->first();

            $status = 'valid';
            $message = 'Hợp lệ';
            $student_id = null;

            if (!$student) {
                $status = 'error';
                $message = 'Sinh viên chưa có trong hệ thống';
            } elseif ($student->class_id != $selectedClassId) {
                $status = 'warning';
                $message = 'Sinh viên thuộc lớp khác: ' . ($student->studentClass->code ?? 'N/A');
                $student_id = $student->id;
            } else {
                $student_id = $student->id;
            }

            $selfScore = is_numeric($row[5]) ? $row[5] : 0;
            $classScore = is_numeric($row[6]) ? $row[6] : 0;

            $previewData[] = [
                'mssv' => $mssv,
                'fullname' => $row[2] ?? 'N/A',
                'dob' => $row[3] ?? '',
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
                            'advisor_score' => $row['class_score'],
                            'final_score' => $row['class_score'],
                        ]
                    );
                    $count++;
                }
            }
            DB::commit();
            return redirect()->route('admin.training_points.index')->with('success', "Đã nhập thành công $count bản ghi điểm rèn luyện.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = TrainingPoint::with(['student.studentClass', 'semester']);

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('rank')) {
            switch ($request->rank) {
                case 'xuatsac':
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

        $data = $query->join('students', 'training_points.student_id', '=', 'students.id')
            ->orderBy('students.class_id')
            ->orderBy('students.fullname', 'asc')
            ->select('training_points.*')
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu nào phù hợp với bộ lọc hiện tại để xuất.');
        }

        if ($request->format === 'excel') {
            return Excel::download(new TrainingPointsExport($data), 'diem-ren-luyen-sv.xlsx');
        } elseif ($request->format === 'pdf') {
            $pdf = Pdf::loadView('admin.training_points.pdf_export', compact('data'));
            $pdf->setOption('defaultFont', 'DejaVu Sans'); 
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('diem-ren-luyen-sv.pdf');
        }

        return back();
    }
}
