<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cart_management')
            ->where('is_monthly_cron', 1)
            ->whereNotNull('month')
            ->update([
                'month' => DB::raw('CAST(`month` AS UNSIGNED)'),
            ]);

        Schema::table('cart_management', function (Blueprint $table) {
            $table->index(
                ['user_id', 'course_id', 'month', 'year', 'is_monthly_cron'],
                'cart_management_monthly_fee_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('cart_management', function (Blueprint $table) {
            $table->dropIndex('cart_management_monthly_fee_lookup_idx');
        });
    }
};
