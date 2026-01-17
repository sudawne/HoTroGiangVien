<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('123456');

        $adminUser = User::create([
            'name' => 'Quản Trị Viên',
            'email' => 'admin@domain.com',
            'password' => $password,
            'role_id' => 1,
        ]);
        Lecturer::create(['user_id' => $adminUser->id, 'lecturer_code' => 'ADMIN']);

        $lecturers = [
            ['name' => 'TS. Nguyễn Văn A', 'email' => 'nguyenvana@domain.com', 'code' => 'GV001'],
            ['name' => 'ThS. Trần Thị B', 'email' => 'tranthib@domain.com', 'code' => 'GV002'],
            ['name' => 'TS. Lê Hoàng C', 'email' => 'lehoangc@domain.com', 'code' => 'GV003'],
            ['name' => 'GV. Phạm Minh D', 'email' => 'phamminhd@domain.com', 'code' => 'GV004'],
            ['name' => 'ThS. Hoàng Văn E', 'email' => 'hoangvane@domain.com', 'code' => 'GV005'],
            ['name' => 'TS. Ngô Thị F', 'email' => 'ngothif@domain.com', 'code' => 'GV006'],
        ];

        foreach ($lecturers as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'role_id' => 2,
            ]);

            Lecturer::create([
                'user_id' => $user->id,
                'lecturer_code' => $data['code']
            ]);
        }
    }
}
