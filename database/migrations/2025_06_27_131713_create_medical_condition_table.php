<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalConditionTable extends Migration
{
    public function up()
    {
        Schema::create('medical_condition', function (Blueprint $table) {
            $table->string('student_id')->primary();
            $table->string('additional_student')->nullable();
            $table->string('guardianid')->nullable();
            $table->string('family_id')->nullable();
            $table->string('drName')->nullable();
            $table->string('drNumber')->nullable();
            $table->text('medicalDetails')->nullable();
            $table->text('medicalConditions_explanation')->nullable();
            $table->text('allergies')->nullable();
            $table->boolean('medicalConsent')->nullable();

            $table->string('gpPrefix')->nullable();
            $table->string('gpFirstName')->nullable();
            $table->string('gpLastName')->nullable();
            $table->string('gpAddress')->nullable();
            $table->string('gpAddressLineTwo')->nullable();
            $table->string('gp_city')->nullable();
            $table->string('gp_countyStateRegion')->nullable();
            $table->string('gpzipCode')->nullable();
            $table->string('gpcountry')->nullable();
            $table->string('GPPhone')->nullable();

            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_condition');
    }
}
