<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->string('reg_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('surname', 100);
            $table->string('other_name', 100)->nullable();
            $table->string('parent_phone', 20);
            $table->text('address');
            $table->string('passport', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
