<?php

namespace Database\Seeders;

use App\Models\Difficulty_level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DifficultyLevelSeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        foreach (['Beginner', 'Intermediate', 'Advanced'] as $name) {
            Difficulty_level::updateOrCreate(
                ['name' => $name],
                ['uuid' => (string) Str::uuid()]
            );
        }
    }
}