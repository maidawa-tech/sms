<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('traits', function (Blueprint $table) {
            $table->id();
            $table->string('trait_name'); // e.g Attendance, Creativity
            $table->enum('trait_type', ['affective', 'psychomotor']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['trait_name', 'trait_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traits');
    }
};
