<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LocalSettingSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(CourseLanguageSeeder::class);
        $this->call(DifficultyLevelSeeder::class);
        $this->call(CourseSeeder::class);

        // Other seeders commented out for now
//         $this->call(AppSetting::class);
//         $this->call(LanguageSeeder::class);
//         $this->call(MetaSetting::class);
//         $this->call(SettingSeeder::class);
//         $this->call(RoleSeeder::class);
//         $this->call(PermissionSeeder::class);
//         $this->call(PageSeeder::class);
//         $this->call(MenuSeeder::class);
//         $this->call(HomeSeeder::class);
//         $this->call(BlogCategorySeeder::class);
//         $this->call(BlogSeeder::class);
//         $this->call(SubCategorySeeder::class);
//         $this->call(RankingLevelSeeder::class);
//         $this->call(TagSeeder::class);
//         $this->call(CourseTagSeeder::class);
//         $this->call(LearnKeyPointSeeder::class);
// //        $this->call(OrderSeeder::class);
// //        $this->call(OrderItemSeeder::class);
//         $this->call(AssignmentSeeder::class);
//         $this->call(NoticeBoardSeeder::class);
//         $this->call(InstructorFeatureSeeder::class);
//         $this->call(InstructorProcedureSeeder::class);
//         $this->call(FaqQuestionSeeder::class);
//         $this->call(SupportTicketQuestionSeeder::class);
//         $this->call(ClientLogoSeeder::class);
//         $this->call(AboutUsGeneralSeeder::class);
//         $this->call(TeamMemberSeeder::class);
//         $this->call(InstructorSupportSeeder::class);
//         $this->call(OurHistorySeeder::class);
//         $this->call(CurrencySeeder::class);
//         $this->call(CountrySeeder::class);
//         $this->call(StateSeeder::class);
//         $this->call(CitySeeder::class);
//         $this->call(BankSeeder::class);
//         $this->call(SkillSeeder::class);
//         $this->call(BadgeSeeder::class);
//         $this->call(PackagePlanSeeder::class);
    }
}
