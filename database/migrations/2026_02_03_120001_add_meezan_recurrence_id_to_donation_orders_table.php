<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Store Meezan recurrenceId from register.do when cart had recurring items; used to create donation_recurring_schedules on payment success.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_orders', function (Blueprint $table) {
            $table->string('meezan_recurrence_id', 64)->nullable()->after('meezan_trans_response')->comment('Meezan recurrenceId from register.do when recurrenceType=MANUAL');
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
            $table->dropColumn('meezan_recurrence_id');
        });
    }
};
