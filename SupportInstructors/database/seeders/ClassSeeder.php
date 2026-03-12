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
    }
}
