<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Student;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $dept = Department::firstOrCreate(
            ['code' => 'TT&TT'],
            ['name' => 'Thông tin & Truyền thông']
        );

        // Lấy hoặc tạo một Giảng viên mặc định để gán làm Cố vấn
        $advisor = Lecturer::first() ?? Lecturer::factory()->create(); 
        
        // 2. Danh sách các lớp bạn muốn thêm
        $classes = [
            ['code' => 'B022TT1', 'name' => 'Công nghệ thông tin 1'],
            ['code' => 'B022TT2', 'name' => 'Công nghệ thông tin 2'], 
            ['code' => 'B022TT3', 'name' => 'Công nghệ thông tin 3'], 
            ['code' => 'B022TT4', 'name' => 'Công nghệ thông tin 4'], 
        ];

        foreach ($classes as $class) {
            Classes::firstOrCreate(
                ['code' => $class['code']], // Kiểm tra nếu mã lớp đã có thì không thêm nữa
                [
                    'name' => $class['name'],
                    'department_id' => $dept->id,
                    'advisor_id' => $advisor->id,
                    'academic_year' => '2022-2026', // Ví dụ khóa 2022
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        echo "Đã seed xong danh sách lớp học!\n";
    }
}