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
        Schema::table('studentdata', function (Blueprint $table) {
            $table->text('subject_names')->nullable()->after('leaveAlone');
            $table->text('sessions')->nullable()->after('subject_names');
            $table->text('target_grades')->nullable()->after('sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studentdata', function (Blueprint $table) {
             $table->dropColumn(['subject_names', 'sessions', 'target_grades']);
        });
    }
};
