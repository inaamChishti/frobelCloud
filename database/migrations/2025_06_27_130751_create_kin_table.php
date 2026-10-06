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
        Schema::create('kin', function (Blueprint $table) {
            $table->id('kinid');
            $table->string('kinname');
            $table->string('kinaddress')->nullable();
            $table->string('kintel')->nullable();
            $table->string('kinmob')->nullable();
            $table->string('emergency_conatct1_Address_line2')->nullable();
            $table->string('emergency_conatct1_city')->nullable();
            $table->string('emergency_conatct1_country_state_region')->nullable();
            $table->string('emergency_conatct1_zipCode')->nullable();
            $table->string('emergency_conatct1_country')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
            // timestamps not included as $timestamps = false in model
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kin');
    }
};
