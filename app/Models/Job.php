<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    // Job model logic will go here

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'category_id',
        'client_id',
        'type',
        'experience_level',
        'budget_min',
        'budget_max',
        'budget_type',
        'status',
        'deadline',
        'published_at',
        'max_applications',
        'applications_count',
        'is_featured',
        'attachments',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'attachments' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    /**
     * The skills that belong to the job.
     */
    public function skills()
    {
        return $this->belongsToMany(\App\Models\Skill::class, 'job_skill')
            ->withTimestamps()
            ->withPivot(['proficiency_required']);
    }

    /**
     * Get the applications for the job.
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'job_id');
    }

    /**
     * Get the category that owns the job.
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Get the user that posted the job.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id');
    }

    public function poster()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id');
    }

    /**
     * Users who bookmarked this job.
     */
    public function savedByUsers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'saved_jobs')
            ->withTimestamps();
    }
}
