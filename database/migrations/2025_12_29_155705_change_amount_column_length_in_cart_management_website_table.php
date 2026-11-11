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
           $table->decimal('main_price', 15, 2)->change();
           $table->decimal('price', 15, 2)->change();
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
            $table->decimal('main_price', 8, 2)->change();
            $table->decimal('price', 8, 2)->change();
        });
    }
};
