<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('i_a_g_learner_requests', function (Blueprint $table) {
            $table->id();
            $table->string('family_id')->nullable();
            $table->string('learner_name')->nullable();
            $table->string('destination')->nullable();
            $table->string('learner_name_template')->nullable();
            $table->string('staff_lead_name')->nullable();
            $table->string('category')->nullable();
            $table->text('courses_subjects_being_studied')->nullable();
            $table->date('meeting_date')->nullable();
            $table->string('career_next_steps')->nullable();
            $table->text('interested_fields')->nullable();
            $table->string('researched_application_process_deadlines')->nullable();
            $table->string('clear_go_information')->nullable();
            $table->string('is_helpful_information')->nullable();
            $table->string('started_application')->nullable();
            $table->string('need_help_in_application')->nullable();
            $table->string('visited_our_resources_on_line')->nullable();
            $table->string('resources_in_career_library')->nullable();
            $table->text('IAG_lerner_plan_1')->nullable();
            $table->text('IAG_lerner_plan_2')->nullable();
            $table->text('IAG_lerner_plan_3')->nullable();
            $table->string('learner_on_secure_pathway')->nullable();
            $table->text('file_input_1')->nullable();
            $table->text('file_input_2')->nullable();
            $table->text('file_input_3')->nullable();
            $table->text('term_name')->nullable();
            $table->text('staff_lead')->nullable();
            $table->text('date')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->text('secure_pathway')->nullable();
            $table->text('iag_target1')->nullable();
            $table->text('deadline')->nullable();
            $table->text('iag_target2')->nullable();
            $table->text('iag_target3')->nullable();
            $table->text('file')->nullable();
            $table->text('is_approved')->nullable();
            $table->text('branch_name')->nullable();
            $table->text('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                Schema::dropIfExists('i_a_g_learner_requests');

    }
};
