<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('reg_number'); // FK to students
            $table->unsignedBigInteger('subject_id'); // FK to subjects
            $table->unsignedBigInteger('arm_id'); // optional, for reference
            $table->string('session_id'); // e.g., 2024/2025
            $table->timestamps();

            $table->foreign('reg_number')->references('reg_number')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->foreign('arm_id')->references('arm_id')->on('arms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};
