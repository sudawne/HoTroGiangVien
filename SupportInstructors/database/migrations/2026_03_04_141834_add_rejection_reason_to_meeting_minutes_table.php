<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            // Thêm cột lý do từ chối, cho phép null (khi chưa bị từ chối)
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
