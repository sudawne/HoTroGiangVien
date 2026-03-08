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
        // 1. Lấy danh sách tất cả học kỳ (Mới nhất lên đầu)
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        // 2. Xác định học kỳ đang chọn (Nếu ko chọn thì lấy kỳ hiện tại)
        // Ưu tiên 1: Lấy từ bộ lọc (?semester_id=...)
        // Ưu tiên 2: Lấy kỳ đang active (is_current = 1)
        // Ưu tiên 3: Lấy kỳ mới nhất trong DB
        $defaultSemester = $semesters->where('is_current', 1)->first() ?? $semesters->first();
        $selectedId = $request->get('semester_id') ?? ($defaultSemester ? $defaultSemester->id : null);

        // Lấy tên để hiển thị ra giao diện
        $selectedSemester = $semesters->where('id', $selectedId)->first();
        $semesterLabel = $selectedSemester ? $selectedSemester->name . ' (' . $selectedSemester->academic_year . ')' : 'Chưa chọn';

        // --- TRUY VẤN DỮ LIỆU THEO KỲ ĐÃ CHỌN ---

        // Card 1: Tổng sinh viên (Luôn đếm tất cả đang học, không theo kỳ)
        $totalStudents = Student::where('status', 'studying')->count();
        $newStudentsCount = Student::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // Card 2: Cảnh báo (Theo kỳ chọn)
        $warningCount = AcademicWarning::where('semester_id', $selectedId)->count();

        // Card 3: Xóa học phần (Theo kỳ chọn)
        $debtCount = CourseCancellation::where('semester_id', $selectedId)->count();

        // Card 4: Biên bản họp (Theo kỳ chọn)
        $minuteCount = MeetingMinute::where('semester_id', $selectedId)->count();

        // Bảng danh sách sinh viên bị cảnh báo (Theo kỳ chọn)
        $studentsToWatch = Student::whereHas('academicWarnings', function($q) use ($selectedId) {
                                $q->where('semester_id', $selectedId);
                            })
                            ->with(['studentClass', 'academicWarnings' => function($q) use ($selectedId) {
                                $q->where('semester_id', $selectedId)->orderBy('warning_level', 'desc');
                            }])
                            ->take(5) // Lấy 5 người đầu tiên
                            ->get();

        // Widget Thông báo & Lịch họp (Lấy chung hoặc theo kỳ)
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

    // Cập nhật học kì
    public function runSystemCheck()
    {
        try {
            Artisan::call('app:generate-semesters');
            
            // Lấy thông báo từ Command trả về
            $output = Artisan::output();
            $message = !empty(trim($output)) ? $output : 'Hệ thống đã được rà soát. Dữ liệu năm học đang ở trạng thái mới nhất.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error("System Check Error: " . $e->getMessage());
            return back()->with('error', 'Lỗi khi rà soát hệ thống: ' . $e->getMessage());
        }
    }
}
