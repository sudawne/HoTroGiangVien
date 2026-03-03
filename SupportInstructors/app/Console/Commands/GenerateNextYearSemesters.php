<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateNextYearSemesters extends Command
{
    protected $signature = 'app:generate-semesters';
    protected $description = 'Tự động tạo học kỳ cho năm tiếp theo dựa trên dữ liệu cũ nhất';

    public function handle()
    {
        // 1. Tìm học kỳ mới nhất đang có trong Database
        $latestSemester = DB::table('semesters')
                            ->orderBy('start_date', 'desc') // Lấy cái có ngày bắt đầu lớn nhất
                            ->first();

        // 2. Xác định năm bắt đầu tiếp theo
        if ($latestSemester) {
            // Nếu DB đã có dữ liệu (VD: đang là 2025-2026)
            // Tách chuỗi academic_year "2025-2026" để lấy số 2025
            $parts = explode('-', $latestSemester->academic_year);
            $lastStartYear = (int) $parts[0]; 
            
            // Năm tiếp theo sẽ là năm cũ + 1 (2025 + 1 = 2026)
            $nextStartYear = $lastStartYear + 1;
        } else {
            // Nếu DB chưa có gì, lấy năm hiện tại
            $nextStartYear = Carbon::now()->year;
        }

        $nextEndYear = $nextStartYear + 1; // 2027
        $academicYearString = "{$nextStartYear}-{$nextEndYear}"; // "2026-2027"

        // 3. Kiểm tra xem năm này đã tồn tại chưa để tránh trùng lặp
        $exists = DB::table('semesters')->where('academic_year', $academicYearString)->exists();

        if ($exists) {
            $this->info("Năm học {$academicYearString} đã tồn tại. Không cần tạo thêm.");
            return;
        }

        $this->info("Đang tạo dữ liệu cho năm học: {$academicYearString}...");

        $data = [];
        $now = Carbon::now();

        // --- HỌC KỲ 1 (Tháng 8 năm Start -> Tháng 1 năm End) ---
        $data[] = [
            'code'          => "{$nextStartYear}_{$nextEndYear}_HK1",
            'name'          => 'Học kỳ 1',
            'academic_year' => $academicYearString,
            'start_date'    => "{$nextStartYear}-08-15",
            'end_date'      => "{$nextEndYear}-01-15",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        // --- HỌC KỲ 2 (Tháng 1 năm End -> Tháng 6 năm End) ---
        $data[] = [
            'code'          => "{$nextStartYear}_{$nextEndYear}_HK2",
            'name'          => 'Học kỳ 2',
            'academic_year' => $academicYearString,
            'start_date'    => "{$nextEndYear}-01-20",
            'end_date'      => "{$nextEndYear}-06-15",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        // --- HỌC KỲ 3 / HÈ (Tháng 6 năm End -> Tháng 8 năm End) ---
        $data[] = [
            'code'          => "{$nextStartYear}_{$nextEndYear}_HK3",
            'name'          => 'Học kỳ 3 (Hè)',
            'academic_year' => $academicYearString,
            'start_date'    => "{$nextEndYear}-06-20",
            'end_date'      => "{$nextEndYear}-08-05",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        DB::table('semesters')->insert($data);

        $this->info("Đã tạo thành công 3 học kỳ cho năm {$academicYearString}!");
    }
}