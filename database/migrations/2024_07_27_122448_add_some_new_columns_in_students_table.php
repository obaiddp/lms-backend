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
            $table->string('lms_uses')->nullable();
            $table->integer('no_of_joined_students')->nullable();
            $table->string('preferred_teacher')->nullable();
            $table->string('learn_about_lms')->nullable();
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
            $table->dropColumn('lms_uses');
            $table->dropColumn('no_of_joined_students');
            $table->dropColumn('preferred_teacher');
            $table->dropColumn('learn_about_lms');
        });
    }
};
