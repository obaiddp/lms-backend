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
    public function up()
    {
        Schema::table('class_details', function (Blueprint $table) {
            $table->text('google_meet_link')->nullable();
            $table->tinyInteger('is_meet_class_started')->default(0);
            $table->string('google_event_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_details', function (Blueprint $table) {
            $table->dropColumn('google_meet_link');
            $table->dropColumn('is_meet_class_started');
            $table->dropColumn('google_event_id');
        });
    }
};
