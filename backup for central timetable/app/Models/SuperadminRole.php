<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperadminRole extends Model
{
    protected $table = 'superadmin_roles';

    protected $fillable = ['name'];

    public $timestamps = true;
}
