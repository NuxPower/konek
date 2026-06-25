<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoPassword = env('KONEK_DEMO_PASSWORD', 'password');

        // Create default admin user
        User::updateOrCreate(
            [
                'email' => 'admin@cmu.edu.ph',
            ],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make($demoPassword),
                'role' => 'admin',
                'phone' => '+63 912 345 6789',
                'bio' => 'System administrator for KONEK platform.',
                'is_active' => true,
            ]
        );

        // Create default client user
        User::updateOrCreate(
            [
                'email' => 'client@cmu.edu.ph',
            ],
            [
                'name' => 'John Client',
                'email_verified_at' => now(),
                'password' => Hash::make($demoPassword),
                'role' => 'client',
                'phone' => '+63 917 123 4567',
                'bio' => 'Client user for KONEK platform.',
                'is_active' => true,
            ]
        );

        // Create default freelancer user
        User::updateOrCreate(
            [
                'email' => 'freelancer@cmu.edu.ph',
            ],
            [
                'name' => 'Jane Freelancer',
                'email_verified_at' => now(),
                'password' => Hash::make($demoPassword),
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

        $skillIds = Skill::where('is_active', true)->pluck('id');
        User::where('role', 'freelancer')->get()->each(function (User $user) use ($skillIds) {
            if ($skillIds->count() >= 3) {
                $user->skills()->syncWithoutDetaching(
                    $skillIds->random(min(6, $skillIds->count()))
                );
            }
        });

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

        $this->command->info("Demo users seeded. Shared password: {$demoPassword}");
    }
}
