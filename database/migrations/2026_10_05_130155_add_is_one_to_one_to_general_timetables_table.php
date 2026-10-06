<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_timetables', function (Blueprint $table) {
            // JSON array of booleans, one per student slot (e.g. [false, true, false])
            $table->text('is_one_to_one')->nullable()->after('is_attendance');
        });
    }

    public function down(): void
    {
        Schema::table('general_timetables', function (Blueprint $table) {
            $table->dropColumn('is_one_to_one');
        });
    }
};
