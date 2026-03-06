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
        // 1. Nếu bảng đã tồn tại thì XÓA trước (để reset cấu trúc)
        Schema::dropIfExists('academic_results');

        // 2. Tạo bảng mới gọn nhẹ theo yêu cầu
        Schema::create('academic_results', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            
            // 3 trường dữ liệu quan trọng
            $table->float('gpa_10')->nullable()->comment('Điểm TB Hệ 10');
            $table->float('gpa_4')->nullable()->comment('Điểm TB Hệ 4');
            $table->string('classification')->nullable()->comment('Xếp loại học lực (Giỏi, Khá...)');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_results');
    }
};