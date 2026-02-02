<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id('subject_id'); // Auto increment primary key
            $table->string('subject_name', 100);
            $table->string('short_name', 20)->nullable();
            $table->timestamps(); // optional: adds created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
