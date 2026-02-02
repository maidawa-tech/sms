<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // Foreign key to sections table - required and indexed for performance
            $table->unsignedBigInteger('section_id')->index(); 
            
            // Score range with precision for decimal values
            $table->decimal('min_score', 5, 2); // e.g., 0.00 to 100.00
            $table->decimal('max_score', 5, 2); // e.g., 0.00 to 100.00
            
            // Grade letter (A, B+, C, etc.)
            $table->string('grade_letter', 5);
            
            // Remark/description
            $table->string('remark', 100);

            // Grading type: false = Custom, true = WAEC
            $table->boolean('is_waec')->default(false);

            $table->timestamps();

            // Composite unique constraint: Prevent duplicate grade letters 
            // within same section and grading type
            $table->unique(['section_id', 'grade_letter', 'is_waec'], 
                         'unique_grade_per_section_type');

            // Foreign Key Constraint with cascade delete
            $table->foreign('section_id')
                ->references('section_id')
                ->on('sections')
                ->onDelete('cascade');
        });

        // Add index for faster filtering by grading type
        Schema::table('grades', function (Blueprint $table) {
            $table->index(['section_id', 'is_waec'], 'idx_section_grading_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Drop custom indexes first
            $table->dropIndex('idx_section_grading_type');
        });
        
        Schema::dropIfExists('grades');
    }
};