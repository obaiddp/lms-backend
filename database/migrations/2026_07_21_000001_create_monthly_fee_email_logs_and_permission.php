<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_fee_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->string('mode', 32)->default('generate'); // generate|resend
            $table->json('recipients')->nullable();
            $table->string('status', 32)->default('sent'); // sent|failed|skipped
            $table->text('error')->nullable();
            $table->timestamps();
        });

        $permission = Permission::firstOrCreate(
            ['name' => 'resend_monthly_fee_invoice', 'guard_name' => 'web']
        );

        $roleNames = DB::table('roles')->pluck('name');
        foreach ($roleNames as $roleName) {
            $role = Role::findByName($roleName, 'web');
            if ($role && ($role->hasPermissionTo('manage_student') || strcasecmp($roleName, 'Admin') === 0 || strcasecmp($roleName, 'Super Admin') === 0)) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_fee_email_logs');

        $permission = Permission::where('name', 'resend_monthly_fee_invoice')->where('guard_name', 'web')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
