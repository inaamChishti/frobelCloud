<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermBreak extends Model
{
    use HasFactory;
     protected $fillable = [
        'start_term_break',
        'end_term_break',
        'branch_name',
        'branch_id',
    ];
}
