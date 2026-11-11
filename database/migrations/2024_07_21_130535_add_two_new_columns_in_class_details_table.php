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
        Schema::table('class_details', function (Blueprint $table) {
            $table->integer('start_time_zoom')->nullable();
            $table->integer('end_time_zoom')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_details', function (Blueprint $table) {
            $table->dropColumn('start_time_zoom');
            $table->dropColumn('end_time_zoom');
        });
    }
};
