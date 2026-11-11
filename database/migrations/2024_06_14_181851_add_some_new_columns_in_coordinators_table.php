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
        Schema::table('coordinators', function (Blueprint $table) {
            $table->string('professional_title')->nullable();
            $table->mediumText('social_link')->nullable();
            $table->string('slug')->nullable();
            $table->string('is_private', 10)->default('no')->comment('yes, no');
            $table->string('remove_from_web_search', 10)->default('no')->comment('yes, no');
            $table->string('cv_file')->nullable();
            $table->string('cv_filename')->nullable();
            $table->tinyInteger('consultation_available')->default(0)->comment('1=yes, 0=no')->nullable();
            $table->tinyInteger('available_type')->default(3)->comment('1=In-person, 0=Online, 3=Both')->nullable();
            $table->integer('hourly_rate')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->tinyInteger('consultancy_area')->default(3);
            $table->tinyInteger('is_offline')->default(0)->comment('offline status');
            $table->string('offline_message')->nullable()->comment('offline message');
            $table->decimal('hourly_old_rate', 10, 2)->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->tinyInteger('is_subscription_enable')->nullable()->default(STATUS_PENDING);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coordinators', function (Blueprint $table) {
            $table->dropColumn('professional_title');
            $table->dropColumn('social_link');
            $table->dropColumn('slug');
            $table->dropColumn('is_private');
            $table->dropColumn('remove_from_web_search');
            $table->dropColumn('cv_file');
            $table->dropColumn('cv_filename');
            $table->dropColumn('consultation_available');
            $table->dropColumn('available_type');
            $table->dropColumn('hourly_rate');
            $table->dropColumn('level_id');
            $table->dropColumn('consultancy_area');
            $table->dropColumn('is_offline');
            $table->dropColumn('offline_message');
            $table->dropColumn('hourly_old_rate');
            $table->dropColumn('organization_id');
            $table->dropColumn('is_subscription_enable');
        });
    }
};
