<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Thêm cột status (nếu chưa có) vì Controller đang cần dùng
            if (!Schema::hasColumn('notifications', 'status')) {
                $table->enum('status', ['draft', 'pending', 'approved'])->default('draft')->after('class_id');
            }

            // Thêm cột allow_comments
            if (!Schema::hasColumn('notifications', 'allow_comments')) {
                $table->boolean('allow_comments')->default(true)->after('class_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'allow_comments')) {
                $table->dropColumn('allow_comments');
            }
            
            if (Schema::hasColumn('notifications', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};