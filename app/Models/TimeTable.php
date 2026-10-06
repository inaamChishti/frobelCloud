<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class TimeTable extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'timetable';
    public $timestamps = false;

    protected $fillable = [
        'studentname',
        'admissionid',
        'day',
        'timeslot',
        'subject',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Time Table has been {$eventName}")
            ->logOnly([
                'studentname',
                'admissionid',
                'day',
                'timeslot',
                'subject',
                'branch_name',
                'branch_id',
            ])
            ->useLogName('TimeTable');
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
