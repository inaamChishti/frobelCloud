<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAndUciNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        'candidate_id',
        'uci_number',
        'candidate_name',
        'email',
        'is_sent',
        'session',
    ];
}
