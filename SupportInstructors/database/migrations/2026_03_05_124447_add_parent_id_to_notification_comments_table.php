<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_comments', function (Blueprint $table) {
            // Thêm parent_id để tạo cấu trúc cây (Reply)
            if (!Schema::hasColumn('notification_comments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('notification_comments')->cascadeOnDelete()->after('notification_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notification_comments', function (Blueprint $table) {
            if (Schema::hasColumn('notification_comments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};
