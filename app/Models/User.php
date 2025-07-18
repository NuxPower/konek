<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Services\TwoFactorAuthenticationProvider;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'bio',
        'student_id',
        'department',
        'year_level',
        'is_active',
        'last_login_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'email_otp_code',
        'email_otp_expires_at',
        'email_otp_attempts',
        'email_otp_last_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
         'email_otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'year_level' => 'integer',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'email_otp_expires_at' => 'datetime',
            'email_otp_last_sent_at' => 'datetime',
            'email_otp_attempts' => 'integer',
        ];
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'is_active' => true,
        'role' => 'client',
        'two_factor_enabled' => false,
    ];

    /**
     * Role constants
     */
    public const ROLES = [
        'admin' => 'Administrator',
        'client' => 'Client',
        'freelancer' => 'Freelancer',
    ];

    /**
     * Relationship: User has one Client profile
     */
    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Relationship: User has one Freelancer profile
     */
    public function freelancer(): HasOne
    {
        return $this->hasOne(Freelancer::class);
    }

    /**
     * Relationship: User has many job views
     */
    public function jobViews(): HasMany
    {
        return $this->hasMany(JobView::class);
    }

    /**
     * Get the OTP tokens for the user.
     */
    public function otpTokens(): HasMany
    {
        return $this->hasMany(OtpToken::class);
    }

    /**
     * Get the skills that belong to the user
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
                    ->withPivot(['proficiency_level', 'years_experience'])
                    ->withTimestamps();
    }

    /**
     * Get user skills with proficiency level
     */
    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    /**
     * Scope to filter active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter users by role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get clients only
     */
    public function scopeClients($query)
    {
        return $query->where('role', 'client');
    }

    /**
     * Scope to get freelancers only
     */
    public function scopeFreelancers($query)
    {
        return $query->where('role', 'freelancer');
    }

    /**
     * Scope to get admins only
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if user is freelancer
     */
    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    /**
     * Get the user's role name in a format compatible with Laravel Permission package.
     * This method provides compatibility for views expecting Spatie Permission methods.
     */
    public function getRoleNames()
    {
        return collect([$this->role]);
    }

    /**
     * Get all available roles for the system.
     */
    public static function getAvailableRoles(): array
    {
        return array_keys(self::ROLES);
    }

    /**
     * Get the display name for the user's role.
     */
    public function getRoleDisplayName(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Check if user has any of the given roles (compatibility method).
     */
    public function hasAnyRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role, $roles);
    }

    /**
     * Check if user has all of the given roles (compatibility method).
     */
    public function hasAllRoles($roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return count($roles) === 1 && $this->hasRole($roles[0]);
    }

    /**
     * Check if user has two-factor authentication enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && !is_null($this->two_factor_secret);
    }

    /**
     * Get the user's two-factor authentication recovery codes.
     */
    public function recoveryCodes(): array
    {
        return $this->two_factor_recovery_codes ?? [];
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     */
    public function replaceRecoveryCode(string $code): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => collect($this->recoveryCodes())
                ->reject($code)
                ->push(encrypt(Str::random(10).'-'.Str::random(10)))
                ->all(),
        ])->save();
    }

    /**
     * Get the user's full name (alias for name)
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get formatted role name
     */
    public function getFormattedRoleAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Activate user account
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get skills by proficiency level
     */
    public function skillsByProficiency($level)
    {
        return $this->skills()->wherePivot('proficiency_level', $level);
    }

    /**
     * Add skill to user
     */
    public function addSkill($skillId, $proficiencyLevel = 'basic', $yearsExperience = 0)
    {
        return $this->skills()->attach($skillId, [
            'proficiency_level' => $proficiencyLevel,
            'years_experience' => $yearsExperience,
        ]);
    }

    /**
     * Update user skill
     */
    public function updateSkill($skillId, $proficiencyLevel = null, $yearsExperience = null)
    {
        $data = [];
        if ($proficiencyLevel !== null) {
            $data['proficiency_level'] = $proficiencyLevel;
        }
        if ($yearsExperience !== null) {
            $data['years_experience'] = $yearsExperience;
        }
        
        return $this->skills()->updateExistingPivot($skillId, $data);
    }

    /**
     * Remove skill from user
     */
    public function removeSkill($skillId)
    {
        return $this->skills()->detach($skillId);
    }

    /**
     * Get user's client profile (create if doesn't exist)
     */
    public function getClientProfile()
    {
        if (!$this->client) {
            $this->client()->create([
                'company_name' => $this->name,
                'is_verified' => false,
                'rating' => 0,
                'total_jobs_posted' => 0,
                'total_spent' => 0,
            ]);
        }
        
        return $this->client;
    }

    /**
     * Get user's freelancer profile (create if doesn't exist)
     */
    public function getFreelancerProfile()
    {
        if (!$this->freelancer) {
            $this->freelancer()->create([
                'title' => 'Freelancer',
                'bio' => $this->bio,
                'experience_level' => 'entry',
                'availability' => 'part-time',
                'is_available' => true,
                'is_verified' => false,
                'rating' => 0,
                'total_jobs_completed' => 0,
                'total_earnings' => 0,
            ]);
        }
        
        return $this->freelancer;
    }

    /**
     * Get jobs posted by this user (if client)
     */
    public function jobsPosted()
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    /**
     * Get jobs posted by this user (alias for jobsPosted)
     */
    public function jobs()
    {
        return $this->jobsPosted();
    }

    /**
     * Get applications submitted by this user (if freelancer)
     */
    public function applicationsSubmitted()
    {
        return $this->hasMany(Application::class, 'freelancer_id');
    }

    /**
     * Get applications made by freelancer through freelancer relationship
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'freelancer_id');
    }

    /**
     * Get jobs that user has applied to (if freelancer)
     */
    public function appliedJobs()
    {
        return $this->belongsToMany(Job::class, 'applications', 'freelancer_id', 'job_id')
                    ->withPivot(['status', 'cover_letter', 'created_at'])
                    ->withTimestamps();
    }

    /**
     * Check if user can post jobs
     */
    public function canPostJobs(): bool
    {
        return $this->isClient() && $this->is_active;
    }

    /**
     * Check if user can apply to jobs
     */
    public function canApplyToJobs(): bool
    {
        return $this->isFreelancer() && $this->is_active;
    }

    /**
     * Check if user has a pending OTP verification
     */
    public function hasPendingOtp(): bool
    {
        $expiresAt = $this->email_otp_expires_at ? Carbon::parse($this->email_otp_expires_at) : null;
        return !is_null($this->email_otp_code) && 
               !is_null($expiresAt) && 
               $expiresAt->isFuture();
    }

    /**
     * Check if OTP has expired
     */
    public function isOtpExpired(): bool
    {
        $expiresAt = $this->email_otp_expires_at ? Carbon::parse($this->email_otp_expires_at) : null;
        return !is_null($expiresAt) && 
               $expiresAt->isPast();
    }

    /**
     * Get user's dashboard stats
     */
    public function getDashboardStats()
    {
        $stats = [
            'role' => $this->role,
            'is_active' => $this->is_active,
            'member_since' => $this->created_at->format('M Y'),
            'two_factor_enabled' => $this->hasTwoFactorEnabled(),
        ];

        if ($this->isClient()) {
            $client = $this->client;
            $stats['jobs_posted'] = $client->jobs()->count();
            $stats['active_jobs'] = $client->jobs()->active()->count();
            $stats['total_spent'] = $client->total_spent ?? 0;
            $stats['rating'] = $client->rating ?? 0;
        } elseif ($this->isFreelancer()) {
            $freelancer = $this->freelancer;
            $stats['applications_sent'] = $freelancer->applications()->count();
            $stats['jobs_completed'] = $freelancer->total_jobs_completed ?? 0;
            $stats['total_earned'] = $freelancer->total_earnings ?? 0;
            $stats['rating'] = $freelancer->rating ?? 0;
        }

        return $stats;
    }
}