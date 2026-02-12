<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateNextYearSemesters extends Command
{
    protected $signature = 'app:generate-semesters';

    // Mô tả lệnh
    protected $description = 'Tự động kiểm tra và tạo dữ liệu cho năm học tiếp theo nếu chưa có';

    public function handle()
    {
        // 1. Xác định năm cần tạo (Là năm sau của thời điểm hiện tại)
        $nextYear = Carbon::now()->addYear()->year; // Ví dụ: Giờ là 2026 -> Cần tạo 2027
        $futureYear = $nextYear + 1; // 2028
        
        $academicYearString = "{$nextYear}-{$futureYear}";
        $hk1Code = "{$nextYear}_{$futureYear}_HK1";

        // 2. Kiểm tra xem năm này đã có trong CSDL chưa
        $exists = DB::table('semesters')->where('code', $hk1Code)->exists();

        if ($exists) {
            $this->info("Năm học {$academicYearString} đã tồn tại. Không cần tạo thêm.");
            return;
        }

        $this->info("Đang tạo dữ liệu cho năm học: {$academicYearString}...");

        // 3. Cấu hình ngày tháng (Logic giống Seeder chuẩn)
        $data = [];
        $now = Carbon::now();

        // --- HỌC KỲ 1 ---
        $data[] = [
            'code'          => "{$nextYear}_{$futureYear}_HK1",
            'name'          => 'Học kỳ 1',
            'academic_year' => $academicYearString,
            'start_date'    => "{$nextYear}-08-15",
            'end_date'      => "{$futureYear}-01-15",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        // --- HỌC KỲ 2 ---
        $data[] = [
            'code'          => "{$nextYear}_{$futureYear}_HK2",
            'name'          => 'Học kỳ 2',
            'academic_year' => $academicYearString,
            'start_date'    => "{$futureYear}-01-20",
            'end_date'      => "{$futureYear}-06-15",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        // --- HỌC KỲ 3 (HÈ) ---
        $data[] = [
            'code'          => "{$nextYear}_{$futureYear}_HK3",
            'name'          => 'Học kỳ 3 (Hè)',
            'academic_year' => $academicYearString,
            'start_date'    => "{$futureYear}-06-20",
            'end_date'      => "{$futureYear}-08-05",
            'is_current'    => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        DB::table('semesters')->insert($data);

        $this->info("Đã tạo thành công 3 học kỳ cho năm {$academicYearString}!");
    }
}