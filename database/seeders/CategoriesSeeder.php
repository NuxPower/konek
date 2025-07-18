<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'General',
            'Web Development',
            'Mobile Development',
            'Design & Graphics',
            'Writing & Content',
            'Marketing & SEO',
            'Data & Analytics',
            'DevOps & Infrastructure',
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate(
                ['name' => $categoryName],
                [
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'is_active' => true
                ]
            );
        }
    }
}