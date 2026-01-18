<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Import Batches (Lô nhập liệu)
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('imported_by')->constrained('users');
            $table->string('name')->comment('VD: DS Cảnh báo HK1 25-26');
            $table->enum('type', ['warning', 'dropout', 'course_cancellation', 'training_point', 'result', 'debt']);
            $table->string('file_url')->nullable();
            $table->integer('total_records')->default(0);
            $table->enum('status', ['pending', 'published', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 2. Student Debts (Nợ môn)
        Schema::create('student_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('import_batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->string('course_code');
            $table->string('course_name');
            $table->integer('credits');
            $table->float('score')->nullable();
            $table->enum('status', ['owed', 'cleared'])->default('owed');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 3. Academic Warnings (Cảnh báo học vụ)
        Schema::create('academic_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('import_batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->integer('warning_level')->comment('1, 2, 3');
            $table->float('gpa_term');
            $table->float('gpa_cumulative');
            $table->integer('credits_owed');
            $table->integer('warning_count');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'processed', 'resolved'])->default('pending');
            $table->text('advisor_note')->nullable();
            $table->timestamps();
        });

        // 4. Course Cancellations (Hủy học phần)
        Schema::create('course_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('import_batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->string('course_code');
            $table->string('course_name');
            $table->integer('credits');
            $table->string('reason')->nullable();
            $table->boolean('is_notified')->default(false);
            $table->timestamps();
        });

        // 5. Academic Results (Kết quả học tập)
        Schema::create('academic_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('import_batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->float('gpa_10')->nullable();
            $table->float('gpa_4')->nullable();
            $table->integer('training_point')->nullable();
            $table->string('classification')->nullable();
            $table->float('accumulated_gpa_4')->nullable();
            $table->integer('accumulated_credits')->nullable();
            $table->boolean('is_warning')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_results');
        Schema::dropIfExists('course_cancellations');
        Schema::dropIfExists('academic_warnings');
        Schema::dropIfExists('student_debts');
        Schema::dropIfExists('import_batches');
    }
};
