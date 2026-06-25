<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SkillSeeder::class,
        ]);

        if (! app()->environment('production') || env('KONEK_SEED_DEMO', false)) {
            $this->call([
                UserSeeder::class,
                JobSeeder::class,
                ApplicationSeeder::class,
            ]);
        }
    }
}
