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
        Schema::create('studentdata', function (Blueprint $table) {
            $table->id('studentid');
            $table->string('studentname');
            $table->string('studentsur');
            $table->date('studentdob')->nullable();
            $table->string('studentgender')->nullable();
            $table->string('medical_condition')->nullable();
            $table->string('studentyearinschool')->nullable();
            $table->string('studenthours')->nullable();
            $table->string('guardianid')->nullable();
            $table->string('kinid')->nullable();
            $table->string('admissionid')->nullable();
            $table->string('student_status')->nullable();
            $table->string('additionalNeeds')->nullable();
            $table->string('medicalConsent')->default(false);
            $table->string('photoConsent')->default(false);
            $table->string('leaveAlone')->default(false);
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();

            $table->timestamps(); // optional if timestamps are needed
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studentdata');
    }
};
