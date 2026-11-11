<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->bigInteger('cart_session_id');
            $table->bigInteger('donation_user_id');
            $table->string('order_number', 50);
            $table->decimal('sub_total',15, 2)->default(0.00);
            $table->decimal('discount',15, 2)->default(0.00);
            $table->decimal('shipping_cost',15, 2)->default(0.00);
            $table->decimal('tax',15, 2)->default(0.00);
            $table->decimal('platform_charge',15, 2)->default(0.00);
            $table->decimal('grand_total',15, 2)->default(0.00);
            $table->string('payment_method', 100)->nullable();
            $table->mediumText('customer_comment')->nullable();
            $table->string('payment_status', 15)->default('due')->comment('paid, due, free, pending, cancelled');
            $table->tinyInteger('delivery_status')->default(0)->comment('0=pending, 1=complete');
            // $table->tinyInteger('created_by_type')->default(1)->comment('1=student, 2=admin')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('payment_id', 255)->nullable();
            $table->string('meezan_transaction_id', 255)->nullable();
            $table->string('meezan_payment_source', 255)->nullable();
            $table->string('meezan_payment_date', 255)->nullable();
            $table->string('meezan_order_number', 255)->nullable();
            $table->string('meezan_order_id', 255)->nullable();
            $table->text('meezan_trans_response')->nullable();
            $table->string('faysal_transaction_id', 255)->nullable();
            $table->string('faysal_payment_source', 255)->nullable();
            $table->string('faysal_payment_date', 255)->nullable();
            $table->string('faysal_order_id', 255)->nullable();
            $table->mediumText('faysal_ipn_payload')->nullable();
            $table->text('faysal_hash_original')->nullable();
            $table->text('faysal_hash_calculated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_orders');
    }
};
