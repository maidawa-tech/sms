<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->string('reg_number'); // FK
            $table->unsignedBigInteger('enrollment_id'); // FK

            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('term_id');
            $table->string('session_id');

            $table->decimal('ca1', 5, 2)->nullable();
            $table->decimal('ca2', 5, 2)->nullable();
            $table->decimal('exam', 5, 2)->nullable();
            $table->decimal('total', 6, 2)->nullable();

            $table->string('grade', 5)->nullable();
            $table->string('remark', 100)->nullable();

            // Subject ranking
            $table->integer('position_in_subject')->nullable();

            // Overall rankings
            $table->decimal('average', 6, 2)->nullable();
            $table->integer('overall_position')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('reg_number')->references('reg_number')->on('students')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('student_enrollments')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->foreign('term_id')->references('term_id')->on('terms')->onDelete('cascade');

            // index for fast lookup
            $table->index(['enrollment_id','subject_id','term_id','session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
