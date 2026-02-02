<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    use HasFactory;

    protected $table = 'student_subjects';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reg_number',
        'subject_id',
        'arm_id',
        'session_id',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'reg_number', 'reg_number');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function arm()
    {
        return $this->belongsTo(Arm::class, 'arm_id', 'arm_id');
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id', 'session_name');
    }
}
