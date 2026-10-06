<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class MockResult extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'family_id',
        'name',
        'subject',
        'mock_type',
        'exam_date',
        'percentage',
        'fine_grade',
        'qualifications',
        'tier',
        'exam_marked_by',
        'updated_by',
        'step_1',
        'step_2',
        'step_3',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Mock Result has been {$eventName}")
            ->logOnly([
                'family_id',
                'name',
                'subject',
                'mock_type',
                'exam_date',
                'percentage',
                'fine_grade',
                'qualifications',
                'tier',
                'exam_marked_by',
                'updated_by',
                'step_1',
                'step_2',
                'step_3',
                'branch_id',
                'branch_name',
            ])
            ->useLogName('MockResult');
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
