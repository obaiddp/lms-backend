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
        Schema::create('secure_checkout_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token')->unique();
            $table->integer('user_id');
            $table->string('cart_id'); // or order/cart reference
            $table->string('payment_method'); // e.g., 'stripe', 'jazzcash'
            $table->string('created_by_type'); // e.g., 'stripe', 'jazzcash'
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');
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
        Schema::dropIfExists('secure_checkout_tokens');
    }
};
