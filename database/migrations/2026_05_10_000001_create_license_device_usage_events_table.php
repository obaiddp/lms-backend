<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_device_usage_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_id');
            $table->unsignedBigInteger('license_device_id')->nullable();
            $table->string('device_key', 512)->nullable();
            $table->string('event', 64);
            $table->text('detail')->nullable();
            $table->timestamps();

            $table->index(['license_id', 'created_at']);
            $table->foreign('license_id')->references('id')->on('licenses')->cascadeOnDelete();
            $table->foreign('license_device_id')->references('id')->on('license_devices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_device_usage_events');
    }
};
