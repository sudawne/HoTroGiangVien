<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            // Thêm thông tin thời gian địa điểm
            $table->dateTime('held_at')->nullable()->after('title');
            $table->dateTime('ended_at')->nullable()->after('held_at');
            $table->string('location')->nullable()->after('ended_at');

            // Thêm nhân sự (Chủ trì & Thư ký)
            $table->unsignedBigInteger('monitor_id')->nullable()->after('created_by'); // Lớp trưởng
            $table->unsignedBigInteger('secretary_id')->nullable()->after('monitor_id'); // Thư ký
            
            // Tách nội dung thành 3 phần rõ ràng
            $table->longText('content_discussions')->nullable()->after('content'); // Mục II
            $table->longText('content_conclusion')->nullable()->after('content_discussions'); // Mục III
            $table->longText('content_requests')->nullable()->after('content_conclusion'); // Mục IV
            
            // Khóa ngoại
            $table->foreign('monitor_id')->references('id')->on('students')->nullOnDelete();
            $table->foreign('secretary_id')->references('id')->on('students')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            $table->dropForeign(['monitor_id']);
            $table->dropForeign(['secretary_id']);
            $table->dropColumn([
                'held_at', 'ended_at', 'location', 
                'monitor_id', 'secretary_id',
                'content_discussions', 'content_conclusion', 'content_requests'
            ]);
        });
    }
};