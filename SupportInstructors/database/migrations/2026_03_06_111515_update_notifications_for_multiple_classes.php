<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tạo bảng trung gian nếu chưa có
        if (!Schema::hasTable('class_notification')) {
            Schema::create('class_notification', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
                $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            });
        }

        // 2. Xóa Khóa ngoại (nếu còn tồn tại)
        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign(['class_id']);
            });
        } catch (\Exception $e) {
            // Bỏ qua nếu khóa ngoại đã bị xóa từ lần chạy trước
        }

        // 3. Xóa Index (nếu còn tồn tại)
        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex(['class_id']);
            });
        } catch (\Exception $e) {
            // Bỏ qua nếu Index không tồn tại (lỗi bạn vừa gặp)
        }

        // 4. Xóa cột class_id một cách an toàn
        if (Schema::hasColumn('notifications', 'class_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('class_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_notification');
        if (!Schema::hasColumn('notifications', 'class_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('class_id')->nullable();
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            });
        }
    }
};
