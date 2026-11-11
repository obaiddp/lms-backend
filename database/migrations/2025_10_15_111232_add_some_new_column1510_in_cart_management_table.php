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
            $table->decimal('net_original_price', 8, 2)->nullable();
            $table->tinyInteger('discount_type_admission_fee')->default(0)->comments('0 => amount, 1 => percentage');
            $table->decimal('discount_admission_fee', 8, 2)->nullable();
            $table->decimal('net_admission_fee', 8, 2)->nullable();

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
            //
        });
    }
};
