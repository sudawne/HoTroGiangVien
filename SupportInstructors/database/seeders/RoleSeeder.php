<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Role::create(['id' => 1, 'name' => 'ADMIN']);
        Role::create(['id' => 2, 'name' => 'LECTURER']);
        Role::create(['id' => 3, 'name' => 'STUDENT']);
    }
}
