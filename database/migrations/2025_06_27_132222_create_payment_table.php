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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('paymentid');
            $table->string('deleted_by')->nullable();
            $table->string('cash_payment', 10, 2)->nullable();
            $table->string('card_payment', 10, 2)->nullable();
            $table->string('adjustment', 10, 2)->nullable();
            $table->string('bank_transfer', 10, 2)->nullable();
            $table->string('paymentfamilyid')->nullable();
            $table->string('paymentfrom')->nullable();
            $table->string('paymentto')->nullable();
            $table->string('paymentdate')->nullable();
            $table->string('to')->nullable();
            $table->string('paid', 10, 2)->nullable();
            $table->string('paid_up_to_date')->nullable();
            $table->string('last_payment_date')->nullable();
            $table->string('package')->nullable();
            $table->string('collector')->nullable();
            $table->string('balance', 10, 2)->nullable();
            $table->text('comment')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_detail')->nullable();
            $table->string('receipt_no')->nullable();
              $table->string('branch_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Additional nullable columns

        });
    }

    public function down()
    {
        Schema::dropIfExists('payment');
    }
};
