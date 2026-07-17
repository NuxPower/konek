<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    // ActivityLog model logic will go here

    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subject entity that the activity was performed on.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the user (causer) who performed the activity.
     */
    public function causer()
    {
        return $this->morphTo();
    }
}
