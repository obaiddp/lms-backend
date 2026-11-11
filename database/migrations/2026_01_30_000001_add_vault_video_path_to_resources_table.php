<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores relative path to downloaded YouTube/vault video file (e.g. vault_videos/123.mp4).
     * When set and file exists, vault API sends secure stream URL instead of YouTube URL.
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('vault_video_path', 512)->nullable()->after('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('vault_video_path');
        });
    }
};
