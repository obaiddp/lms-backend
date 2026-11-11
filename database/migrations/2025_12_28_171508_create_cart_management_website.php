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
        Schema::create('cart_management_website', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cart_session_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('main_price')->default(0.00);
            $table->decimal('price')->default(0.00);
            $table->string('cycle')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('cart_management_website');
    }
};
