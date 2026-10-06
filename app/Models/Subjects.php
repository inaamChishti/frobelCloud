<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;
    protected $fillable = [
        'examBoard',
        'courseType',
        'subjectName',
        'tier',
        'subjectCode',
        'examSession',
        'examTime',
        'examDate',
        'examDay',
        'duration',
        'nea',
        'slot'
    ];
}
