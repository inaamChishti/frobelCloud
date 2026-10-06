<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class StudentGrade extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'student_grades';

    protected $fillable = [
        'studentid',
        'family_id',
        'full_name',
        'subject_name',
        'grade',
        'month',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Student Grade has been {$eventName}")
            ->logOnly([
                'studentid',
                'family_id',
                'full_name',
                'subject_name',
                'grade',
                'month',
                'branch_id',
                'branch_name',
            ])
            ->logOnlyDirty()
            ->useLogName('StudentGrade');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->causer_id = auth()->user() ? auth()->id() : 0;

        // Fetch branch details based on session branch_id
        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        // Set branch_name and branch_id directly on the activity
        $activity->branch_id = $branch ? $branch->branch_id : ($this->branch_id ?? null);
        $activity->branch_name = $branch ? $branch->branch_name : ($this->branch_name ?? null);

        // Also add them to properties if needed
        try {
            if (isset($activity->properties) && $activity->properties) {
                $activity->properties = $activity->properties->merge([
                    'branch_id' => $branch ? $branch->branch_id : ($this->branch_id ?? null),
                    'branch_name' => $branch ? $branch->branch_name : ($this->branch_name ?? null),
                ]);
            }
        } catch (\Exception $e) {
            // Properties might not be available in all contexts, ignore
        }
    }
}

