<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarksSetting extends Model
{
    protected $table = 'marks_settings';

    // Allow mass assignment for section_id too
    protected $fillable = [
        'section_id',  
        'max_ca1',
        'max_ca2',
        'max_exam',
    ];

    // Cast numeric values to float automatically
    protected $casts = [
        'max_ca1' => 'float',
        'max_ca2' => 'float',
        'max_exam' => 'float',
    ];

    // Optional: relation to Section model
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }
}
