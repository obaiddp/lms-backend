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
             $table->decimal('original_price', 8, 2)->nullable();
             $table->decimal('admission_fee', 8, 2)->nullable();
            $table->tinyInteger('discount_type')->default(0)->comments('0 => amount, 1 => percentage');
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
