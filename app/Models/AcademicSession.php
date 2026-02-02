<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $primaryKey = 'session_id';
    protected $fillable = [
        'session_name',
        'is_active',
        'start_date',
        'end_date',
    ];

    // One academic session can have many terms
    public function terms()
    {
        return $this->hasMany(Term::class, 'session_id');
    }
}
