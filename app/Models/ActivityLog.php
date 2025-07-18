<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property-read Model|\Eloquent $causer
 * @property-read mixed $activity_type
 * @property-read mixed $attributes
 * @property-read mixed $changes
 * @property-read mixed $formatted_activity_type
 * @property-read mixed $formatted_log_name
 * @property-read mixed $ip_address
 * @property-read mixed $new
 * @property-read mixed $old
 * @property-read mixed $time_ago
 * @property-read mixed $user_agent
 * @property-read Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byCauser($causer)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byCauserType($causerType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byLogName($logName)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog bySubject($subject)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog bySubjectType($subjectType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog latest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog today()
 * @mixin \Eloquent
 */
class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Log name constants
     */
    public const LOG_NAMES = [
        'application' => 'Application',
        'job' => 'Job',
        'user' => 'User',
        'profile' => 'Profile',
        'contract' => 'Contract',
        'payment' => 'Payment',
        'review' => 'Review',
        'message' => 'Message',
        'system' => 'System',
    ];

    /**
     * Common activity types
     */
    public const ACTIVITY_TYPES = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'viewed' => 'Viewed',
        'applied' => 'Applied',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
        'shortlisted' => 'Shortlisted',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
        'logged_in' => 'Logged In',
        'logged_out' => 'Logged Out',
        'password_changed' => 'Password Changed',
        'profile_updated' => 'Profile Updated',
    ];

    /**
     * Relationship: The subject of the activity (polymorphic)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship: The causer of the activity (polymorphic)
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Filter by log name
     */
    public function scopeByLogName($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope: Filter by subject type
     */
    public function scopeBySubjectType($query, $subjectType)
    {
        return $query->where('subject_type', $subjectType);
    }

    /**
     * Scope: Filter by causer type
     */
    public function scopeByCauserType($query, $causerType)
    {
        return $query->where('causer_type', $causerType);
    }

    /**
     * Scope: Filter by causer
     */
    public function scopeByCauser($query, $causer)
    {
        if (is_object($causer)) {
            return $query->where('causer_type', get_class($causer))
                        ->where('causer_id', $causer->id);
        }
        
        return $query->where('causer_id', $causer);
    }

    /**
     * Scope: Filter by subject
     */
    public function scopeBySubject($query, $subject)
    {
        if (is_object($subject)) {
            return $query->where('subject_type', get_class($subject))
                        ->where('subject_id', $subject->id);
        }
        
        return $query->where('subject_id', $subject);
    }

    /**
     * Scope: Get recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Get activities from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * Scope: Order by most recent
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get formatted log name
     */
    public function getFormattedLogNameAttribute()
    {
        return self::LOG_NAMES[$this->log_name] ?? ucfirst($this->log_name);
    }

    /**
     * Get activity type from description
     */
    public function getActivityTypeAttribute()
    {
        // Extract activity type from description
        $description = strtolower($this->description);
        
        foreach (self::ACTIVITY_TYPES as $key => $value) {
            if (str_contains($description, $key) || str_contains($description, strtolower($value))) {
                return $key;
            }
        }
        
        return 'unknown';
    }

    /**
     * Get formatted activity type
     */
    public function getFormattedActivityTypeAttribute()
    {
        $activityType = $this->activity_type;
        return self::ACTIVITY_TYPES[$activityType] ?? ucfirst(str_replace('_', ' ', $activityType));
    }

    /**
     * Get a specific property
     */
    public function getProperty($key, $default = null)
    {
        return $this->properties[$key] ?? $default;
    }

    /**
     * Check if activity has a specific property
     */
    public function hasProperty($key): bool
    {
        return isset($this->properties[$key]);
    }

    /**
     * Get old values from properties
     */
    public function getOldAttribute()
    {
        return $this->getProperty('old', []);
    }

    /**
     * Get new values from properties
     */
    public function getNewAttribute()
    {
        return $this->getProperty('new', []);
    }

    /**
     * Get changes from properties
     */
    public function getChangesAttribute()
    {
        return $this->getProperty('changes', []);
    }

    /**
     * Get attributes from properties
     */
    public function getAttributesAttribute()
    {
        return $this->getProperty('attributes', []);
    }

    /**
     * Get IP address from properties
     */
    public function getIpAddressAttribute()
    {
        return $this->getProperty('ip_address');
    }

    /**
     * Get user agent from properties
     */
    public function getUserAgentAttribute()
    {
        return $this->getProperty('user_agent');
    }

    /**
     * Get time since activity was logged
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to log an activity
     */
    public static function logActivity(
        string $logName,
        string $description,
        $subject = null,
        $causer = null,
        array $properties = []
    ): self {
        $activity = new static([
            'log_name' => $logName,
            'description' => $description,
            'properties' => $properties,
        ]);

        if ($subject) {
            $activity->subject_type = get_class($subject);
            $activity->subject_id = $subject->id;
        }

        if ($causer) {
            $activity->causer_type = get_class($causer);
            $activity->causer_id = $causer->id;
        }

        $activity->save();

        return $activity;
    }

    /**
     * Static method to log application activity
     */
    public static function logApplicationActivity(
        string $description,
        $application,
        $causer = null,
        array $properties = []
    ): self {
        return static::logActivity('application', $description, $application, $causer, $properties);
    }

    /**
     * Static method to log job activity
     */
    public static function logJobActivity(
        string $description,
        $job,
        $causer = null,
        array $properties = []
    ): self {
        return static::logActivity('job', $description, $job, $causer, $properties);
    }

    /**
     * Static method to log user activity
     */
    public static function logUserActivity(
        string $description,
        $user,
        $causer = null,
        array $properties = []
    ): self {
        return static::logActivity('user', $description, $user, $causer, $properties);
    }

    /**
     * Static method to log system activity
     */
    public static function logSystemActivity(
        string $description,
        array $properties = []
    ): self {
        return static::logActivity('system', $description, null, null, $properties);
    }
}