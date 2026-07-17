<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'Node.js',
            'Python', 'Django', 'HTML', 'CSS', 'Bootstrap', 'Tailwind CSS',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Git', 'GitHub',
            'Adobe Photoshop', 'Adobe Illustrator', 'Figma', 'Canva',
            'WordPress', 'WooCommerce', 'Shopify', 'Magento',
            'SEO', 'Google Analytics', 'Facebook Ads', 'Google Ads',
            'Content Writing', 'Copywriting', 'Technical Writing',
            'Data Entry', 'Excel', 'Google Sheets', 'PowerPoint',
            'Video Editing', 'Adobe Premiere', 'Final Cut Pro',
            'Photography', 'Photo Editing', 'Social Media Management',
            'English', 'Filipino', 'Cebuano', 'Translation',
            'Accounting', 'Bookkeeping', 'QuickBooks', 'Xero',
            'Customer Service', 'Virtual Assistant', 'Email Marketing',
            'Project Management', 'Agile', 'Scrum', 'Trello', 'Asana',
            'Mobile Development', 'Android', 'iOS', 'Flutter', 'React Native',
        ];

        $name = fake()->unique()->randomElement($skills);
        $baseSlug = \Illuminate\Support\Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $baseSlug = trim($baseSlug, '-');
            if (empty($baseSlug)) {
                $baseSlug = 'skill-'.\Illuminate\Support\Str::random(8);
            }
        }
        $slug = $baseSlug;
        // No DB check here, but ensures a valid slug
        if (empty($slug) || ! is_string($slug) || $slug === '?' || ! preg_match('/^[a-z0-9\-]+$/', $slug)) {
            $slug = 'skill-'.\Illuminate\Support\Str::random(8);
        }

        return [
            'name' => $name,
            'description' => fake()->sentence(),
            'slug' => $slug,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create an inactive skill
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
