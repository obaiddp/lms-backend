<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('license_devices', function (Blueprint $table) {
            $table->timestamp('revoked_at')->nullable()->after('vault_activated_at');
            $table->string('revoke_reason', 512)->nullable()->after('revoked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_devices', function (Blueprint $table) {
            $table->dropColumn(['revoked_at', 'revoke_reason']);
        });
    }
};
