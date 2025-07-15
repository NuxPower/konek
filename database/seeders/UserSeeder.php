<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@cmu.edu.ph',
            ],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+63 912 345 6789',
                'bio' => 'System administrator for KONEK platform.',
                'is_active' => true,
            ]
        );

        // Create default client user
        $client = User::firstOrCreate(
            [
                'email' => 'client@cmu.edu.ph',
            ],
            [
                'name' => 'John Client',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => '+63 917 123 4567',
                'bio' => 'Client user for KONEK platform.',
                'is_active' => true,
            ]
        );

        // Create default freelancer user
        $freelancer = User::firstOrCreate(
            [
                'email' => 'freelancer@cmu.edu.ph',
            ],
            [
                'name' => 'Jane Freelancer',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'freelancer',
                'phone' => '+63 998 765 4321',
                'bio' => 'Freelancer user for KONEK platform.',
                'is_active' => true,
            ]
        );

        // Create additional clients
        $clients = User::factory()
            ->count(15)
            ->client()
            ->cmuEmail()
            ->create();

        // Create additional freelancers
        $freelancers = User::factory()
            ->count(25)
            ->freelancer()
            ->cmuEmail()
            ->create();

        // Create some inactive users
        User::factory()
            ->count(5)
            ->cmuEmail()
            ->create(['is_active' => false]);

        // Create some unverified users
        User::factory()
            ->count(8)
            ->cmuEmail()
            ->unverified()
            ->create();

        // Attach skills to freelancers after skills are created
        $this->command->info('User seeding completed. Skills will be attached after SkillSeeder runs.');
    }
}