<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'bio',
        'student_id',
        'department',
        'year_level',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'year_level' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * The skills that belong to the user.
     */
    public function skills()
    {
        return $this->belongsToMany(\App\Models\Skill::class, 'user_skill')
            ->withTimestamps()
            ->withPivot(['proficiency_level', 'years_experience']);
    }

    /**
     * The jobs posted by the user (if client).
     */
    public function jobs()
    {
        return $this->hasMany(\App\Models\Job::class, 'client_id');
    }

    /**
     * The applications submitted by the user (if freelancer).
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'freelancer_id');
    }
}
