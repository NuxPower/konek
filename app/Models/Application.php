<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    // Application model logic will go here

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
    ];

    protected $casts = [
        'attachments' => 'array',
        'proposed_rate' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the job that this application belongs to.
     */
    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }

    /**
     * Get the freelancer (user) that submitted the application.
     */
    public function freelancer()
    {
        return $this->belongsTo(\App\Models\User::class, 'freelancer_id');
    }
} 