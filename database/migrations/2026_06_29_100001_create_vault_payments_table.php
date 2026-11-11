<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_user_id');
            $table->unsignedBigInteger('vault_subscription_id')->nullable();
            $table->unsignedBigInteger('license_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8)->default('PKR');
            $table->date('paid_at');
            $table->enum('method', ['cash', 'bank_transfer', 'cheque', 'other'])->default('cash');
            $table->string('reference_number', 255)->nullable();
            $table->string('receipt_path', 512)->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->enum('status', ['pending', 'completed', 'void'])->default('pending');
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('vault_user_id')->references('id')->on('vault_users')->cascadeOnDelete();
            $table->foreign('vault_subscription_id')->references('id')->on('vault_subscriptions')->nullOnDelete();
            $table->foreign('license_id')->references('id')->on('licenses')->nullOnDelete();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['vault_user_id', 'paid_at']);
            $table->index(['vault_subscription_id', 'status']);
            $table->index(['license_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_payments');
    }
};
