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
        Schema::create('subject_book_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('book_name')->nullable();
            $table->string('subject_name')->nullable();
            $table->string('branch_name');
            $table->string('branch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_book_assignments');
    }
};
