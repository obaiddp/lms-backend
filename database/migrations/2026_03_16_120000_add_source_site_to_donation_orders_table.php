<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('donation_orders', function (Blueprint $table) {
            $table->string('source_site', 100)->nullable()->default(null)->after('recurring_period');
        });
    }

    public function down()
    {
        Schema::table('donation_orders', function (Blueprint $table) {
            $table->dropColumn('source_site');
        });
    }
};
