<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'message_id',
        'from_phone',
        'message_text',
        'received_at',
        'branch_name',
        'branch_id'
    ];
}
