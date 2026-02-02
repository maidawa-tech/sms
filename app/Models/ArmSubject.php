<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArmSubject extends Model
{
    use HasFactory;

    protected $table = 'arm_subjects';

    protected $fillable = [
        'arm_id',
        'subject_id',
        'teacher_id'
    ];

    public function arm()
    {
        return $this->belongsTo(Arm::class, 'arm_id', 'arm_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }
}
