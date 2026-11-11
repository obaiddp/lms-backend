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
        Schema::create('live_class_student_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('live_class_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('StudentURI')->nullable();
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
        Schema::dropIfExists('live_class_student_links');
    }
};
