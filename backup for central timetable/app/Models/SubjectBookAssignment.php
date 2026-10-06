<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectBookAssignment extends Model
{
    use HasFactory;
     protected $fillable = [
        'book_name',
        'subject_name',
        'branch_name',
        'price',
        'branch_id'
    ];
}
