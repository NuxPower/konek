<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'huervas108@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('511812Earl'),
            'role' => 'admin',
            'phone' => null,
            'bio' => 'System Administrator',
            'student_id' => null,
            'department' => null,
            'year_level' => null,
            'is_active' => 1,
            'last_login_at' => null,
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'two_factor_enabled' => 0,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'email_otp_code' => null,
            'email_otp_expires_at' => null,
            'email_otp_attempts' => 0,
            'email_otp_last_sent_at' => null,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: huervas108@gmail.com');
        $this->command->info('Password: 511812Earl');
    }
}