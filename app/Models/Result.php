<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reg_number',
        'enrollment_id',
        'subject_id',
        'term_id',
        'session_id',
        'ca1',
        'ca2',
        'exam',
        'total',
        'grade',
        'remark',
        'position_in_subject',
        'average',
        'overall_position',
    ];

    /**
     * Relationships
     */

    public function student()
    {
        return $this->belongsTo(Student::class, 'reg_number', 'reg_number');
    }

    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }
}
