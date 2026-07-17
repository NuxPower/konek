<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'email' => strtolower(fake()->unique()->userName()).'@cmu.edu.ph',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'remember_token' => Str::random(10),
            'role' => 'member',
            'phone' => fake()->phoneNumber(),
            'bio' => fake()->paragraph(3),
            'is_active' => true,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
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
            'email' => fake()->unique()->userName().'@cmu.edu.ph',
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
     * Create a member user who can post work and find work.
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'email' => strtolower(fake()->unique()->userName()).'@cmu.edu.ph',
        ]);
    }

    /**
     * Create a member user who can post work and find work.
     */
    public function freelancer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'email' => strtolower(fake()->unique()->userName()).'@cmu.edu.ph',
        ]);
    }

    public function member(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'email' => strtolower(fake()->unique()->userName()).'@cmu.edu.ph',
        ]);
    }
}
