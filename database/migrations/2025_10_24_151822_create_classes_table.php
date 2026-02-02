<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id('class_id');
            $table->unsignedBigInteger('section_id');
            $table->string('class_name', 100);
            $table->string('class_short_name', 20);
            $table->timestamps();

            $table->foreign('section_id')
                  ->references('section_id')
                  ->on('sections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
