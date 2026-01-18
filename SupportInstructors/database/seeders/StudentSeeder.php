<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mật khẩu chung cho tất cả sinh viên
        $password = Hash::make('123456');

        // Danh sách sinh viên mẫu (Demo Data)
        $students = [
            ['name' => 'Nguyễn Thị Lan', 'code' => '20110001'],
            ['name' => 'Trần Văn Hùng', 'code' => '20110002'],
            ['name' => 'Lê Thị Mai', 'code' => '20110003'],
            ['name' => 'Phạm Quốc Bảo', 'code' => '20110004'],
            ['name' => 'Hoàng Minh Tuấn', 'code' => '20110005'],
            ['name' => 'Đỗ Thu Hà', 'code' => '20110006'],
            ['name' => 'Vũ Đức Thắng', 'code' => '20110007'],
            ['name' => 'Bùi Phương Thảo', 'code' => '20110008'],
            ['name' => 'Đặng Văn Lâm', 'code' => '20110009'],
            ['name' => 'Ngô Thị Ngọc', 'code' => '20110010'],
            ['name' => 'Dương Văn Khánh', 'code' => '20110011'],
            ['name' => 'Lý Thị Hương', 'code' => '20110012'],
            ['name' => 'Mai Văn Đạt', 'code' => '20110013'],
            ['name' => 'Trương Thị Yến', 'code' => '20110014'],
            ['name' => 'Nguyễn Văn A', 'code' => '20110452'],
        ];

        foreach ($students as $data) {
            // 1. Tạo tài khoản User cho sinh viên
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['code'] . '@st.domain.com', // Email theo mã SV
                'password' => $password,
                'role_id' => 3, // Giả sử ID 3 là role STUDENT
            ]);

            // 2. Tạo thông tin chi tiết Student
            Student::create([
                'user_id' => $user->id,
                'student_code' => $data['code'],
                'fullname' => $data['name'], // <-- QUAN TRỌNG: Gán tên vào fullname
                'enrollment_year' => 2020,   // Giá trị mặc định (nếu cần)
                'status' => 'studying',      // Trạng thái mặc định (nếu cần)
            ]);
        }
    }
}
