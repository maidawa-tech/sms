<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraitModel extends Model
{
    protected $table = 'traits';

    protected $fillable = [
        'trait_name',
        'trait_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Query scopes
    public function scopeAffective($query)
    {
        return $query->where('trait_type', 'affective');
    }

    public function scopePsychomotor($query)
    {
        return $query->where('trait_type', 'psychomotor');
    }
}
