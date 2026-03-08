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
        // THÊM DÒNG NÀY: Xóa bảng nếu đã tồn tại để tránh lỗi trùng lặp
        Schema::dropIfExists('course_cancellations');

        Schema::create('course_cancellations', function (Blueprint $table) {
            $table->id();
            // Liên kết sinh viên
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // Liên kết học kỳ
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            
            // Liên kết môn học (Quan trọng: Bảng subjects phải có trước)
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            
            // Lý do (Mặc định: Nợ học phí)
            $table->string('reason')->default('Nợ học phí')->comment('Lý do xóa');
            
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