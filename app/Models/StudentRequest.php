<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRequest extends Model
{
    use HasFactory;
    protected $fillable = ['base64_data','termsAndConditions','pdf_name','is_approved','branch_name','branch_id','is_archive'];

}
