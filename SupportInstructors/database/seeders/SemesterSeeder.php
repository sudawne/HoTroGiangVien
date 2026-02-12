<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('semesters')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $startYear = 2024;
        $totalYears = 2; 
        $data = [];
        $now = Carbon::now();

        for ($i = 0; $i < $totalYears; $i++) {
            $currentYear = $startYear + $i;     // VD: 2023
            $nextYear    = $currentYear + 1;    // VD: 2024
            $academicYearString = "{$currentYear}-{$nextYear}";

            // --- HỌC KỲ 1: Thường từ tháng 8/9 -> tháng 1 năm sau ---
            $hk1Start = "{$currentYear}-08-15";
            $hk1End   = "{$nextYear}-01-15";
            
            $data[] = [
                'code'          => "{$currentYear}_{$nextYear}_HK1", // Code nên kẹp cả 2 năm để unique dễ hơn
                'name'          => 'Học kỳ 1',
                'academic_year' => $academicYearString,
                'start_date'    => $hk1Start,
                'end_date'      => $hk1End,
                'is_current'    => $now->between($hk1Start, $hk1End),
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            // --- HỌC KỲ 2: Từ tháng 1 -> tháng 6 năm sau ---
            $hk2Start = "{$nextYear}-01-20";
            $hk2End   = "{$nextYear}-06-15";

            $data[] = [
                'code'          => "{$currentYear}_{$nextYear}_HK2",
                'name'          => 'Học kỳ 2',
                'academic_year' => $academicYearString,
                'start_date'    => $hk2Start,
                'end_date'      => $hk2End,
                'is_current'    => $now->between($hk2Start, $hk2End),
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            // --- HỌC KỲ HÈ (HK3): Tháng 6 -> Tháng 8 ---
            $hk3Start = "{$nextYear}-06-20";
            $hk3End   = "{$nextYear}-08-05";

            $data[] = [
                'code'          => "{$currentYear}_{$nextYear}_HK3",
                'name'          => 'Học kỳ 3 (Hè)',
                'academic_year' => $academicYearString,
                'start_date'    => $hk3Start,
                'end_date'      => $hk3End,
                'is_current'    => $now->between($hk3Start, $hk3End),
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        DB::table('semesters')->insert($data);
    }
}