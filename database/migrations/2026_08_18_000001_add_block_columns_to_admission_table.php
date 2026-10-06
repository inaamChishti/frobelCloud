<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission', function (Blueprint $table) {
            $table->tinyInteger('is_blocked')->default(0)->after('branch_id');
            $table->text('block_reason')->nullable()->after('is_blocked');
        });
    }

    public function down(): void
    {
        Schema::table('admission', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'block_reason']);
        });
    }
};
