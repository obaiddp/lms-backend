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
        Schema::create('license_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->Integer('license_id');
            // physical unique identifier of device (hwid, mac, machine-id, etc.)
            $table->string('name')->nullable(); // e.g., "Ali's Laptop"
            $table->string('k_sign',255); // placeholder for the "k sign" you mentioned
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->unique(['license_id','k_sign']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('license_devices');
    }
};
