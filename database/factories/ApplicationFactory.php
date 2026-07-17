<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coverLetters = [
            'I am excited to apply for this position. With my extensive experience in web development and a strong portfolio of successful projects, I believe I can deliver exceptional results for your project. I have worked on similar projects and understand the requirements well.',
            'Hello! I would love to work on this project. I have been working as a freelancer for the past 3 years and have completed numerous similar projects. My expertise in graphic design and attention to detail make me the perfect candidate for this job.',
            'I am interested in this opportunity and believe my skills align perfectly with your requirements. I have a proven track record of delivering high-quality work on time and within budget. I would be happy to discuss how I can contribute to your project.',
            'Greetings! I am a skilled professional with extensive experience in this field. I have successfully completed many projects similar to yours and have received excellent feedback from clients. I am confident I can exceed your expectations.',
            'I am writing to express my interest in this position. My background in digital marketing and content creation makes me an ideal candidate. I am committed to delivering outstanding results and building long-term professional relationships.',
        ];

        $statuses = ['pending', 'reviewing', 'shortlisted', 'accepted', 'rejected'];

        return [
            'freelancer_id' => User::factory()->freelancer(),
            'job_id' => Job::factory()->published(),
            'cover_letter' => fake()->randomElement($coverLetters),
            'proposed_rate' => fake()->randomFloat(2, 500, 10000),
            'estimated_hours' => fake()->numberBetween(8, 320),
            'status' => fake()->randomElement($statuses),
            'portfolio_links' => json_encode([
                'https://github.com/example/project1',
                'https://portfolio.example.com',
                'https://behance.net/example',
            ]),
            'attachments' => null, // Can be populated with file paths if needed
            'reviewed_at' => fake()->boolean(60) ? fake()->dateTimeBetween('-2 weeks', 'now') : null,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
        ];
    }

    /**
     * Create a pending application
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'reviewed_at' => null,
        ]);
    }

    /**
     * Create a reviewing application
     */
    public function reviewing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reviewing',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create a shortlisted application
     */
    public function shortlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shortlisted',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create an accepted application
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create a rejected application
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create a completed application
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'reviewed_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
        ]);
    }
}
