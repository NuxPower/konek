<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property-read mixed $active_jobs_count
 * @property-read mixed $completed_jobs_count
 * @property-read mixed $formatted_company_size
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client companySize($size)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client industry($industry)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client verified()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_description',
        'company_website',
        'company_size',
        'industry',
        'location',
        'phone',
        'is_verified',
        'verification_document',
        'rating',
        'total_jobs_posted',
        'total_spent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_jobs_posted' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Company size constants
     */
    public const COMPANY_SIZES = [
        'startup' => '1-10 employees',
        'small' => '11-50 employees',
        'medium' => '51-200 employees',
        'large' => '201-1000 employees',
        'enterprise' => '1000+ employees',
    ];

    /**
     * Relationship: Client belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Client has many jobs
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Scope: Get only verified clients
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Filter by company size
     */
    public function scopeCompanySize($query, $size)
    {
        return $query->where('company_size', $size);
    }

    /**
     * Scope: Filter by industry
     */
    public function scopeIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Get active jobs count
     */
    public function getActiveJobsCountAttribute()
    {
        return $this->jobs()->active()->count();
    }

    /**
     * Get completed jobs count
     */
    public function getCompletedJobsCountAttribute()
    {
        return $this->jobs()->where('status', 'closed')->count();
    }

    /**
     * Get formatted company size
     */
    public function getFormattedCompanySizeAttribute()
    {
        return self::COMPANY_SIZES[$this->company_size] ?? ucfirst($this->company_size);
    }

    /**
     * Check if client is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Mark client as verified
     */
    public function markAsVerified()
    {
        $this->update(['is_verified' => true]);
    }
}