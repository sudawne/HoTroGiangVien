<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('training_points', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
        $table->integer('self_score')->nullable();
        $table->integer('class_score')->nullable();
        $table->integer('advisor_score')->nullable();
        $table->integer('final_score')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_points');
    }
};
