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
        Schema::create('class_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('slo_detail_id')->nullable();
            $table->date('class_date');
            $table->time('class_time');
            $table->Integer('slo_progress')->nullable();
            $table->string('slo_rank')->nullable();
            $table->string('class_participation')->nullable();
            $table->string('InstructorURI')->nullable();
            $table->string('StudentURI')->nullable();
            $table->string('AcademicURI')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('status')->comment('Scheduled,Pending,Completed,Missed');
            $table->text('std_classfeedback')->nullable();
            $table->text('inst_classfeedback')->nullable();
            $table->Integer('std_feedback_email_sent')->nullable();
            $table->Integer('inst_feedback_email_sent')->nullable();
            $table->time('instructor_start_class_time')->nullable();
            $table->Integer('start_time')->nullable();
            $table->Integer('end_time')->nullable();
            $table->tinyInteger('is_instructor_joined')->default(0);
            $table->tinyInteger('is_coordinator_joined')->default(0);
            $table->tinyInteger('is_moderator_left')->default(0);
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
        Schema::dropIfExists('class_details');
    }
};
