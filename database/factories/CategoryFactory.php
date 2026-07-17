<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Web Development',
            'Mobile Development',
            'Graphic Design',
            'Content Writing',
            'Digital Marketing',
            'Data Entry',
            'Translation',
            'Video Editing',
            'Photography',
            'Accounting',
            'Virtual Assistant',
            'SEO Services',
            'Social Media Management',
            'UI/UX Design',
            'Database Administration',
            'Technical Writing',
            'E-commerce',
            'WordPress Development',
            'Logo Design',
            'Tutoring',
        ];

        $name = fake()->randomElement($categories);
        $baseSlug = \Illuminate\Support\Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $baseSlug = trim($baseSlug, '-');
            if (empty($baseSlug)) {
                $baseSlug = 'category-'.\Illuminate\Support\Str::random(8);
            }
        }
        $slug = $baseSlug;
        $counter = 1;
        // Ensure uniqueness of slug
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        if (empty($slug) || ! is_string($slug) || $slug === '?' || ! preg_match('/^[a-z0-9\-]+$/', $slug)) {
            $slug = 'category-'.\Illuminate\Support\Str::random(8);
        }

        return [
            'name' => $name,
            'description' => fake()->paragraph(2),
            'slug' => $slug,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create an inactive category
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
