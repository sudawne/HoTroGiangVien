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
        // Tìm học kỳ mới nhất đang có trong Database
        $latestSemester = DB::table('semesters')
                            ->orderBy('start_date', 'desc')
                            ->first();

        // Xác định năm bắt đầu tiếp theo
        if ($latestSemester) {
            $parts = explode('-', $latestSemester->academic_year);
            $lastStartYear = (int) $parts[0]; 
            $nextStartYear = $lastStartYear + 1;
        } else {
            $nextStartYear = Carbon::now()->year;
        }
        $nextEndYear = $nextStartYear + 1; 
        $academicYearString = "{$nextStartYear}-{$nextEndYear}"; 

        // Kiểm tra xem năm này đã tồn tại chưa
        $exists = DB::table('semesters')->where('academic_year', $academicYearString)->exists();
        if ($exists) {
            $this->info("Năm học {$academicYearString} đã tồn tại. Không cần tạo thêm.");
            return;
        }
        $this->info("Đang tạo dữ liệu cho năm học: {$academicYearString}...");
        $data = [];
        $now = Carbon::now();

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
        $data[] = [
            'code'          => "{$nextStartYear}_{$nextEndYear}_HK3",
            'name'          => 'Học kỳ 3',
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