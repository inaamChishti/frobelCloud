<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IAGLearnerRequest extends Model
{
    use HasFactory;
     use HasFactory;
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
        'is_approved',
    ];
}
