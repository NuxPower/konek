<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $type
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpToken whereUserId($value)
 * @mixin \Eloquent
 */
class OtpToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'token',
        'type',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
    ];

    /**
     * OTP token types.
     */
    const TYPE_LOGIN = 'login';
    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_TWO_FACTOR = 'two_factor';

    /**
     * Get the user that owns the OTP token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the token is used.
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Mark the token as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Generate a new OTP token.
     */
    public static function generateToken(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP token for a user.
     */
    public static function createForUser(User $user, string $type, int $expiresInMinutes = 10): self
    {
        return self::create([
            'user_id' => $user->id,
            'token' => self::generateToken(),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope a query to only include valid tokens.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->whereNull('used_at');
    }

    /**
     * Scope a query to only include tokens of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Clean up expired tokens.
     */
    public static function cleanupExpired(): void
    {
        self::where('expires_at', '<', now())->delete();
    }
}