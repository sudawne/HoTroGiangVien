<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cập nhật Users
        Schema::table('users', function (Blueprint $table) {
            // Role_id đã có trong dump cũ, không cần thêm
            $table->string('username')->unique()->nullable()->after('role_id')->comment('Mã SV / Mã GV'); // Thêm nullable vì dữ liệu cũ chưa có
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar_url');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes(); // deleted_at
        });

        // Cập nhật Lecturers
        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('user_id')->constrained('departments');
            $table->string('degree')->nullable()->after('lecturer_code')->comment('ThS, TS');
            $table->string('position')->nullable()->after('degree');
        });
    }

    public function down()
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'degree', 'position']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'avatar_url', 'is_active', 'last_login_at', 'deleted_at']);
        });
    }
};
