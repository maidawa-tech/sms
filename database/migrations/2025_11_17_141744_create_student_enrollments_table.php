<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();

            $table->string('reg_number'); // FK to students.reg_number
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('arm_id');
            $table->string('session_id'); // Example: 2024/2025

            $table->timestamps();

            // Foreign keys
            $table->foreign('reg_number')->references('reg_number')->on('students')->onDelete('cascade');
            $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
            $table->foreign('arm_id')->references('arm_id')->on('arms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
