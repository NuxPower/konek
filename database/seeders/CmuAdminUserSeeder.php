<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CmuAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Earl Daniel Huervas',
            'email' => 's.huervas.earldaniel@cmu.edu.ph',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('511812Earl'),
            'role' => 'admin',
            'phone' => null,
            'bio' => 'CMU System Administrator',
            'student_id' => 'S.Huervas.EarlDaniel',
            'department' => 'Information Technology',
            'year_level' => null, // Admin doesn't need year level
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

        $this->command->info('CMU Admin user created successfully!');
        $this->command->info('Email: s.huervas.earldaniel@cmu.edu.ph');
        $this->command->info('Password: 511812Earl');
        $this->command->info('Student ID: S.Huervas.EarlDaniel');
    }
}