<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_average_comments', function (Blueprint $table) {
            $table->id();

            // Score range
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);

            // Comment text
            $table->string('comment', 255);

            // Optional status flag (future-proofing)
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes for fast lookup
            $table->index(['min_score', 'max_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_average_comments');
    }
};
