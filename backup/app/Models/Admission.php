<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Admission extends Model
{
    use HasFactory, LogsActivity;
    public $timestamps = false;
    protected $primaryKey = 'admissionid';
    protected $table = 'admission';

    protected $fillable = [
        'child_name',
        'school_name',
        'familyno',
        'formfilingdate',
        'joiningdate',
        'medicalcondition',
        'feedetail',
        'timing',
        'familystatus',
        'meetingdetail',
        'payment_method',
        'add_comment',
        'child_name2',
        'child_name3',
        'child_name4',
        'child_name5',
        'school_name2',
        'school_name3',
        'school_name4',
        'school_name5',
        'child_name1',
        'school_name1',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Admission has been {$eventName}")
            ->logOnly([
                'joining_date',
                'medical_condition',
                'timing',
                'family_status',
                'meeting_detail',
                'family_id',
                'form_filling_date',
                'branch_name',
                'branch_id'
            ])
            ->useLogName('Admission');
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
