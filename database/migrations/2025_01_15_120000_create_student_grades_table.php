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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->string('studentid');
            $table->string('family_id');
            $table->string('full_name');
            $table->string('subject_name');
            $table->string('grade');
            $table->string('month'); // Format: "January_2025"
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index(['studentid', 'month']);
            $table->index(['family_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};

