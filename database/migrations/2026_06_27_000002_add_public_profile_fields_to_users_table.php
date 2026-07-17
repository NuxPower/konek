<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->string('profile_photo_path')->nullable();
            $table->string('introduction', 160)->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->boolean('is_profile_public')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_phone')->default(false);
            $table->boolean('show_links')->default(false);
        });

        DB::table('users')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->each(function ($user) {
                $base = Str::slug($user->name) ?: 'member';
                $username = $base;
                $suffix = 1;

                while (DB::table('users')->where('username', $username)->exists()) {
                    $username = "{$base}-{$suffix}";
                    $suffix++;
                }

                DB::table('users')->where('id', $user->id)->update(['username' => $username]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username',
                'profile_photo_path',
                'introduction',
                'portfolio_url',
                'linkedin_url',
                'github_url',
                'facebook_url',
                'is_profile_public',
                'show_email',
                'show_phone',
                'show_links',
            ]);
        });
    }
};
