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

        // 2. Cập nhật tất cả Giảng viên hiện có vào Khoa CNTT
        // (Giả định rằng User và Lecturer đã có từ file Dump như bạn nói)

        $updatedCount = Lecturer::whereNull('department_id') // Chỉ update những người chưa có khoa
            ->update(['department_id' => $department->id]);

        $this->command->info("Đã phân công {$updatedCount} giảng viên vào khoa CNTT.");

        // 3. (Tùy chọn) Nếu bạn muốn reset ID về 1 cho đẹp (chỉ chạy khi DB trống)
        // DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1;');
    }
}
