<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('CNTT, KINH_TE');
            $table->string('name');
            $table->timestamps();
        });

        // 2. Semesters
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('2025_HK1');
            $table->string('name');
            $table->string('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('departments');
    }
};
