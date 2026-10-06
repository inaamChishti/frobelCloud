<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'studentdata';
    protected $primaryKey = 'studentid';
    public $timestamps = false;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'start_date',
        'is_flag',
        'tier',
        'studentname',
        'studentsur',
        'studentdob',
        'studentgender',
        'medical_condition',
        'studentyearinschool',
        'studenthours',
        'guardianid',
        'kinid',
        'admissionid',
        'student_status',
        'additionalNeeds',
        'medicalConsent',
        'photoConsent',
        'leaveAlone',
        'branch_name',
        'branch_id',
        'subject_names',
        'sessions',
        'target_grades',
        'current_grades',
        'qualifications',
        'additional_needs_explanation',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Student has been {$eventName}")
            ->logOnly([
                'studentname',
                'studentsur',
                'studentdob',
                'studentgender',
                'studentyearinschool',
                'studenthours',
                'medical_condition',
                'student_status',
                'additionalNeeds',
                'medicalConsent',
                'photoConsent',
                'leaveAlone',
                'branch_name',
                'branch_id',
            ])
            ->useLogName('Student');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->causer_id = auth()->user() ? auth()->id() : 0;

        // Fetch branch details based on session branch_id
        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        // Set branch_name and branch_id directly on the activity
        $activity->branch_id = $branch ? $branch->branch_id : null;
        $activity->branch_name = $branch ? $branch->branch_name : null;

        // Also add them to properties if needed
        $activity->properties = $activity->properties->merge([
            'branch_id' => $branch ? $branch->branch_id : null,
            'branch_name' => $branch ? $branch->branch_name : null,
        ]);
    }

    public function guardian()
{
    return $this->belongsTo(Guardian::class, 'guardianid', 'Guardianid');
}

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function kin()
    {
        return $this->belongsTo(Kin::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
