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
        Schema::create('instructor_attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('student_id');
            $table->integer('instructor_id');
            $table->date('attendance_date');
            $table->integer('month');
            $table->integer('year');
            $table->string('tqc_lesson',255)->nullable();
            $table->string('nq_lesson',255)->nullable();
            $table->string('uqc_lesson',255)->nullable();
            $table->string('hifz_lesson',255)->nullable();
            $table->string('lesson_delivered',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instructor_attendance');
    }
};
