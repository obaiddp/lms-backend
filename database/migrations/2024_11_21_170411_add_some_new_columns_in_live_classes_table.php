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
        Schema::table('live_classes', function (Blueprint $table) {
            $table->string('InstructorURI')->nullable();
            $table->string('StudentURI')->nullable();
            $table->string('AcademcicURI')->nullable();
            $table->string('status')->comment('Scheduled,Pending,Completed,Missed')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->tinyInteger('is_instructor_joined')->default(0);
            $table->tinyInteger('is_coordinator_joined')->default(0);
            $table->tinyInteger('is_moderator_left')->default(0);
            $table->integer('start_time_unix')->nullable();
            $table->integer('end_time_unix')->nullable();
            $table->integer('start_time_zoom')->nullable();
            $table->integer('end_time_zoom')->nullable();
            $table->integer('zoom_meeting_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn('InstructorURI');
            $table->dropColumn('StudentURI');
            $table->dropColumn('AcademicURI');
            $table->dropColumn('status');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('is_instructor_joined');
            $table->dropColumn('is_coordinator_joined');
            $table->dropColumn('is_moderator_left');
            $table->dropColumn('start_time_unix');
            $table->dropColumn('end_time_unix');
            $table->dropColumn('start_time_zoom');
            $table->dropColumn('start_time_zoom');
            $table->dropColumn('zoom_meeting_signature');
        });
    }
};
