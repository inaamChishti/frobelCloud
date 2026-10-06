<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferedSubject extends Model
{
    use HasFactory;
    protected $fillable = [
        'examboard',
        'qualification',
        'code',
        'description',
        'session'
    ];
}
