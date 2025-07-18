<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Carbon\Carbon;

class TwoFactorAuthenticationProvider
{
    /**
     * Generate and send a 2FA code to the user
     */
    public function generateCode(User $user): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store code in cache for 5 minutes
        Cache::put(
            $this->getCacheKey($user->id),
            [
                'code' => $code,
                'attempts' => 0,
                'expires_at' => Carbon::now()->addMinutes(5)
            ],
            300 // 5 minutes
        );
        
        // Send email with the code
        Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->name));
        
        return $code;
    }
    
    /**
     * Verify the 2FA code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $cacheKey = $this->getCacheKey($user->id);
        $storedData = Cache::get($cacheKey);
        
        if (!$storedData) {
            return false;
        }
        
        // Check if code has expired
        if (Carbon::now()->isAfter($storedData['expires_at'])) {
            Cache::forget($cacheKey);
            return false;
        }
        
        // Check attempts limit (max 3 attempts)
        if ($storedData['attempts'] >= 3) {
            Cache::forget($cacheKey);
            return false;
        }
        
        // Increment attempts
        $storedData['attempts']++;
        Cache::put($cacheKey, $storedData, 300);
        
        // Verify the code
        if ($storedData['code'] === $code) {
            Cache::forget($cacheKey);
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user has a pending 2FA code
     */
    public function hasPendingCode(User $user): bool
    {
        $storedData = Cache::get($this->getCacheKey($user->id));
        
        if (!$storedData) {
            return false;
        }
        
        return Carbon::now()->isBefore($storedData['expires_at']);
    }
    
    /**
     * Get remaining attempts for 2FA code
     */
    public function getRemainingAttempts(User $user): int
    {
        $storedData = Cache::get($this->getCacheKey($user->id));
        
        if (!$storedData) {
            return 0;
        }
        
        return max(0, 3 - $storedData['attempts']);
    }
    
    /**
     * Clear any pending 2FA code
     */
    public function clearCode(User $user): void
    {
        Cache::forget($this->getCacheKey($user->id));
    }
    
    /**
     * Get time until code expires
     */
    public function getTimeUntilExpiry(User $user): ?int
    {
        $storedData = Cache::get($this->getCacheKey($user->id));
        
        if (!$storedData) {
            return null;
        }
        
        return Carbon::now()->diffInSeconds($storedData['expires_at'], false);
    }
    
    /**
     * Generate cache key for user's 2FA code
     */
    private function getCacheKey(int $userId): string
    {
        return "two_factor_code_{$userId}";
    }
}