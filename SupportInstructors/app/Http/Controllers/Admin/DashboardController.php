<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan; 
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\AcademicWarning;
use App\Models\CourseCancellation;
use App\Models\MeetingMinute;
use App\Models\Semester;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $defaultSemester = $semesters->where('is_current', 1)->first() ?? $semesters->first();
        $selectedId = $request->get('semester_id') ?? ($defaultSemester ? $defaultSemester->id : null);
        $selectedSemester = $semesters->where('id', $selectedId)->first();
        $semesterLabel = $selectedSemester ? $selectedSemester->name . ' (' . $selectedSemester->academic_year . ')' : 'Chưa chọn';
        $totalStudents = Student::where('status', 'studying')->count();
        $newStudentsCount = Student::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $warningCount = AcademicWarning::where('semester_id', $selectedId)->count();
        $debtCount = CourseCancellation::where('semester_id', $selectedId)->count();
        $minuteCount = MeetingMinute::where('semester_id', $selectedId)->count();
        $studentsToWatch = Student::whereHas('academicWarnings', function($q) use ($selectedId) {
                                $q->where('semester_id', $selectedId);
                            })
                            ->with(['studentClass', 'academicWarnings' => function($q) use ($selectedId) {
                                $q->where('semester_id', $selectedId)->orderBy('warning_level', 'desc');
                            }])
                            ->take(5) 
                            ->get();

        $recentNotifications = Notification::orderBy('created_at', 'desc')->take(3)->get();
        $upcomingMeetings = MeetingMinute::where('semester_id', $selectedId)
                                         ->orderBy('created_at', 'desc')
                                         ->take(4)
                                         ->get();

        return view('admin.dashboard', compact(
            'semesters', 'selectedId', 'semesterLabel',
            'totalStudents', 'newStudentsCount',
            'warningCount', 'debtCount', 'minuteCount',
            'studentsToWatch', 'recentNotifications', 'upcomingMeetings'
        ));
    }

    public function runSystemCheck()
    {
        try {
            Artisan::call('app:generate-semesters');
            $output = Artisan::output();
            $message = !empty(trim($output)) ? $output : 'Hệ thống đã được rà soát. Dữ liệu năm học đang ở trạng thái mới nhất.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error("System Check Error: " . $e->getMessage());
            return back()->with('error', 'Lỗi khi rà soát hệ thống: ' . $e->getMessage());
        }
    }
}
