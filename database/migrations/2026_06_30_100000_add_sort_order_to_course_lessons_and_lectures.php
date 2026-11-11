<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('short_description');
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
