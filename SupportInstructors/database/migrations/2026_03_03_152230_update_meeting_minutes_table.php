<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {

            // 1. Thêm thông tin thời gian địa điểm
            if (!Schema::hasColumn('meeting_minutes', 'held_at')) {
                $table->dateTime('held_at')->nullable()->after('title');
            }

            if (!Schema::hasColumn('meeting_minutes', 'ended_at')) {
                // Lưu ý: Nếu cột held_at vừa tạo ở trên, lệnh này sẽ chạy ổn. 
                // Nếu held_at đã có từ trước, nó sẽ chèn sau held_at.
                $table->dateTime('ended_at')->nullable()->after('held_at');
            }

            if (!Schema::hasColumn('meeting_minutes', 'location')) {
                $table->string('location')->nullable()->after('ended_at');
            }

            // 2. Thêm nhân sự (Chủ trì & Thư ký)
            if (!Schema::hasColumn('meeting_minutes', 'monitor_id')) {
                $table->unsignedBigInteger('monitor_id')->nullable()->after('created_by'); // Lớp trưởng
                $table->foreign('monitor_id')->references('id')->on('students')->nullOnDelete();
            }

            if (!Schema::hasColumn('meeting_minutes', 'secretary_id')) {
                $table->unsignedBigInteger('secretary_id')->nullable()->after('monitor_id'); // Thư ký
                $table->foreign('secretary_id')->references('id')->on('students')->nullOnDelete();
            }

            // 3. Tách nội dung thành 3 phần rõ ràng
            if (!Schema::hasColumn('meeting_minutes', 'content_discussions')) {
                $table->longText('content_discussions')->nullable()->after('content'); // Mục II
            }

            if (!Schema::hasColumn('meeting_minutes', 'content_conclusion')) {
                $table->longText('content_conclusion')->nullable()->after('content_discussions'); // Mục III
            }

            if (!Schema::hasColumn('meeting_minutes', 'content_requests')) {
                $table->longText('content_requests')->nullable()->after('content_conclusion'); // Mục IV
            }
        });
    }

    public function down()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            // Xóa khóa ngoại trước
            // Kiểm tra xem khóa ngoại có tồn tại không trước khi xóa để tránh lỗi (tùy chọn, nhưng an toàn)
            // Laravel thường tự xử lý tên index, nhưng nếu cần chắc chắn:
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('meeting_minutes');

            if (array_key_exists('meeting_minutes_monitor_id_foreign', $indexes)) {
                $table->dropForeign(['monitor_id']);
            }
            if (array_key_exists('meeting_minutes_secretary_id_foreign', $indexes)) {
                $table->dropForeign(['secretary_id']);
            }

            // Xóa các cột
            $columnsToDrop = [
                'held_at',
                'ended_at',
                'location',
                'monitor_id',
                'secretary_id',
                'content_discussions',
                'content_conclusion',
                'content_requests'
            ];

            // Chỉ xóa cột nào thực sự tồn tại
            $existingColumns = [];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('meeting_minutes', $col)) {
                    $existingColumns[] = $col;
                }
            }

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
