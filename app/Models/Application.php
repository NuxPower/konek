<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $job_id
 * @property int $freelancer_id
 * @property string $cover_letter
 * @property numeric|null $proposed_rate
 * @property string|null $rate_type
 * @property int|null $estimated_hours
 * @property array<array-key, mixed>|null $portfolio_links
 * @property array<array-key, mixed>|null $attachments
 * @property string $status
 * @property string|null $client_notes
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Freelancer $freelancer
 * @property-read mixed $formatted_estimated_hours
 * @property-read mixed $formatted_proposed_rate
 * @property-read mixed $formatted_rate_type
 * @property-read mixed $formatted_status
 * @property-read mixed $formatted_total_project_cost
 * @property-read mixed $time_ago
 * @property-read mixed $total_project_cost
 * @property-read \App\Models\Job $job
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application accepted()
 * @method static \Database\Factories\ApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application reviewing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application shortlisted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereClientNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCoverLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereEstimatedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereFreelancerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application wherePortfolioLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereProposedRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereRateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'freelancer_id',
        'cover_letter',
        'proposed_rate',
        'rate_type',
        'estimated_hours',
        'portfolio_links',
        'attachments',
        'status',
        'client_notes',
        'reviewed_at',
        'accepted_at',
        'completion_message',      // Add this
        'completion_attachments',  // Add this
        'completed_at',           // Add this
        
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'proposed_rate' => 'decimal:2',
        'estimated_hours' => 'integer',
        'portfolio_links' => 'array',
        'attachments' => 'array',
        'reviewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completion_attachments' => 'array',  // Add this
        'completed_at' => 'datetime',         // Add this
    ];

    /**
     * Application status constants
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'reviewing' => 'Reviewing',
        'shortlisted' => 'Shortlisted',
        'rejected' => 'Rejected',
        'accepted' => 'Accepted',
        'withdrawn' => 'Withdrawn',
    ];

    /**
     * Rate type constants
     */
    public const RATE_TYPES = [
        'hourly' => 'Hourly',
        'fixed' => 'Fixed',
    ];

    /**
     * Relationship: Application belongs to a Job
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Relationship: Application belongs to a Freelancer
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    /**
     * Scope: Get pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get accepted applications
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope: Get rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Get applications under review
     */
    public function scopeReviewing($query)
    {
        return $query->where('status', 'reviewing');
    }

    /**
     * Scope: Get shortlisted applications
     */
    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    /**
     * Scope: Get recent applications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get formatted rate type
     */
    public function getFormattedRateTypeAttribute()
    {
        return self::RATE_TYPES[$this->rate_type] ?? ucfirst($this->rate_type);
    }

    /**
     * Get formatted proposed rate
     */
    public function getFormattedProposedRateAttribute()
    {
        if (!$this->proposed_rate) {
            return 'Not specified';
        }

        $currency = '$'; // Make this configurable
        $rate = "{$currency}{$this->proposed_rate}";
        
        if ($this->rate_type === 'hourly') {
            return "{$rate}/hour";
        }
        
        return $rate;
    }

    /**
     * Get formatted estimated hours
     */
    public function getFormattedEstimatedHoursAttribute()
    {
        if (!$this->estimated_hours) {
            return 'Not specified';
        }

        $hours = $this->estimated_hours;
        if ($hours == 1) {
            return '1 hour';
        } elseif ($hours < 8) {
            return "{$hours} hours";
        } elseif ($hours < 40) {
            $days = round($hours / 8);
            return $days == 1 ? '1 day' : "{$days} days";
        } else {
            $weeks = round($hours / 40);
            return $weeks == 1 ? '1 week' : "{$weeks} weeks";
        }
    }

    /**
     * Get total project cost (for fixed rate projects)
     */
    public function getTotalProjectCostAttribute()
    {
        if ($this->rate_type === 'fixed') {
            return $this->proposed_rate;
        }
        
        if ($this->rate_type === 'hourly' && $this->estimated_hours) {
            return $this->proposed_rate * $this->estimated_hours;
        }
        
        return null;
    }

    /**
     * Get formatted total project cost
     */
    public function getFormattedTotalProjectCostAttribute()
    {
        $total = $this->total_project_cost;
        if (!$total) {
            return 'Not specified';
        }
        
        $currency = '$'; // Make this configurable
        return "{$currency}{$total}";
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application is shortlisted
     */
    public function isShortlisted(): bool
    {
        return $this->status === 'shortlisted';
    }

    /**
     * Check if application is under review
     */
    public function isReviewing(): bool
    {
        return $this->status === 'reviewing';
    }

    /**
     * Check if rate type is hourly
     */
    public function isHourly(): bool
    {
        return $this->rate_type === 'hourly';
    }

    /**
     * Check if rate type is fixed
     */
    public function isFixed(): bool
    {
        return $this->rate_type === 'fixed';
    }

    /**
     * Mark application as under review
     */
    public function markAsReviewing($notes = null)
    {
        $this->update([
            'status' => 'reviewing',
            'reviewed_at' => now(),
            'client_notes' => $notes,
        ]);
    }

    /**
     * Shortlist application
     */
    public function shortlist($notes = null)
    {
        $this->update([
            'status' => 'shortlisted',
            'client_notes' => $notes,
        ]);
    }

    /**
     * Accept application
     */
    public function accept($notes = null)
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'client_notes' => $notes,
        ]);
    }

    /**
     * Reject application
     */
    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'client_notes' => $notes,
        ]);
    }

    /**
     * Withdraw application
     */
    public function withdraw()
    {
        $this->update(['status' => 'withdrawn']);
    }

    /**
     * Get time since application was submitted
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}