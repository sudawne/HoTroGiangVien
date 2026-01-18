<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tạo bảng Classes
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('advisor_id')->constrained('lecturers'); // CVHT
            // Monitor ID (Lớp trưởng) sẽ update sau hoặc để nullable để tránh lỗi vòng lặp lúc insert
            $table->foreignId('monitor_id')->nullable()->constrained('students');
            $table->string('code')->unique()->comment('20DTHA2');
            $table->string('name');
            $table->string('academic_year');
            $table->timestamps();
        });

        // 2. Cập nhật bảng Students (Thêm các cột mới theo thiết kế)
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('user_id')->constrained('classes');
            // student_code đã có, bỏ qua
            $table->string('fullname')->after('student_code');
            $table->date('dob')->nullable()->after('fullname');
            $table->string('pob')->nullable()->after('dob')->comment('Nơi sinh');
            $table->enum('status', ['studying', 'reserved', 'dropped', 'graduated'])->default('studying')->after('pob');
            $table->integer('enrollment_year')->nullable()->after('status');
        });

        // 3. Tạo bảng Student Relatives
        Schema::create('student_relatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('fullname');
            $table->string('relationship')->comment('Bố, Mẹ');
            $table->string('phone')->comment('SĐT Khẩn cấp');
            $table->string('address')->nullable();
            $table->boolean('is_emergency_contact')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_relatives');

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id', 'fullname', 'dob', 'pob', 'status', 'enrollment_year']);
        });

        Schema::dropIfExists('classes');
    }
};
