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
            $table->string('faysal_transaction_id')->nullable();
            $table->string('faysal_payment_source')->nullable();
            $table->string('faysal_payment_date')->nullable();
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
            $table->dropColumn('faysal_transaction_id');
            $table->dropColumn('faysal_payment_source');
            $table->dropColumn('faysal_payment_date');
        });
    }
};
