<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_books', function (Blueprint $table) {
            $table->id();
            $table->string('family_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('book')->nullable();
            $table->string('subject')->nullable();
            $table->string('teacher')->nullable();
            $table->string('paid_status')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_books');
    }
}
