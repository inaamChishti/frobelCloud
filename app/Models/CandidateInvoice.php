<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateInvoice extends Model
{
    use HasFactory;

     protected $fillable = [
        'request_id',
        'family_id',
        'email',
        'candidate_name',
        'subjects',
        'total_fee',
        'payment_deadline',
    ];

    protected $casts = [
        'subjects' => 'array',
        'payment_deadline' => 'date',
    ];
}
