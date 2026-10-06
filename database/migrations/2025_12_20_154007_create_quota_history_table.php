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
        Schema::create('quota_history', function (Blueprint $table) {
            $table->id();
            $table->string('studentid');
            $table->string('family_id');
            $table->string('student_name');
            $table->text('old_studenthours')->nullable(); // Previous quota (JSON)
            $table->text('new_studenthours')->nullable(); // New quota (JSON)
            $table->integer('old_total_weekly_quota')->default(0);
            $table->integer('new_total_weekly_quota')->default(0);
            $table->date('change_date'); // Date when quota changed
            $table->string('branch_id');
            $table->string('branch_name')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['studentid', 'branch_id']);
            $table->index(['family_id', 'student_name', 'branch_id']);
            $table->index('change_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quota_history');
    }
};
