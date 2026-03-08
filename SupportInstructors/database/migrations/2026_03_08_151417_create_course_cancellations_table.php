<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm dòng này để rọn dẹp bảng cũ đang bị kẹt trong DB
        Schema::dropIfExists('course_cancellations');

        // 2. Chạy lệnh tạo bảng như bình thường
        Schema::create('course_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();

            // Cột khóa ngoại trỏ tới bảng môn học
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            $table->string('reason')->default('Nợ học phí')->comment('Lý do xóa');
            $table->decimal('debt_amount', 15, 0)->nullable()->comment('Số tiền nợ (nếu có)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_cancellations');
    }
};
