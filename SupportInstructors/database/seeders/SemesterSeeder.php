<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        DB::table('semesters')->insert([
            [
                'code' => '2025_HK1',
                'name' => 'Học kỳ 1',
                'academic_year' => '2025-2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-01-15',
                'is_current' => true, // Đánh dấu đây là học kỳ hiện tại
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '2025_HK2',
                'name' => 'Học kỳ 2',
                'academic_year' => '2025-2026',
                'start_date' => '2026-01-20',
                'end_date' => '2026-06-30',
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}