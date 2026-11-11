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
        Schema::table('donation_order_items', function (Blueprint $table) {
            $table->integer('quantity')->default(1);
            $table->decimal('original_price',15,2)->default(0.00);
            $table->string('cycle')->nullable();
            $table->string('type')->nullable();
            $table->mediumText('custom_message')->nullable();
            $table->mediumText('error_trans_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation_order_items', function (Blueprint $table) {
           $table->dropColumn('quantity');
           $table->dropColumn('original_price');
           $table->dropColumn('cycle');
           $table->dropColumn('type');
           $table->dropColumn('custom_message');
           $table->dropColumn('error_trans_response');
        });
    }
};
