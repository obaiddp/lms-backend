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
            $table->renameColumn('AcademcicURI', 'AcademicURI');
        });

        Schema::table('live_classes', function (Blueprint $table) {
            $table->text('InstructorURI')->nullable()->change();
            $table->text('StudentURI')->nullable()->change();
            $table->text('AcademicURI')->nullable()->change();
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
            $table->string('InstructorURI')->nullable()->change();
            $table->string('StudentURI')->nullable()->change();
            $table->string('AcademicURI')->nullable()->change();
        });

        Schema::table('live_classes', function (Blueprint $table) {
            $table->renameColumn('AcademicURI', 'AcademcicURI');
        });

    }
};
