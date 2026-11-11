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
        Schema::table('classes', function (Blueprint $table) {
            $table->string('InstructorURI')->nullable();
            $table->string('StudentURI')->nullable();
            $table->string('AcademicURI')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('zoom_meeting_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('InstructorURI');
            $table->dropColumn('StudentURI');
            $table->dropColumn('AcademicURI');
            $table->dropColumn('meeting_id');
            $table->dropColumn('zoom_meeting_signature');
        });
    }
};
