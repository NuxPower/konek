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
 * @property int|null $user_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $browser_info
 * @property-read \App\Models\Job $job
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView anonymous()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView authenticated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView uniqueByIp()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView uniqueByUser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobView whereUserId($value)
 * @mixin \Eloquent
 */
class JobView extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'job_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: JobView belongs to a Job
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Relationship: JobView belongs to a User (optional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get views by authenticated users only
     */
    public function scopeAuthenticated($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope: Get anonymous views only
     */
    public function scopeAnonymous($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope: Get unique views by IP
     */
    public function scopeUniqueByIp($query)
    {
        return $query->distinct('ip_address');
    }

    /**
     * Scope: Get unique views by user
     */
    public function scopeUniqueByUser($query)
    {
        return $query->distinct('user_id')->whereNotNull('user_id');
    }

    /**
     * Scope: Get views from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Get views from specific date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get browser information from user agent
     */
    public function getBrowserInfoAttribute()
    {
        if (!$this->user_agent) {
            return null;
        }

        // Simple browser detection - you might want to use a more sophisticated library
        if (strpos($this->user_agent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($this->user_agent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($this->user_agent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($this->user_agent, 'Edge') !== false) {
            return 'Edge';
        }

        return 'Unknown';
    }

    /**
     * Check if view is from authenticated user
     */
    public function isAuthenticated(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Check if view is anonymous
     */
    public function isAnonymous(): bool
    {
        return is_null($this->user_id);
    }
}