<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'budget',
        'budget_min',
        'budget_max',
        'budget_type',
        'type',
        'deadline',
        'experience_level',
        'duration',
        'status',
        'client_id',
        'category_id',
        'is_featured',
        'views_count',
        'applications_count',
        'published_at',
        'max_applications',
        'attachments',
        'location',
        'remote_allowed',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'published_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'is_featured' => 'boolean',
        'remote_allowed' => 'boolean',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'max_applications' => 'integer',
        'attachments' => 'array',
    ];

    /**
     * Get the client that posted this job
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the category this job belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all applications for this job
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the skills required for this job
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skill')
                    ->withTimestamps();
    }

    /**
     * Get job views
     */
    public function views(): HasMany
    {
        return $this->hasMany(JobView::class);
    }

    /**
     * Get freelancers who have applied to this job
     */
    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'applications', 'job_id', 'freelancer_id')
                    ->withPivot(['status', 'cover_letter', 'proposed_rate', 'proposed_timeline', 'created_at'])
                    ->withTimestamps();
    }

    /**
     * Scope for active jobs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for featured jobs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for jobs by experience level
     */
    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    /**
     * Scope for jobs by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for jobs within budget range
     */
    public function scopeWithinBudget($query, $minBudget, $maxBudget)
    {
        return $query->where(function ($q) use ($minBudget, $maxBudget) {
            $q->whereBetween('budget_min', [$minBudget, $maxBudget])
              ->orWhereBetween('budget_max', [$minBudget, $maxBudget])
              ->orWhere(function ($subQ) use ($minBudget, $maxBudget) {
                  $subQ->where('budget_min', '<=', $minBudget)
                       ->where('budget_max', '>=', $maxBudget);
              });
        });
    }

    /**
     * Additional scopes from your original model
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['open', 'active']);
    }

    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    public function scopeHourly($query)
    {
        return $query->where('type', 'hourly');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithinDeadline($query, $days = 30)
    {
        return $query->where('deadline', '>=', now())
                    ->where('deadline', '<=', now()->addDays($days));
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('requirements', 'like', '%' . $searchTerm . '%');
        });
    }

    public function scopeWithBudgetRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where(function ($q) use ($min) {
                $q->where('budget_min', '>=', $min)
                  ->orWhere(function($subQ) use ($min) {
                      $subQ->whereNull('budget_min')
                           ->where('budget', '>=', $min);
                  });
            });
        }

        if ($max !== null) {
            $query->where(function ($q) use ($max) {
                $q->where('budget_max', '<=', $max)
                  ->orWhere(function($subQ) use ($max) {
                      $subQ->whereNull('budget_max')
                           ->where('budget', '<=', $max);
                  });
            });
        }

        return $query;
    }

    /**
     * Get the budget as a formatted string
     */
    public function getBudgetAttribute()
    {
        if ($this->budget_type === 'fixed') {
            return $this->budget_min ? number_format($this->budget_min, 2) : '0.00';
        }
        
        if ($this->budget_min && $this->budget_max) {
            return number_format($this->budget_min, 2) . ' - ' . number_format($this->budget_max, 2);
        }
        
        return $this->budget_min ? number_format($this->budget_min, 2) : '0.00';
    }

    /**
     * Get formatted budget with currency
     */
   public function getFormattedBudgetAttribute()
{
    try {
        // Convert to float to handle string values
        $budgetMin = $this->budget_min ? (float) $this->budget_min : null;
        $budgetMax = $this->budget_max ? (float) $this->budget_max : null;
        
        if ($budgetMin && $budgetMax) {
            return '₱' . number_format($budgetMin, 2) . ' - ₱' . number_format($budgetMax, 2);
        } elseif ($budgetMin) {
            return 'From ₱' . number_format($budgetMin, 2);
        } elseif ($budgetMax) {
            return 'Up to ₱' . number_format($budgetMax, 2);
        } 
        
        // Fallback for old budget column (if it exists)
        if (isset($this->attributes['budget']) && is_numeric($this->attributes['budget'])) {
            return '₱' . number_format((float) $this->attributes['budget'], 2);
        }
        
        return 'Budget not specified';
        
    } catch (\Exception $e) {
        // Log the error for debugging
        \Illuminate\Support\Facades\Log::error('Error formatting job budget', [
            'job_id' => $this->id,
            'budget_min' => $this->budget_min,
            'budget_max' => $this->budget_max,
            'error' => $e->getMessage()
        ]);
        
        return 'Budget not available';
    }
}

    /**
     * Get the job status with proper formatting
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'open' => 'Open',
            'closed' => 'Closed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the experience level with proper formatting
     */
    public function getExperienceLevelLabelAttribute()
    {
        return match($this->experience_level) {
            'entry' => 'Entry Level',
            'intermediate' => 'Intermediate',
            'expert' => 'Expert',
            default => ucfirst($this->experience_level)
        };
    }

    /**
     * Check if job is still open for applications
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if job has reached maximum applications
     */
    public function hasReachedMaxApplications(): bool
    {
        if (!$this->max_applications) {
            return false;
        }
        
        return $this->applications_count >= $this->max_applications;
    }

    /**
     * Check if deadline has passed
     */
    public function isDeadlinePassed(): bool
    {
        if (!$this->deadline) {
            return false;
        }
        
        return $this->deadline->isPast();
    }

    /**
     * Check if user can apply to this job
     */
    public function canBeAppliedBy(User $user): bool
    {
        // Check if job is open
        if (!$this->isOpen()) {
            return false;
        }

        // Check if deadline passed
        if ($this->isDeadlinePassed()) {
            return false;
        }

        // Check if max applications reached
        if ($this->hasReachedMaxApplications()) {
            return false;
        }

        // Check if user is freelancer
        if (!$user->isFreelancer()) {
            return false;
        }

        // Check if user hasn't already applied
        return !$this->applications()
                    ->where('freelancer_id', $user->freelancer->id ?? null)
                    ->exists();
    }

    /**
     * Get similar jobs based on skills and category
     */
    public function getSimilarJobs($limit = 5)
    {
        $skillIds = $this->skills->pluck('id')->toArray();
        
        return static::where('id', '!=', $this->id)
                    ->where('status', 'open')
                    ->where(function ($query) use ($skillIds) {
                        $query->where('category_id', $this->category_id)
                              ->orWhereHas('skills', function ($skillQuery) use ($skillIds) {
                                  $skillQuery->whereIn('skills.id', $skillIds);
                              });
                    })
                    ->with(['client', 'skills'])
                    ->limit($limit)
                    ->get();
    }

    /**
     * Calculate match percentage with freelancer skills
     */
    public function calculateMatchPercentage(User $freelancer): int
    {
        if (!$freelancer->isFreelancer() || $this->skills->isEmpty()) {
            return 0;
        }

        $jobSkillIds = $this->skills->pluck('id')->toArray();
        $freelancerSkillIds = $freelancer->skills->pluck('id')->toArray();
        
        $matchingSkills = array_intersect($jobSkillIds, $freelancerSkillIds);
        
        if (empty($jobSkillIds)) {
            return 0;
        }
        
        return round((count($matchingSkills) / count($jobSkillIds)) * 100);
    }

    /**
     * Get view count for this job
     */
    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }

    /**
     * Record a view for this job
     */
    public function recordView(User $user = null, string $ipAddress = null)
    {
        return $this->views()->firstOrCreate([
            'job_id' => $this->id,
            'user_id' => $user?->id,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Additional helper methods to maintain compatibility with your existing code
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'bg-green-100 text-green-800',
            'closed' => 'bg-red-100 text-red-800',
            'draft' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTypeDisplayAttribute()
    {
        return $this->type === 'fixed' ? 'Fixed Price' : 'Hourly Rate';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getDaysUntilDeadlineAttribute()
    {
        if (!$this->deadline) {
            return null;
        }

        $now = now();
        if ($this->deadline < $now) {
            return 'Overdue';
        }

        $days = $now->diffInDays($this->deadline);
        if ($days == 0) {
            return 'Due today';
        } elseif ($days == 1) {
            return 'Due tomorrow';
        } else {
            return "Due in {$days} days";
        }
    }

    // Additional helper methods from your original model
    public function isActive(): bool
    {
        return $this->status === 'open' || $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->client_id === $user->id;
    }

    public function hasApplicationFrom(User $user): bool
    {
        if (!$user->freelancer) {
            return false;
        }
        return $this->applications()->where('freelancer_id', $user->freelancer->id)->exists();
    }

    public function canReceiveApplications(): bool
    {
        return $this->isActive() && (!$this->deadline || $this->deadline >= now());
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}