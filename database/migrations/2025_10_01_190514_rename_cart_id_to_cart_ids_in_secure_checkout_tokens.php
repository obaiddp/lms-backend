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
        Schema::table('secure_checkout_tokens', function (Blueprint $table) {
              // Drop old single cart_id
            $table->dropColumn('cart_id');

            // Add new cart_ids (JSON)
            $table->json('cart_ids')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('secure_checkout_tokens', function (Blueprint $table) {
            $table->dropColumn('cart_ids');
            $table->unsignedBigInteger('cart_id')->nullable();
        });
    }
};
