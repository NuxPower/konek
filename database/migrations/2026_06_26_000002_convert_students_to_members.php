<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'client', 'freelancer', 'member') NOT NULL DEFAULT 'member'");
        }

        DB::table('users')
            ->whereIn('role', ['client', 'freelancer'])
            ->update(['role' => 'member']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'member') NOT NULL DEFAULT 'member'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'client', 'freelancer', 'member') NOT NULL DEFAULT 'member'");
        }

        DB::table('users')
            ->where('role', 'member')
            ->update(['role' => 'freelancer']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'client', 'freelancer') NOT NULL DEFAULT 'freelancer'");
        }
    }
};
