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
        Schema::create('mock_results', function (Blueprint $table) {
            $table->id();
            $table->string('family_id');
            $table->string('name');
            $table->string('subject');
            $table->string('mock_type');
            $table->string('exam_date');
            $table->string('percentage');
            $table->string('fine_grade');
            $table->string('qualifications')->nullable();
            $table->string('tier')->nullable();
            $table->string('exam_marked_by');
            $table->string('updated_by');
            $table->text('step_1')->nullable();
            $table->text('step_2')->nullable();
            $table->text('step_3')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_results');
    }
};
