<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read mixed $accepted_applications_count
 * @property-read mixed $formatted_availability
 * @property-read mixed $formatted_experience_level
 * @property-read mixed $formatted_hourly_rate
 * @property-read mixed $formatted_response_time
 * @property-read mixed $formatted_total_earnings
 * @property-read mixed $pending_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read int|null $skills_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer experienceLevel($level)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer hourlyRateRange($min = null, $max = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer minRating($rating)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer search($term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Freelancer verified()
 * @mixin \Eloquent
 */
class Freelancer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'hourly_rate',
        'experience_level',
        'availability',
        'location',
        'languages',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'is_available',
        'is_verified',
        'rating',
        'total_jobs_completed',
        'total_earnings',
        'response_time',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'languages' => 'array',
        'is_available' => 'boolean',
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'total_jobs_completed' => 'integer',
        'total_earnings' => 'decimal:2',
        'response_time' => 'integer', // in hours
        'success_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Experience level constants
     */
    public const EXPERIENCE_LEVELS = [
        'entry' => 'Entry Level',
        'intermediate' => 'Intermediate',
        'expert' => 'Expert',
    ];

    /**
     * Availability constants
     */
    public const AVAILABILITY = [
        'full-time' => 'Full Time (40+ hrs/week)',
        'part-time' => 'Part Time (20-40 hrs/week)',
        'occasional' => 'Occasional (10-20 hrs/week)',
        'unavailable' => 'Currently Unavailable',
    ];

    /**
     * Relationship: Freelancer belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Freelancer has many applications
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Relationship: Freelancer has many skills through pivot table
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skill')
                    ->withPivot('proficiency_level', 'years_experience')
                    ->withTimestamps();
    }

    /**
     * Scope: Get available freelancers
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Get verified freelancers
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Filter by experience level
     */
    public function scopeExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    /**
     * Scope: Filter by hourly rate range
     */
    public function scopeHourlyRateRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('hourly_rate', '>=', $min);
        }
        if ($max !== null) {
            $query->where('hourly_rate', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope: Filter by minimum rating
     */
    public function scopeMinRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope: Search freelancers by title or bio
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('bio', 'like', "%{$term}%");
        });
    }

    /**
     * Get formatted experience level
     */
    public function getFormattedExperienceLevelAttribute()
    {
        return self::EXPERIENCE_LEVELS[$this->experience_level] ?? ucfirst($this->experience_level);
    }

    /**
     * Get formatted availability
     */
    public function getFormattedAvailabilityAttribute()
    {
        return self::AVAILABILITY[$this->availability] ?? ucfirst($this->availability);
    }

    /**
     * Get formatted hourly rate
     */
    public function getFormattedHourlyRateAttribute()
    {
        if (!$this->hourly_rate) {
            return 'Rate not specified';
        }

        $currency = '$'; // Make this configurable
        return "{$currency}{$this->hourly_rate}/hr";
    }

    /**
     * Get formatted total earnings
     */
    public function getFormattedTotalEarningsAttribute()
    {
        if (!$this->total_earnings) {
            return '$0';
        }

        $currency = '$'; // Make this configurable
        return "{$currency}" . number_format($this->total_earnings, 2);
    }

    /**
     * Get pending applications count
     */
    public function getPendingApplicationsCountAttribute()
    {
        return $this->applications()->pending()->count();
    }

    /**
     * Get accepted applications count
     */
    public function getAcceptedApplicationsCountAttribute()
    {
        return $this->applications()->accepted()->count();
    }

    /**
     * Get formatted response time
     */
    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time) {
            return 'Not available';
        }

        $hours = $this->response_time;
        if ($hours < 1) {
            return 'Less than 1 hour';
        } elseif ($hours < 24) {
            return "{$hours} hours";
        } else {
            $days = round($hours / 24);
            return $days == 1 ? '1 day' : "{$days} days";
        }
    }

    /**
     * Check if freelancer is available
     */
    public function isAvailable(): bool
    {
        return $this->is_available && $this->availability !== 'unavailable';
    }

    /**
     * Check if freelancer is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Mark freelancer as verified
     */
    public function markAsVerified()
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Toggle availability
     */
    public function toggleAvailability()
    {
        $this->update(['is_available' => !$this->is_available]);
    }

    /**
     * Update rating based on new feedback
     */
    public function updateRating($newRating)
    {
        $totalRatings = $this->total_jobs_completed;
        $currentRating = $this->rating ?? 0;
        
        $newAverage = (($currentRating * $totalRatings) + $newRating) / ($totalRatings + 1);
        
        $this->update(['rating' => round($newAverage, 2)]);
    }

    /**
     * Increment job completion stats
     */
    public function incrementJobStats($earnings)
    {
        $this->increment('total_jobs_completed');
        $this->increment('total_earnings', $earnings);
        
        // Update success rate
        $successRate = ($this->total_jobs_completed / $this->applications()->count()) * 100;
        $this->update(['success_rate' => round($successRate, 2)]);
    }
}