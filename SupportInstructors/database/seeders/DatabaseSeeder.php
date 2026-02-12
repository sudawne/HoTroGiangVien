<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            LecturerSeeder::class,
            StudentSeeder::class,
            DepartmentAndLecturerSeeder::class,
            SemesterSeeder::class,
        ]);
    }
}
