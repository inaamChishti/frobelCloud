<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'attendance';
    public $timestamps = false;
    protected $fillable = [
        'performance',
        'behaviour',
        'additional_info',
        'family_id',
        'student_name',
        'student_year_in_school',
        'bk_ch',
        'status',
        'date',
        'teacher_name',
        'subject',
        'time_slot',
        'session_1',
        'session_2',
        'session_3',
        'branch_name',
        'branch_id',
        'adjustment',
        'attendance_type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Attendance has been {$eventName}")
            ->logOnly([
                'family_id',
                'student_name',
                'teacher_name',
                'subject',
                'time_slot',
                'session_1',
                'date',
                'bk_ch',
                'status',
                'student_year_in_school'
            ])
            ->logOnlyDirty()
            ->useLogName('Attendance');
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
        return $this->belongsTo(Student::class, 'family_id', 'admissionid');
    }
}
