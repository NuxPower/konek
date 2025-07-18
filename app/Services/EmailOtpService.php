<?php
namespace App\Services;

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmailOtpService
{
    /**
     * OTP expiration time in minutes
     */
    const OTP_EXPIRY_MINUTES = 10;
   
    /**
     * Maximum OTP attempts
     */
    const MAX_ATTEMPTS = 3;
   
    /**
     * Rate limit for sending OTP (seconds) - DISABLED
     */
    const SEND_RATE_LIMIT = 0; // Set to 0 to disable rate limiting
    
    /**
     * Generate and send OTP code to user's email
     */
    public function generateAndSendOtp(User $user): bool
    {
        try {
            // Rate limiting check DISABLED
            // if ($this->isRateLimited($user)) {
            //     Log::warning('OTP rate limited', [
            //         'user_id' => $user->id,
            //         'email' => $user->email,
            //         'remaining_time' => $this->getRateLimitRemainingTime($user),
            //     ]);
            //     return false;
            // }
            
            // Generate 6-digit OTP
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
           
            Log::info('Generated OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                // 'otp' => $otpCode, // Remove this in production for security!
            ]);
            
            // Check if required database columns exist
            if (!$this->hasRequiredColumns($user)) {
                Log::error('Missing required database columns for OTP', [
                    'user_id' => $user->id,
                    'table' => $user->getTable(),
                ]);
                return false;
            }
           
            // Update user with OTP details using DB transaction
            DB::transaction(function () use ($user, $otpCode) {
                $user->update([
                    'email_otp_code' => $otpCode,
                    'email_otp_expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'email_otp_attempts' => 0,
                    'email_otp_last_sent_at' => Carbon::now(), // Still track when sent, but not for limiting
                ]);
            });
            
            Log::info('OTP data stored in database', [
                'user_id' => $user->id,
                'expires_at' => $user->email_otp_expires_at,
            ]);
            
            // Send OTP via email notification
            $user->notify(new EmailOtpNotification($otpCode));
           
            Log::info('OTP notification sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
           
            return true;
           
        } catch (\Exception $e) {
            Log::error('Failed to generate and send OTP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
           
            // Clear OTP data on failure
            $this->clearOtp($user);
            return false;
        }
    }
    
    /**
     * Verify the OTP code
     */
    public function verifyOtp(User $user, string $code): bool
    {
        try {
            Log::info('Attempting to verify OTP', [
                'user_id' => $user->id,
                'code_length' => strlen($code),
                'stored_code' => $user->email_otp_code,
                'expires_at' => $user->email_otp_expires_at,
                'attempts' => $user->email_otp_attempts,
            ]);
            
            // Check if OTP exists and hasn't expired
            if (!$user->email_otp_code || !$user->email_otp_expires_at) {
                Log::warning('OTP verification failed: No OTP found', [
                    'user_id' => $user->id,
                    'has_code' => !empty($user->email_otp_code),
                    'has_expiry' => !empty($user->email_otp_expires_at),
                ]);
                return false;
            }
            
            // Check if OTP has expired
            if (Carbon::now()->isAfter($user->email_otp_expires_at)) {
                Log::warning('OTP verification failed: OTP expired', [
                    'user_id' => $user->id,
                    'expired_at' => $user->email_otp_expires_at,
                    'current_time' => Carbon::now(),
                ]);
                $this->clearOtp($user);
                return false;
            }
            
            // Check if max attempts exceeded
            if ($user->email_otp_attempts >= self::MAX_ATTEMPTS) {
                Log::warning('OTP verification failed: Max attempts exceeded', [
                    'user_id' => $user->id,
                    'attempts' => $user->email_otp_attempts,
                    'max_attempts' => self::MAX_ATTEMPTS,
                ]);
                $this->clearOtp($user);
                return false;
            }
            
            // Increment attempts
            $user->increment('email_otp_attempts');
            
            // Verify the code using timing-safe comparison
            if (hash_equals($user->email_otp_code, $code)) {
                Log::info('OTP verification successful', [
                    'user_id' => $user->id,
                    'attempts_used' => $user->email_otp_attempts,
                ]);
               
                // Success - clear OTP data
                $this->clearOtp($user);
                return true;
            }
            
            Log::warning('OTP verification failed: Invalid code', [
                'user_id' => $user->id,
                'attempts_remaining' => self::MAX_ATTEMPTS - $user->email_otp_attempts,
            ]);
            return false;
           
        } catch (\Exception $e) {
            Log::error('OTP verification error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
    
    /**
     * Clear OTP data from user
     */
    public function clearOtp(User $user): void
    {
        try {
            $user->update([
                'email_otp_code' => null,
                'email_otp_expires_at' => null,
                'email_otp_attempts' => 0,
            ]);
           
            Log::info('OTP data cleared', ['user_id' => $user->id]);
           
        } catch (\Exception $e) {
            Log::error('Failed to clear OTP data', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Check if user is rate limited for sending OTP - DISABLED
     */
    public function isRateLimited(User $user): bool
    {
        // Rate limiting disabled - always return false
        return false;
        
        // Original code (commented out):
        // if (!$user->email_otp_last_sent_at) {
        //     return false;
        // }
        // return Carbon::now()->diffInSeconds($user->email_otp_last_sent_at) < self::SEND_RATE_LIMIT;
    }
    
    /**
     * Get remaining rate limit time in seconds - DISABLED
     */
    public function getRateLimitRemainingTime(User $user): int
    {
        // Rate limiting disabled - always return 0
        return 0;
        
        // Original code (commented out):
        // if (!$this->isRateLimited($user)) {
        //     return 0;
        // }
        // return self::SEND_RATE_LIMIT - Carbon::now()->diffInSeconds($user->email_otp_last_sent_at);
    }
    
    /**
     * Check if OTP is still valid (not expired and attempts remaining)
     */
    public function isOtpValid(User $user): bool
    {
        return $user->email_otp_code &&
               $user->email_otp_expires_at &&
               Carbon::now()->isBefore($user->email_otp_expires_at) &&
               $user->email_otp_attempts < self::MAX_ATTEMPTS;
    }
    
    /**
     * Get OTP expiry time for display
     */
    public function getOtpExpiryTime(User $user): ?Carbon
    {
        return $user->email_otp_expires_at;
    }
    
    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(User $user): int
    {
        return max(0, self::MAX_ATTEMPTS - $user->email_otp_attempts);
    }
    
    /**
     * Resend OTP (rate limiting disabled)
     */
    public function resendOtp(User $user): bool
    {
        try {
            Log::info('Attempting to resend OTP', ['user_id' => $user->id]);
           
            // Clear existing OTP first
            $this->clearOtp($user);
           
            // Generate and send new OTP (no rate limiting check)
            return $this->generateAndSendOtp($user);
           
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Check if the user table has required OTP columns
     */
    private function hasRequiredColumns(User $user): bool
    {
        $requiredColumns = [
            'email_otp_code',
            'email_otp_expires_at',
            'email_otp_attempts',
            'email_otp_last_sent_at',
        ];
        
        $tableColumns = DB::getSchemaBuilder()->getColumnListing($user->getTable());
       
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $tableColumns)) {
                Log::error("Missing required column: {$column} in {$user->getTable()} table");
                return false;
            }
        }
        return true;
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfiguration(): bool
    {
        try {
            $driver = config('mail.default');
           
            Log::info('Testing email configuration', [
                'driver' => $driver,
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
            ]);
            
            if ($driver === 'log') {
                Log::info('Mail driver is set to log - emails will be logged only');
                return true;
            }
            
            // Test SMTP configuration
            if ($driver === 'smtp') {
                $required = [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username'),
                    'password' => config('mail.mailers.smtp.password'),
                ];
                
                foreach ($required as $key => $value) {
                    if (empty($value)) {
                        Log::error("SMTP configuration missing: {$key}");
                        return false;
                    }
                }
            }
            
            return true;
           
        } catch (\Exception $e) {
            Log::error('Email configuration test failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}