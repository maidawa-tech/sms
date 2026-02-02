<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';

    protected $fillable = [
        'section_id',
        'class_name',
        'class_short_name',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }
    
    public function arms()
    {
        return $this->hasMany(Arm::class, 'class_id', 'class_id');
    }
}
