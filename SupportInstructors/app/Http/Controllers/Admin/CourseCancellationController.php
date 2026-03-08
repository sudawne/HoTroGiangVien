<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCancellation;
use App\Models\Semester;
use App\Imports\CourseCancellationsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CourseCancellationController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseCancellation::with(['student.studentClass', 'semester']);

        // Lọc theo học kỳ
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $cancellations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.course_cancellations.index', compact('cancellations', 'semesters'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            Excel::import(new CourseCancellationsImport($request->semester_id), $request->file('file'));
            return back()->with('success', 'Import danh sách xóa học phần thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi import: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        CourseCancellation::destroy($id);
        return back()->with('success', 'Đã xóa bản ghi.');
    }
}