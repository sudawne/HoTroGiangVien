<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'secretary_id')) {
                $table->foreignId('secretary_id')->nullable()->after('monitor_id'); // Code cũ của bạn
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['secretary_id']);
            $table->dropColumn('secretary_id');
        });
    }
};
