<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    use HasFactory;
    protected $fillable = ['layout_name','ids', 'date', 'ampm','cell_id','position','session_name'];
}
