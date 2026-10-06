<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'startTime',
        'endTime',
        'subject',
        'branch_name','branch_id',
    ];
}
