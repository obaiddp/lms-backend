<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ALTER TABLE `enrollments` CHANGE `discount_type` `discount_type` TINYINT NULL DEFAULT '0';
     * ALTER TABLE `enrollments` CHANGE `discount_type_admission_fee` `discount_type_admission_fee` TINYINT NULL DEFAULT '0';
     * ALTER TABLE `enrollments` CHANGE `request_enrollment` `request_enrollment` TINYINT NULL DEFAULT '0';
     */
    public function up()
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `discount_type` `discount_type` TINYINT NULL DEFAULT '0'"
            );
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `discount_type_admission_fee` `discount_type_admission_fee` TINYINT NULL DEFAULT '0'"
            );
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `request_enrollment` `request_enrollment` TINYINT NULL DEFAULT '0'"
            );
        }
    }

    /**
     * Reverse the migrations.
     * Restores NOT NULL (fails if any row has NULL in these columns).
     */
    public function down()
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `request_enrollment` `request_enrollment` TINYINT NOT NULL DEFAULT '0'"
            );
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `discount_type_admission_fee` `discount_type_admission_fee` TINYINT NOT NULL DEFAULT '0'"
            );
            DB::statement(
                "ALTER TABLE `enrollments` CHANGE `discount_type` `discount_type` TINYINT NOT NULL DEFAULT '0'"
            );
        }
    }
};
