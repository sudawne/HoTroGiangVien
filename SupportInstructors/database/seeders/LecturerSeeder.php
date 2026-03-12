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
    }
}
