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
            $table->tinyInteger('is_cef_website_donation')->default(0);
            $table->string('cart_session_id')->nullable();
            $table->string('item_name')->nullable();
            $table->string('cycle')->nullable();
            $table->string('type')->nullable();
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
            $table->dropColumn('is_cef_website_donation');
            $table->dropColumn('cart_session_id');
            $table->dropColumn('item_name');
            $table->dropColumn('cycle');
            $table->dropColumn('type');
        });
    }
};
