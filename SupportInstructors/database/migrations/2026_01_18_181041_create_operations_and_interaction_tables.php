<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Meeting Minutes (Biên bản họp)
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('content')->nullable();
            $table->integer('attendees_count')->default(0);
            $table->json('absent_list')->nullable();
            $table->string('file_url')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        // 2. Consultation Logs (Nhật ký tư vấn)
        Schema::create('consultation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('advisor_id')->constrained('lecturers');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->string('topic');
            $table->text('content')->nullable();
            $table->text('solution')->nullable();
            $table->timestamps();
        });

        // 3. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            // related_batch_id nullable vì không phải thông báo nào cũng từ batch
            // Lưu ý: bảng import_batches phải tồn tại trước (đã tạo ở bước 4)
            $table->unsignedBigInteger('related_batch_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'urgent', 'batch_alert']);
            $table->timestamps();

            $table->foreign('related_batch_id')->references('id')->on('import_batches')->onDelete('set null');
        });

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 4. Chat System
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['private', 'group']);
            $table->string('name')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users');
            $table->text('content')->nullable();
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('consultation_logs');
        Schema::dropIfExists('meeting_minutes');
    }
};
