<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->string('family_id')->nullable();
            $table->string('consent_1_first_name')->nullable();
            $table->string('consent_1_last_name')->nullable();
            $table->date('consent_1_date')->nullable();
            $table->string('how_did_you_hear')->nullable();
            $table->text('consent_1signature')->nullable();
            $table->text('branch_name')->nullable();
            $table->text('branch_id')->nullable();
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
        Schema::dropIfExists('consents');
    }
}
