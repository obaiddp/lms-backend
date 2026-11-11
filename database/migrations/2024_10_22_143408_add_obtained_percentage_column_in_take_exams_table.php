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
        Schema::table('take_exams', function (Blueprint $table) {
            $table->integer('obtained_percentage')->nullable();
            $table->integer('passing_percentage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('take_exams', function (Blueprint $table) {
            $table->dropColumn('obtained_percentage');
            $table->dropColumn('passing_percentage');
        });
    }
};
