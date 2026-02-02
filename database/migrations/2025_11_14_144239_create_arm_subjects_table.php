<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arm_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arm_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->timestamps();

            $table->foreign('arm_id')->references('arm_id')->on('arms')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null'); // optional
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arm_subjects');
    }
};
