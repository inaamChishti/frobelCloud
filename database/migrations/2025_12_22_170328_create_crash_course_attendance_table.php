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
        Schema::create('crash_course_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('family_id');
            $table->string('subject');
            $table->string('teacher');
            $table->string('timeslot');
            $table->string('branch_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crash_course_attendance');
    }
};
