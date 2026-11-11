<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\Language;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class LocalSettingSeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        Language::updateOrCreate(
            ['iso_code' => 'en'],
            [
                'language' => 'English',
                'flag' => null,
                'rtl' => 0,
                'status' => 1,
            ]
        );

        $home = Home::firstOrNew();
        $home->banner_mini_words_title = null;
        $home->banner_second_line_changeable_words = null;
        $home->save();

        $settings = [
            // VersionUpdate compares app_version with config('app.build_version'), which defaults to 22.
            'app_version' => '22',
            'current_version' => '6.2',
            'LMSZAIPRODUCT_build_version' => '2',
            'LMSZAIPRODUCT_current_version' => '1.1',

            // Admin layout and common helpers.
            'app_name' => 'CEF LMS (Local)',
            'app_email' => 'admin@local.test',
            'app_copyright' => 'CEF LMS (Local Development)',
            'app_date_format' => 'd/m/Y',
            'app_timezone' => 'Asia/Karachi',
            'app_logo' => 'favicon.ico',
            'app_black_logo' => 'favicon.ico',
            'app_fav_icon' => 'favicon.ico',
            'app_contact_number' => '',
            'app_currency' => 'PKR',
            'default_language' => 'en',
            'theme' => '1',

            // Local behavior defaults; no payment/provider credentials are seeded.
            'registration_email_verification' => '0',
            'private_mode' => '0',
            'device_control' => '0',
            'subscription_mode' => '0',
            'saas_mode' => '0',
            'cashback_system_mode' => '0',
            'wallet_checkout_system' => '0',
            'wallet_recharge_system' => '0',
            'refund_system_mode' => '0',
            'course_gift_mode' => '0',
            'referral_status' => '0',
            'platform_charge' => '0',
            'sell_commission' => '0',
            'app_font_design_type' => '1',
            'app_color_design_type' => '1',
            'coming_soon_mode' => '0',
            'MAIL_FROM_ADDRESS' => 'admin@local.test',
        ];

        foreach ($settings as $optionKey => $optionValue) {
            Setting::updateOrCreate(
                ['option_key' => $optionKey],
                ['option_value' => $optionValue]
            );
        }
    }
}
