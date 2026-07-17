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

        // Create default member user focused on posting work
        User::updateOrCreate(
            [
                'email' => 'poster@cmu.edu.ph',
            ],
            [
                'name' => 'John Poster',
                'email_verified_at' => now(),
                'password' => Hash::make($demoPassword),
                'role' => 'member',
                'phone' => '+63 917 123 4567',
                'bio' => 'Student member who posts campus work opportunities.',
                'is_active' => true,
            ]
        );

        // Create default member user focused on finding work
        User::updateOrCreate(
            [
                'email' => 'applicant@cmu.edu.ph',
            ],
            [
                'name' => 'Jane Applicant',
                'email_verified_at' => now(),
                'password' => Hash::make($demoPassword),
                'role' => 'member',
                'phone' => '+63 998 765 4321',
                'bio' => 'Student member who finds and applies to campus work.',
                'is_active' => true,
            ]
        );

        // Create additional members who can both post and apply
        $clients = User::factory()
            ->count(15)
            ->member()
            ->cmuEmail()
            ->create();

        // Create additional members who can both post and apply
        $freelancers = User::factory()
            ->count(25)
            ->member()
            ->cmuEmail()
            ->create();

        $skillIds = Skill::where('is_active', true)->pluck('id');
        User::where('role', 'member')->get()->each(function (User $user) use ($skillIds) {
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
