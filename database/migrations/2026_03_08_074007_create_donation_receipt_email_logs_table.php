<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     * Log each donation receipt email send attempt (automatic or resend).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_receipt_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_order_id')->index();
            $table->string('recipient_email', 255);
            $table->string('status', 32)->comment('sent or failed');
            $table->timestamp('sent_at')->nullable()->comment('Set when status is sent');
            $table->text('error_message')->nullable();
            $table->string('trigger', 32)->default('automatic')->comment('automatic or resend');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_receipt_email_logs');
    }
};
