<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'reg_number',
        'section_id',
        'class_id',
        'arm_id',
        'session_id',
    ];

    public $primaryKey = 'id';

    public function student()
    {
        return $this->belongsTo(Student::class, 'reg_number', 'reg_number');
    }
}
