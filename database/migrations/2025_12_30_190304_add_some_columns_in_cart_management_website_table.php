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
        Schema::table('cart_management_website', function (Blueprint $table) {
            $table->mediumText('error_trans_response')->nullable();
            $table->mediumText('faysal_ipn_payload')->nullable();
            $table->text('faysal_hash_original')->nullable();
            $table->text('faysal_hash_calculated')->nullable();
            $table->text('faysal_order_id')->nullable();
            $table->text('meezan_order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_management_website', function (Blueprint $table) {
            $table->dropColumn('error_trans_response');
            $table->dropColumn('faysal_ipn_payload');
            $table->dropColumn('faysal_hash_original');
            $table->dropColumn('faysal_hash_calculated');
            $table->dropColumn('faysal_order_id');
            $table->dropColumn('meezan_order_id');
        });
    }
};
