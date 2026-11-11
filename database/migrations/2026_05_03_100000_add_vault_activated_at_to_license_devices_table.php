<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_devices', function (Blueprint $table) {
            $table->timestamp('vault_activated_at')->nullable()->after('registered_at');
        });
    }

    public function down(): void
    {
        Schema::table('license_devices', function (Blueprint $table) {
            $table->dropColumn('vault_activated_at');
        });
    }
};
