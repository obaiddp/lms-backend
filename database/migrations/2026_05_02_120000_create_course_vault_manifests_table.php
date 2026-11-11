<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_vault_manifests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->unique();
            $table->json('payload')->nullable();
            $table->boolean('is_ready')->default(false);
            $table->unsignedTinyInteger('manifest_version')->default(1);
            $table->timestamp('last_built_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->index('is_ready');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_vault_manifests');
    }
};
