<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        $categories = [
            'Languages',
            'Technology',
            'Business',
            'Personal Development',
            'Creative Arts',
        ];

        foreach ($categories as $index => $name) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'uuid' => (string) Str::uuid(),
                    'status' => 1,
                    'is_feature' => $index < 2 ? 'yes' : 'no',
                    'category_type' => 1,
                    'sort_order' => $index,
                ]
            );

            Subcategory::updateOrCreate(
                ['category_id' => $category->id, 'slug' => Str::slug($name . ' Basics')],
                [
                    'name' => $name . ' Basics',
                    'uuid' => (string) Str::uuid(),
                    'sort_order' => 0,
                ]
            );
        }
    }
}