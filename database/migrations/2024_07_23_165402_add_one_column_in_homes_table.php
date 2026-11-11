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
        Schema::table('homes', function (Blueprint $table) {
            $table->tinyInteger('coordinator_area')->default(1)->comment('1=active, 2=disable');
            $table->tinyInteger('coordinator_support_area')->default(1)->comment('1=active, 2=disable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homes', function (Blueprint $table) {
            $table->dropColumn('coordinator_area');
            $table->dropColumn('coordinator_support_area');
        });
    }
};
