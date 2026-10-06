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
        Schema::create('timetable', function (Blueprint $table) {
            $table->id();
            $table->string('studentname')->nullable();;
            $table->string('admissionid')->nullable();;
            $table->string('day')->nullable();;
            $table->string('timeslot')->nullable();;
            $table->string('subject')->nullable();;

            // Additional nullable columns
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable');
    }
};
