<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_read_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('alert_id'); // Lưu trữ dạng: 'c_1', 'l_2', 'p_3'
            $table->timestamp('read_at')->useCurrent();

            // Ràng buộc không cho trùng lặp
            $table->unique(['user_id', 'alert_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_read_alerts');
    }
};
