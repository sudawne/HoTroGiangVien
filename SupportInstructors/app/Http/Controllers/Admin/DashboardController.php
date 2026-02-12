<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDebt;
use App\Models\MeetingMinute;
use App\Models\ImportBatch;
use Illuminate\Support\Facades\Artisan; 
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Tính toán số liệu cho các thẻ Card trên Dashboard
        $totalStudents = Student::count();
        $debtsCount = StudentDebt::where('status', 'owed')->distinct('student_id')->count();
        $minutesCount = MeetingMinute::whereMonth('created_at', now()->month)->count();

        // Demo dữ liệu cảnh báo (vì chưa có bảng Warning model trong list của bạn, tạm thời gán cứng hoặc query)
        $warningsCount = 3;

        // List sinh viên cần theo dõi (Demo 5 người mới nhất)
        $watchlist = Student::with('class')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalStudents', 'debtsCount', 'minutesCount', 'warningsCount', 'watchlist'));
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
