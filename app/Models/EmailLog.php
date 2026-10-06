<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'candidate_name',
        'email_address',
        'student_id',
        'candidate_id',
        'uci_number',
        'subject_line',
        'message',
        'message_date',
        'session',
        'sent_status',
        'tracking_token',
        'opened_at',
        'open_count',
    ];

    protected $casts = [
        'message_date' => 'datetime',
        'opened_at'    => 'datetime',
    ];
}
