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
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('instructor_google_access_token')->nullable();
                $table->text('instructor_google_refresh_token')->nullable();
                $table->timestamp('instructor_google_token_expires_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('instructor_google_access_token');
            $table->dropColumn('instructor_google_refresh_token');
            $table->dropColumn('instructor_google_token_expires_at');
        });
    }
};
