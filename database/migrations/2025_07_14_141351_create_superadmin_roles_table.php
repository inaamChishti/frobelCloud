<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperadminRolesTable extends Migration
{
    public function up()
    {
        Schema::create('superadmin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('superadmin_roles');
    }
}
