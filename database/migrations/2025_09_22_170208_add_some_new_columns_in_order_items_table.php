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
        Schema::table('order_items', function (Blueprint $table) {
            $table->Integer('created_by_type')->default(1);
            $table->Integer('cart_type')->default(1);
            $table->Integer('cart_id')->nullable();
            $table->tinyInteger('request_enrollment')->default(0);
            $table->Integer('trial_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('created_by_type');
            $table->dropColumn('cart_type');
            $table->dropColumn('cart_id');
            $table->dropColumn('request_enrollment');
            $table->dropColumn('trial_id');
        });
    }
};
