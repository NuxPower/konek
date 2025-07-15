<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    // Skill model logic will go here

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the skill.
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * The jobs that require this skill.
     */
    public function jobs()
    {
        return $this->belongsToMany(\App\Models\Job::class, 'job_skill')
            ->withTimestamps()
            ->withPivot(['proficiency_required']);
    }

    /**
     * The users that have this skill.
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_skill')
            ->withTimestamps()
            ->withPivot(['proficiency_level', 'years_experience']);
    }
} 