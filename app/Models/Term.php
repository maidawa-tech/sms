<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $primaryKey = 'term_id';
    protected $fillable = [
        'term_name',
        'session_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    // Relationship: A term belongs to an academic session
    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
