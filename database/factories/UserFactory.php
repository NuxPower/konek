<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['admin', 'client', 'freelancer']),
            'phone' => fake()->phoneNumber(),
            'bio' => fake()->paragraph(3),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a CMU email address
     */
    public function cmuEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->userName() . '@cmu.edu.ph',
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'email' => 'admin@cmu.edu.ph',
            'name' => 'System Administrator',
        ]);
    }

    /**
     * Create a client user
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    /**
     * Create a freelancer user
     */
    public function freelancer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'freelancer',
            'email' => fake()->unique()->safeEmail(),
        ]);
    }
}