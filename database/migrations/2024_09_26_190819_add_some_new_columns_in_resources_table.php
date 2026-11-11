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
        Schema::table('resources', function (Blueprint $table) {
            $table->string('youtube_url_path')->nullable();
            $table->string('youtube_video_duration')->nullable();
            $table->double('youtube_video_duration_second')->nullable();
            $table->longText('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('youtube_url_path');
            $table->dropColumn('youtube_video_duration');
            $table->dropColumn('youtube_video_duration_second');
            $table->dropColumn('text');
        });
    }
};
