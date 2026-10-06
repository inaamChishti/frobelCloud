<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class request_comments extends Model
{
    use HasFactory;
    protected $fillable = ['student_request_id', 'user_id', 'comment', 'session'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentRequest()
    {
        return $this->belongsTo(StudentRequest::class);
    }


}
