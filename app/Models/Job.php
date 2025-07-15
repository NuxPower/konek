<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    // Job model logic will go here

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
} 