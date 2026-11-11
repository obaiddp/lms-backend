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
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->Integer('no_of_students');
            $table->Integer('no_of_classes');
            // $table->date('class_date');
            // $table->time('class_time');
            $table->string('duration')->comment('duration must be in minutes');
            // $table->string('status')->comment('Scheduled,Pending,Completed,Missed');
            $table->string('type')->comment('Individual,Group');
            $table->string('learning_tool')->comment('Zoom,BBB,Jitsi,Google Meet');
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
        Schema::dropIfExists('classes');
    }
};
