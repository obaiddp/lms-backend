<?php

namespace Database\Seeders;

use App\Models\Course_language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseLanguageSeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        foreach (['English', 'Urdu', 'Arabic'] as $name) {
            Course_language::updateOrCreate(
                ['name' => $name],
                ['uuid' => (string) Str::uuid()]
            );
        }
    }
}