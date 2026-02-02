<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arm extends Model
{
    use HasFactory;

    protected $primaryKey = 'arm_id';

    protected $fillable = [
        'class_id',
        'arm_name',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
