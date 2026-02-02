<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'min_score',
        'max_score',
        'grade_letter',
        'remark',
        'is_waec',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'min_score' => 'float',
        'max_score' => 'float',
        'is_waec' => 'boolean',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate score ranges and prevent overlaps before saving
        static::saving(function ($grade) {
            // Ensure max_score is greater than or equal to min_score
            if ($grade->max_score < $grade->min_score) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], [])->errors()->add('max_score', 'Max score must be greater than or equal to min score')
                );
            }

            // Validate score ranges (0-100)
            if ($grade->min_score < 0 || $grade->max_score > 100) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], [])->errors()->add('min_score', 'Scores must be between 0 and 100')
                );
            }

            // Check for overlapping score ranges in same section and grading type
            $overlap = self::where('section_id', $grade->section_id)
                ->where('is_waec', $grade->is_waec)
                ->when($grade->exists, function ($query) use ($grade) {
                    return $query->where('id', '!=', $grade->id);
                })
                ->where(function ($query) use ($grade) {
                    $query->where(function ($q) use ($grade) {
                        $q->where('min_score', '<=', $grade->min_score)
                          ->where('max_score', '>=', $grade->min_score);
                    })->orWhere(function ($q) use ($grade) {
                        $q->where('min_score', '<=', $grade->max_score)
                          ->where('max_score', '>=', $grade->max_score);
                    })->orWhere(function ($q) use ($grade) {
                        $q->where('min_score', '>=', $grade->min_score)
                          ->where('max_score', '<=', $grade->max_score);
                    });
                })
                ->exists();

            if ($overlap) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], [])->errors()->add('min_score', 'Score range overlaps with existing grade in the same section and grading type')
                );
            }

            // Ensure grade_letter is unique within section and grading type
            $duplicate = self::where('section_id', $grade->section_id)
                ->where('is_waec', $grade->is_waec)
                ->where('grade_letter', $grade->grade_letter)
                ->when($grade->exists, function ($query) use ($grade) {
                    return $query->where('id', '!=', $grade->id);
                })
                ->exists();

            if ($duplicate) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], [])->errors()->add('grade_letter', 'Grade letter already exists for this section and grading type')
                );
            }
        });
    }

    /**
     * Relationship with Section
     * Assuming Section model uses 'section_id' as primary key
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * Scope for WAEC grades
     */
    public function scopeWaec($query)
    {
        return $query->where('is_waec', true);
    }

    /**
     * Scope for Custom grades
     */
    public function scopeCustom($query)
    {
        return $query->where('is_waec', false);
    }

    /**
     * Scope for specific section
     */
    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Scope for specific grading type
     */
    public function scopeOfType($query, $isWaec)
    {
        return $query->where('is_waec', $isWaec);
    }

    /**
     * Check if grade is for SSS section
     */
    public function isForSssSection()
    {
        return $this->section && $this->section->section_short_name === 'SSS';
    }

    /**
     * Get grade type label
     */
    public function getGradeTypeLabelAttribute()
    {
        return $this->is_waec ? 'WAEC' : 'Custom';
    }

    /**
     * Get grade type with icon for display
     */
    public function getGradeTypeDisplayAttribute()
    {
        if ($this->is_waec) {
            return '<i class="fa-solid fa-file-certificate text-primary"></i> WAEC';
        }
        return '<i class="fa-solid fa-gear text-success"></i> Custom';
    }

    /**
     * Get formatted score range
     */
    public function getScoreRangeAttribute()
    {
        return $this->min_score . ' - ' . $this->max_score;
    }

    /**
     * Check if a score falls within this grade's range
     */
    public function isScoreInRange($score)
    {
        return $score >= $this->min_score && $score <= $this->max_score;
    }

    /**
     * Find grade for a given score in specific section and type
     */
    public static function findGradeForScore($score, $sectionId, $isWaec = false)
    {
        return self::where('section_id', $sectionId)
            ->where('is_waec', $isWaec)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }

    /**
     * Get all grades for section grouped by type
     */
    public static function getBySectionGrouped($sectionId)
    {
        return self::where('section_id', $sectionId)
            ->orderBy('is_waec')
            ->orderBy('min_score', 'desc')
            ->get()
            ->groupBy('is_waec');
    }

    /**
     * Validation rules for reuse in controller
     */
    public static function validationRules($id = null)
    {
        return [
            'section_id'   => 'required|exists:sections,section_id',
            'min_score'    => 'required|numeric|min:0|max:100',
            'max_score'    => 'required|numeric|min:0|max:100|gte:min_score',
            'grade_letter' => 'required|string|max:5',
            'remark'       => 'required|string|max:100',
            'is_waec'      => 'required|boolean',
        ];
    }

    /**
     * Custom error messages for validation
     */
    public static function validationMessages()
    {
        return [
            'max_score.gte' => 'Maximum score must be greater than or equal to minimum score.',
            'grade_letter.unique' => 'This grade letter already exists for this section and grading type.',
        ];
    }
}