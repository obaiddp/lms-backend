<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        foreach (['Admin', 'Instructor', 'Student', 'Organization', 'Coordinator'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $adminPermissions = [
            'account_setting', 'add_license', 'all_coordinator', 'all_course',
            'all_classes', 'all_dashboard', 'all_group', 'all_individual',
            'all_instructor', 'all_licenses', 'all_organization',
            'approved_course', 'approved_instructor', 'approved_organization',
            'blocked_coordinator', 'content_setting', 'delete_license',
            'email_notification_template', 'finance', 'global_setting', 'hold_course',
            'home_setting', 'manage_affiliate', 'manage_blog', 'manage_certificate',
            'manage_classes', 'manage_contact', 'manage_coordinator', 'manage_coupon',
            'manage_course_category', 'manage_course_difficulty_level',
            'manage_course_language', 'manage_course_reference', 'manage_course_subcategory',
            'manage_course_tag', 'manage_dashboard', 'manage_instructor',
            'manage_language', 'manage_organization', 'manage_promotion',
            'manage_resource_categories', 'manage_resources', 'manage_saas',
            'manage_student', 'manage_subscriptions', 'manage_wallet_recharge',
            'payout', 'pending_course', 'pending_instructor', 'pending_organization',
            'product_module_category', 'product_module_product', 'product_module_tag',
            'ranking_level', 'resend_monthly_fee_invoice', 'resend_payment_receipt',
            'skill', 'support_ticket', 'update_license', 'user_management',
        ];

        $permissions = collect($adminPermissions)->map(
            fn ($name) => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
        );

        Role::findByName('Admin', 'web')->syncPermissions($permissions);
    }
}