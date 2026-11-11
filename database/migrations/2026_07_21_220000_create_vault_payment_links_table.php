<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allow online gateway methods on vault_payments.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE vault_payments MODIFY COLUMN method ENUM('cash','bank_transfer','cheque','other','faysal_bank','meezan_bank') NOT NULL DEFAULT 'cash'");
        }

        Schema::create('vault_payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('vault_payment_id');
            $table->unsignedBigInteger('license_id');
            $table->unsignedBigInteger('vault_user_id');
            $table->timestamp('expires_at');
            $table->enum('status', ['open', 'paid', 'expired', 'revoked'])->default('open');
            $table->string('gateway', 32)->nullable();
            $table->string('gateway_reference', 128)->nullable()->index();
            $table->string('meezan_transaction_id', 128)->nullable();
            $table->string('meezan_order_id', 128)->nullable();
            $table->boolean('include_offline_pack')->default(true);
            $table->unsignedBigInteger('offline_pack_id')->nullable();
            $table->string('download_token', 64)->nullable()->unique();
            $table->timestamp('download_expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('vault_payment_id')->references('id')->on('vault_payments')->cascadeOnDelete();
            $table->foreign('license_id')->references('id')->on('licenses')->cascadeOnDelete();
            $table->foreign('vault_user_id')->references('id')->on('vault_users')->cascadeOnDelete();
            $table->foreign('offline_pack_id')->references('id')->on('vault_offline_packs')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['license_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_payment_links');
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE vault_payments MODIFY COLUMN method ENUM('cash','bank_transfer','cheque','other') NOT NULL DEFAULT 'cash'");
        }
    }
};
