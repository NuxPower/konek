<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Job;
use App\Models\User;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTitles = [
            'WordPress Website Development',
            'Logo Design for Startup',
            'Content Writing for Blog',
            'Social Media Management',
            'Data Entry Project',
            'Mobile App Development',
            'SEO Optimization',
            'Video Editing for YouTube',
            'Graphic Design for Marketing',
            'E-commerce Store Setup',
            'Database Management',
            'Technical Writing',
            'Photo Editing Services',
            'Virtual Assistant Needed',
            'Translation Services',
            'Accounting Support',
            'UI/UX Design Project',
            'Digital Marketing Campaign',
            'Web Scraping Project',
            'Customer Service Support'
        ];

        $jobTypes = ['full-time', 'part-time', 'contract', 'freelance'];
        $experienceLevels = ['entry', 'mid', 'senior'];
        $statuses = ['draft', 'published', 'closed', 'cancelled'];

        return [
            'title' => fake()->randomElement($jobTitles),
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->paragraphs(2, true),
            'budget_min' => fake()->randomFloat(2, 500, 5000),
            'budget_max' => fake()->randomFloat(2, 5000, 20000),
            'type' => fake()->randomElement(['full-time', 'part-time', 'contract', 'internship']),
            'experience_level' => fake()->randomElement(['entry', 'intermediate', 'expert']),
            'deadline' => fake()->dateTimeBetween('now', '+2 months'),
            'status' => fake()->randomElement($statuses),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'applications_count' => fake()->numberBetween(0, 50),
            'client_id' => User::factory()->client(),
            'category_id' => Category::factory(),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Create a published job
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Create a draft job
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Create a closed job
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Create a featured job
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Create a high-budget job
     */
    public function highBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_min' => fake()->randomFloat(2, 10000, 30000),
            'budget_max' => fake()->randomFloat(2, 30000, 100000),
        ]);
    }

    /**
     * Create an urgent job (deadline within 1 week)
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }
}