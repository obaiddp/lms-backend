<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Idempotency for cron: one DonationOrder per (recurring_schedule_id, recurring_period).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('recurring_schedule_id')->nullable()->after('is_monthly_cron')->comment('Set when order created by recurring cron');
            $table->string('recurring_period', 32)->nullable()->after('recurring_schedule_id')->comment('Y-m for monthly, Y for annually');
            $table->index(['recurring_schedule_id', 'recurring_period']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation_orders', function (Blueprint $table) {
            $table->dropIndex(['recurring_schedule_id', 'recurring_period']);
            $table->dropColumn(['recurring_schedule_id', 'recurring_period']);
        });
    }
};
