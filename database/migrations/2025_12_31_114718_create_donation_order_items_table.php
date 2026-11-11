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
        Schema::create('donation_order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_session_id');
            $table->bigInteger('cart_id');
            $table->bigInteger('order_id');
            $table->bigInteger('donation_user_id');
            $table->bigInteger('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->decimal('unit_price',15, 2)->default(0.00);
            $table->decimal('admin_commission',15, 2)->default(0.00);
            $table->decimal('owner_balance',15, 2)->default(0.00);
            $table->Integer('month')->nullable();
            $table->Integer('year')->nullable();
            $table->integer('sell_commission')->default(0)->comment('How much percentage get admin and calculate in admin_commission');
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
        Schema::dropIfExists('donation_order_items');
    }
};
