<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('vault_download_status', 32)->nullable()->after('vault_video_path');
            $table->text('vault_download_error')->nullable()->after('vault_download_status');
            $table->timestamp('vault_download_attempted_at')->nullable()->after('vault_download_error');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn([
                'vault_download_status',
                'vault_download_error',
                'vault_download_attempted_at',
            ]);
        });
    }
};
