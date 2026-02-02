<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arms', function (Blueprint $table) {
            $table->id('arm_id');
            $table->unsignedBigInteger('class_id');
            $table->string('arm_name', 50);
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('class_id')
                  ->references('class_id')
                  ->on('classes')
                  ->onDelete('cascade');

            // Add unique constraint (no duplicate arm per class)
            $table->unique(['class_id', 'arm_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arms');
    }
};
