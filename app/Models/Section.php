<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
    protected $primaryKey = 'section_id';
    protected $fillable = [
        'section_name',
        'section_short_name',
    ];
    
    public function classes()
    {
        return $this->hasMany(ClassModel::class, 'section_id', 'section_id');
    }

}
