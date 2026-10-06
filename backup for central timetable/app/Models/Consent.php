<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;
    protected $fillable = [
        'consent_1_first_name',
        'consent_1_last_name',
        'consent_1_date',
        'how_did_you_hear',
        'consent_1signature',
        'family_id',
        'branch_id',
        'branch_name'
    ];
}
