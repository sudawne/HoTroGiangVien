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
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('subject_code')->comment('Mã học phần');
            $table->string('subject_name')->comment('Tên học phần');
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