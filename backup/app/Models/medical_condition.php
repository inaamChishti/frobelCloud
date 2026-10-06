<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medical_condition extends Model
{
    public $timestamps = false;

    protected $table = 'medical_condition';

    protected $primaryKey = 'student_id'; // Set the primary key to 'student_id'
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'additional_student',
        'guardianid',
        'student_id',
        'family_id',
        'drName',
        'drNumber',
        'medicalDetails',


        'medicalConditions_explanation',
        'allergies',
        'medicalConsent',
        "gpPrefix",
        "gpFirstName",
        "gpLastName",
        "gpAddress",
        "gpAddressLineTwo",
        "gp_city",
        "gp_countyStateRegion",
        "gpzipCode",
        "gpcountry",
        "GPPhone",
        "branch_name",
        "branch_id",

        'medical_conditions_explanation',
        'allergies_explanation',
    ];
}
