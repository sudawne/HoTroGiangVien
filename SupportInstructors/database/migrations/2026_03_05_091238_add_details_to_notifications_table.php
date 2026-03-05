<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // 1. Cột lưu File
            $table->string('attachment_url')->nullable()->after('type');
            $table->string('attachment_name')->nullable()->after('attachment_url');

            // 2. Cột Đối tượng nhận
            $table->string('target_audience')->default('all')->after('attachment_name')->comment('all (Toàn trường), class (Chỉ một lớp)');
            $table->unsignedBigInteger('class_id')->nullable()->after('target_audience');

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['attachment_url', 'attachment_name', 'target_audience', 'class_id']);
        });
    }
};
