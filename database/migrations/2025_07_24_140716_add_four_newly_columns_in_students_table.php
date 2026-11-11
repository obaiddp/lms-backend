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
        Schema::table('students', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->integer('age')->nullable();
            $table->string('school_grade')->nullable();
            $table->string('parent_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
             $table->dropColumn('city');
             $table->dropColumn('age');
             $table->dropColumn('school_grade');
             $table->dropColumn('parent_name');
        });
    }
};
