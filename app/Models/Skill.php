<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $category_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSkill> $userSkills
 * @property-read int|null $user_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill byCategory($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill bySlug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Default attributes
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the category that owns the skill
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the users that have this skill
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')
                    ->withPivot(['proficiency_level', 'years_experience'])
                    ->withTimestamps();
    }

    /**
     * Get the user skills pivot records
     */
    public function userSkills()
    {
        return $this->hasMany(UserSkill::class);
    }

    /**
     * Scope to filter active skills
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter skills by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get skill by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Generate slug from name
     */
    public function generateSlug()
    {
        return str_replace(' ', '-', strtolower($this->name));
    }

    /**
     * Get users with specific proficiency level for this skill
     */
    public function usersByProficiency($level)
    {
        return $this->users()->wherePivot('proficiency_level', $level);
    }

    /**
     * Get users with minimum years of experience for this skill
     */
    public function usersByMinExperience($years)
    {
        return $this->users()->wherePivot('years_experience', '>=', $years);
    }

    /**
     * Activate skill
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate skill
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }
}

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill byProficiency($level)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill bySkill($skillId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill minExperience($years)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSkill query()
 * @mixin \Eloquent
 */
class UserSkill extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_skills';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'skill_id',
        'proficiency_level',
        'years_experience',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
        ];
    }

    /**
     * Get the user that owns the skill
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the skill
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Scope to filter by proficiency level
     */
    public function scopeByProficiency($query, $level)
    {
        return $query->where('proficiency_level', $level);
    }

    /**
     * Scope to filter by minimum years of experience
     */
    public function scopeMinExperience($query, $years)
    {
        return $query->where('years_experience', '>=', $years);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by skill
     */
    public function scopeBySkill($query, $skillId)
    {
        return $query->where('skill_id', $skillId);
    }

    /**
     * Check if proficiency is basic
     */
    public function isBasic()
    {
        return $this->proficiency_level === 'basic';
    }

    /**
     * Check if proficiency is intermediate
     */
    public function isIntermediate()
    {
        return $this->proficiency_level === 'intermediate';
    }

    /**
     * Check if proficiency is advanced
     */
    public function isAdvanced()
    {
        return $this->proficiency_level === 'advanced';
    }

    /**
     * Get proficiency level badge/color
     */
    public function getProficiencyBadge()
    {
        return match($this->proficiency_level) {
            'basic' => 'secondary',
            'intermediate' => 'warning',
            'advanced' => 'success',
            default => 'primary'
        };
    }

    /**
     * Get formatted years of experience
     */
    public function getFormattedExperience()
    {
        if ($this->years_experience == 0) {
            return 'New to this skill';
        }
        
        return $this->years_experience . ' year' . ($this->years_experience > 1 ? 's' : '') . ' experience';
    }
}