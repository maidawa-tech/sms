<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marks_settings', function (Blueprint $table) {
            $table->id();

            // Foreign key to sections
            $table->foreignId('section_id')
                  ->constrained('sections', 'section_id')
                  ->onDelete('cascade')
                  ->comment('Section this marks setting belongs to');

            // Marks columns as unsigned decimal
            $table->decimal('max_ca1', 5, 2)->unsigned()->default(0)
                  ->comment('Maximum score allowed for CA1');
            $table->decimal('max_ca2', 5, 2)->unsigned()->default(0)
                  ->comment('Maximum score allowed for CA2');
            $table->decimal('max_exam', 5, 2)->unsigned()->default(0)
                  ->comment('Maximum score allowed for Exam');

            $table->timestamps();

            // Ensure only one setting per section
            $table->unique('section_id', 'unique_section_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks_settings');
    }
};
