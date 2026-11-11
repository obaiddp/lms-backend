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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('meezan_payment_source')->nullable();
            $table->string('meezan_payment_date')->nullable();
            $table->string('meezan_order_number')->nullable();
            $table->text('meezan_trans_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('meezan_payment_source');
            $table->dropColumn('meezan_order_number');
            $table->dropColumn('meezan_payment_date');
            $table->dropColumn('meezan_trans_response');
        });
    }
};
