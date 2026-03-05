<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\Classes;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;

class AcademicResultController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh sách Lớp và Học kỳ để làm bộ lọc
        $classes = Classes::orderBy('code', 'asc')->get();
        $semesters = Semester::orderBy('id', 'desc')->get();

        // Học kỳ hiện tại mặc định (nếu chưa chọn)
        $currentSemester = Semester::where('is_current', true)->first();

        // 2. Query lấy dữ liệu (Load kèm Sinh viên, Lớp và Học kỳ)
        $query = AcademicResult::with(['student.class', 'semester']);

        // Bộ lọc theo Lớp
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Bộ lọc theo Học kỳ (Mặc định lấy học kỳ hiện tại nếu chưa chọn)
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else if ($currentSemester) {
            $query->where('semester_id', $currentSemester->id);
            $request->merge(['semester_id' => $currentSemester->id]); // Để select box giữ giá trị
        }

        // Bộ lọc Tìm kiếm (MSSV hoặc Tên SV)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchPattern = preg_replace('/\s+/', '%', $search);

            $query->whereHas('student', function ($q) use ($search, $searchPattern) {
                $q->where('fullname', 'LIKE', "%{$searchPattern}%")
                    ->orWhere('student_code', 'LIKE', "%{$search}%");
            });
        }

        // 3. Phân trang
        $results = $query->orderBy('gpa_4', 'desc')->paginate(20)->withQueryString();

        // 4. Nếu là request AJAX (khi gõ tìm kiếm live search)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.academic_results.partials.table_rows', compact('results'))->render(),
                'pagination' => (string) $results->links(),
                'total' => $results->total()
            ]);
        }

        return view('admin.academic_results.index', compact('results', 'classes', 'semesters'));
    }
}
