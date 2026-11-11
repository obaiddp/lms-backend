<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Meezan-only recurring: schedules created when first payment was via Meezan; cron calls recurringPayment.do.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_recurring_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_user_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id');
            $table->string('cycle', 32)->comment('monthly, annually, daily');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('PKR');
            $table->date('next_due_date');
            $table->string('status', 32)->default('active')->comment('active, cancelled, failed');
            $table->timestamp('last_charged_at')->nullable();
            $table->string('gateway_recurrence_id', 64)->nullable()->comment('Meezan recurrenceId from register.do');
            $table->timestamps();

            $table->index(['status', 'next_due_date']);
            $table->index('gateway_recurrence_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_recurring_schedules');
    }
};
