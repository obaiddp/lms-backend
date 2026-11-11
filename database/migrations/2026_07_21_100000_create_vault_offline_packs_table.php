<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault_offline_packs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_id');
            $table->uuid('pack_id')->unique();
            $table->enum('validity_mode', ['absolute', 'duration']);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->enum('status', ['pending', 'ready', 'failed'])->default('pending');
            $table->string('storage_path', 512)->nullable();
            $table->unsignedBigInteger('byte_size')->nullable();
            $table->text('error')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('license_id')->references('id')->on('licenses')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['license_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_offline_packs');
    }
};
