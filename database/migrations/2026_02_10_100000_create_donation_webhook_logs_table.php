<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Full trail of donation webhook calls (payload, response, duration).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_order_id')->nullable()->index();
            $table->string('event', 64)->comment('donation.created or regular_giving.created');
            $table->string('url', 512);
            $table->longText('payload')->nullable()->comment('JSON payload sent');
            $table->json('request_headers')->nullable();
            $table->unsignedInteger('response_status')->nullable()->comment('HTTP status; 0 if connection failed');
            $table->longText('response_body')->nullable();
            $table->json('response_headers')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('donation_webhook_logs');
    }
};
