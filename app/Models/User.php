<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'phone',
        'bio',
        'introduction',
        'resume_path',
        'profile_photo_path',
        'student_id',
        'department',
        'year_level',
        'availability',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'facebook_url',
        'is_profile_public',
        'show_email',
        'show_phone',
        'show_links',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'year_level' => 'integer',
            'is_profile_public' => 'boolean',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'show_links' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (! $user->username) {
                $user->username = static::generateUniqueUsername($user->name);
            }
        });
    }

    public static function generateUniqueUsername(string $name, ?int $ignoreUserId = null): string
    {
        $base = Str::slug($name) ?: 'member';
        $username = $base;
        $suffix = 1;

        while (static::query()
            ->where('username', $username)
            ->when($ignoreUserId, fn ($query) => $query->whereKeyNot($ignoreUserId))
            ->exists()) {
            $username = "{$base}-{$suffix}";
            $suffix++;
        }

        return $username;
    }

    public function canBeViewedBy(?User $viewer): bool
    {
        if ($viewer && ($viewer->is($this) || $viewer->role === 'admin')) {
            return true;
        }

        return $this->is_profile_public;
    }

    public function identityVerification(): HasOne
    {
        return $this->hasOne(IdentityVerification::class);
    }

    public function getIdProofPointsAttribute(): int
    {
        return $this->identityVerification?->proof_points ?? 0;
    }

    public function getIdProofStatusAttribute(): string
    {
        return $this->identityVerification?->status ?? IdentityVerification::STATUS_UNSUBMITTED;
    }

    /**
     * The skills that belong to the user.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill')
            ->withTimestamps()
            ->withPivot(['proficiency_level', 'years_experience']);
    }

    /**
     * Jobs posted by the user.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    /**
     * Applications submitted by the user.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'freelancer_id');
    }

    /**
     * Jobs bookmarked by the user.
     */
    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs')
            ->withTimestamps();
    }
}
