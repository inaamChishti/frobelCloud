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
        Schema::create('guardian', function (Blueprint $table) {
            $table->id('Guardianid');
            $table->string('guardianname')->nullable();
            $table->string('guardianaddress')->nullable();
            $table->string('guardiantel')->nullable();
            $table->string('parent_relationship')->nullable();
            $table->string('guardianmob')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('countyStateRegion')->nullable();
            $table->string('zIPCode')->nullable();
            $table->string('country')->nullable();

            // ✅ Additional columns
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian');
    }
};
