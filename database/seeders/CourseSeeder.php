<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use App\Models\Course_language;
use App\Models\Difficulty_level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // LOCAL DEVELOPMENT ONLY - dummy data

        // Get some related data
        $instructor = User::where('role', USER_ROLE_INSTRUCTOR)->firstOrFail();
        $categories = Category::pluck('id')->toArray();
        $languages = Course_language::pluck('id')->toArray();
        $difficultyLevels = Difficulty_level::pluck('id')->toArray();

        // Sample course titles
        $courseTitles = [
            'Complete Web Development Bootcamp',
            'Advanced Machine Learning Course',
            'Digital Marketing Masterclass',
            'Photography Fundamentals',
            'Business Management Essentials',
        ];

        foreach ($courseTitles as $title) {
            Course::updateOrCreate(['slug' => Str::slug($title)], [
                'user_id' => $instructor->id,
                'instructor_id' => $instructor->id,
                'category_id' => $categories[array_rand($categories)],
                'course_language_id' => $languages[array_rand($languages)],
                'difficulty_level_id' => $difficultyLevels[array_rand($difficultyLevels)],
                'title' => $title,
                'subtitle' => 'Master ' . $title . ' from scratch',
                'description' => 'This comprehensive course will take you from beginner to professional level in ' . $title,
                'price' => rand(29, 199),
                'old_price' => rand(199, 299),
                'image' => 'uploads/default/course.jpg',
                'slug' => Str::slug($title),
                'status' => 1,
                'duration' => rand(20, 50),
                'duration_type' => 1,
                'short_description' => 'Learn ' . $title . ' with hands-on projects and exercises',
                'meta_title' => $title . ' - Online Course',
                'meta_description' => 'Learn ' . $title . ' with our comprehensive online course',
                'meta_keywords' => strtolower(str_replace(' ', ',', $title)),
            ]);
        }
    }
} 