<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class GeneralTimetable extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'session_type',
        'parent_id',
        'date',
        'slot',
        'time_slot',
        'teacher_id',
        'student_ids',
        'student_names',
        'subjects',
        'is_attendance',
        'additional_student',
        'permanent',
        'branch_id',
        'branch_name',
        'additional_info',
        'move_from_page',
    ];

    protected $casts = [
        'student_ids' => 'array',
        'student_names' => 'array',
        'subjects' => 'array',
    ];
   public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "General Timetable has been {$eventName}")
            ->logOnly([
                'date',
                'slot',
                'time_slot',
                'teacher_id',
                'student_ids',
                'student_names',
                'subjects',
                'is_attendance',
                'permanent',
                'branch_id',
                'branch_name',
                'additional_info',
                'behaviours',
                'performances',
            ])
            ->useLogName('GeneralTimetable');
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

}
