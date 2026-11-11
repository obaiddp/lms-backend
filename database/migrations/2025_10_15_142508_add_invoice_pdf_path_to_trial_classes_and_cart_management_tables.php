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
        Schema::table('trial_classes', function (Blueprint $table) {
            $table->string('invoice_pdf_path')->nullable();
        });
    
        Schema::table('cart_management', function (Blueprint $table) {
            $table->string('invoice_pdf_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trial_classes', function (Blueprint $table) {
            $table->dropColumn('invoice_pdf_path');
        });
    
        Schema::table('cart_management', function (Blueprint $table) {
            $table->dropColumn('invoice_pdf_path');
        });
    }
};
