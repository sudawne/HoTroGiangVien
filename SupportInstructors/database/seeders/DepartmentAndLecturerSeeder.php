<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;

class DepartmentAndLecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo Khoa CNTT (Dùng updateOrCreate để chạy nhiều lần không bị lỗi trùng lặp)
        $department = Department::updateOrCreate(
            ['code' => 'CNTT'], // Điều kiện tìm kiếm
            ['name' => 'Khoa Thông tin và Truyền thông'] // Dữ liệu cập nhật/tạo mới
        );

        $this->command->info('Đã tạo/cập nhật Khoa: ' . $department->name);
    }
}
