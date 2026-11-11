<?php

use App\Models\License;
use App\Models\User;
use App\Models\VaultUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('licenses', 'vault_user_id')) {
            Schema::table('licenses', function (Blueprint $table) {
                $table->unsignedBigInteger('vault_user_id')->nullable()->after('user_id');
            });
        }

        $userVaultMap = [];

        License::query()->with('user')->orderBy('id')->chunk(100, function ($licenses) use (&$userVaultMap) {
            foreach ($licenses as $license) {
                if ($license->vault_user_id) {
                    continue;
                }

                $userId = (int) ($license->user_id ?? 0);
                if ($userId > 0 && isset($userVaultMap[$userId])) {
                    $license->vault_user_id = $userVaultMap[$userId];
                    $license->save();

                    continue;
                }

                $user = $userId > 0 ? User::find($userId) : null;
                $email = $user?->email;

                $vaultUser = null;
                if ($email) {
                    $vaultUser = VaultUser::where('email', $email)->first();
                }

                if (! $vaultUser) {
                    $vaultUser = VaultUser::create([
                        'name' => $user?->name ?? ('License holder #'.$license->id),
                        'email' => $email,
                        'phone' => $user?->mobile_number ?? null,
                        'notes' => 'Migrated from legacy license user_id',
                    ]);
                }

                if ($userId > 0) {
                    $userVaultMap[$userId] = $vaultUser->id;
                }

                $license->vault_user_id = $vaultUser->id;
                $license->save();
            }
        });

        $foreignExists = Schema::getConnection()->getDriverName() === 'sqlite'
            ? false
            : collect(Schema::getConnection()->select(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = 'licenses'
                 AND CONSTRAINT_NAME = 'licenses_vault_user_id_foreign'"
            ))->isNotEmpty();

        if (! $foreignExists) {
            try {
                Schema::table('licenses', function (Blueprint $table) {
                    $table->foreign('vault_user_id')->references('id')->on('vault_users')->nullOnDelete();
                });
            } catch (\Throwable) {
                // FK optional if engine mismatch; column + data migration still valid.
            }
        }
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            if (Schema::hasColumn('licenses', 'vault_user_id')) {
                try {
                    $table->dropForeign(['vault_user_id']);
                } catch (\Throwable) {
                }
                $table->dropColumn('vault_user_id');
            }
        });
    }
};
