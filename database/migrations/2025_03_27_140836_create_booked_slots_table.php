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
        Schema::create('booked_slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('time_slot_id');
            $table->integer('user_id');
            $table->string('status')->default('booked'); // booked, attended, cancelled
            $table->unique(['time_slot_id', 'user_id']);
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
        Schema::dropIfExists('booked_slots');
    }
};
