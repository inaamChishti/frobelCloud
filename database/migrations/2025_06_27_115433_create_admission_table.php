<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('admission', function (Blueprint $table) {
            $table->increments('admissionid');
            $table->string('familyno')->nullable();
            $table->string('formfilingdate')->nullable();
            $table->string('joiningdate')->nullable();
            $table->string('medicalcondition')->nullable();
            $table->string('feedetail')->nullable();
            $table->string('timing')->nullable();
            $table->string('familystatus')->nullable();
            $table->string('meetingdetail')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('add_comment')->nullable();
            $table->string('child_name1')->nullable();
            $table->string('school_name1')->nullable();
            $table->string('child_name2')->nullable();
            $table->string('school_name2')->nullable();
            $table->string('child_name3')->nullable();
            $table->string('school_name3')->nullable();
            $table->string('child_name4')->nullable();
            $table->string('school_name4')->nullable();
            $table->string('child_name5')->nullable();
            $table->string('school_name5')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission');
    }
};
