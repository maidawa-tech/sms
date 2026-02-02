<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'student_id';
    protected $fillable = [
        'reg_number', 'first_name', 'surname', 'other_name', 'parent_phone', 'address', 'passport'
    ];

    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->other_name.' '.$this->surname);
    }
}
