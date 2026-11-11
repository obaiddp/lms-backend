<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_user_id');
            $table->unsignedBigInteger('license_id');
            $table->string('plan_name', 255);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 8)->default('PKR');
            $table->enum('billing_interval', ['monthly', 'quarterly', 'yearly', 'custom'])->default('yearly');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'cancelled', 'expired', 'paused'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('vault_user_id')->references('id')->on('vault_users')->cascadeOnDelete();
            $table->foreign('license_id')->references('id')->on('licenses')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['vault_user_id', 'status']);
            $table->index(['license_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_subscriptions');
    }
};
