<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class IagMeeting extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'destination',
        'learner_on_secure_pathway',
        'family_id',
        'learner_name',
        'learner_name_template',
        'staff_lead_name',
        'category',
        'courses_subjects_being_studied',
        'meeting_date',
        'career_next_steps',
        'interested_fields',
        'researched_application_process_deadlines',
        'clear_go_information',
        'is_helpful_information',
        'started_application',
        'need_help_in_application',
        'visited_our_resources_on_line',
        'resources_in_career_library',
        'IAG_lerner_plan_1',
        'IAG_lerner_plan_2',
        'IAG_lerner_plan_3',
        'file_input_1',
        'file_input_2',
        'file_input_3',
        'term_name',
        'staff_lead',
        'date',
        'meeting_notes',
        'secure_pathway',
        'iag_target1',
        'deadline',
        'iag_target2',
        'iag_target3',
        'file',
        'branch_name',
        'branch_id',
        'current_session',
        'is_old',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "IAG Meeting has been {$eventName}")
            ->logOnly([
                'family_id',
                'learner_name',
                'learner_name_template',
                'staff_lead_name',
                'category',
                'courses_subjects_being_studied',
                'meeting_date',
                'career_next_steps',
                'interested_fields',
                'researched_application_process_deadlines',
                'clear_go_information',
                'is_helpful_information',
                'started_application',
                'need_help_in_application',
                'visited_our_resources_on_line',
                'resources_in_career_library',
                'IAG_lerner_plan_1',
                'IAG_lerner_plan_2',
                'IAG_lerner_plan_3',
                'learner_on_secure_pathway',
                'branch_id',
                'branch_name',
                'current_session',
            ])
            ->useLogName('IagMeeting');
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
