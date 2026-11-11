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
        Schema::table('cart_management', function (Blueprint $table) {
            $table->string('faysal_order_id')->nullable();
            $table->string('meezan_order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_management', function (Blueprint $table) {
            $table->dropColumn('faysal_order_id');
            $table->dropColumn('meezan_order_id');
        });
    }
};
