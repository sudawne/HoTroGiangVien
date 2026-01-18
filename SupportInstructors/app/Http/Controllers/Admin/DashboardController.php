<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDebt;
use App\Models\MeetingMinute;
use App\Models\ImportBatch;

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
}
