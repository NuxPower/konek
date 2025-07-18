<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DebugOtpCommand extends Command
{
    protected $signature = 'debug:otp {email}';
    protected $description = 'Debug OTP service for a specific user';

    public function handle(EmailOtpService $otpService)
    {
        $email = $this->argument('email');
        
        $this->info("Debugging OTP service for: {$email}");
        $this->newLine();

        // Find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $this->info("User found: {$user->name} (ID: {$user->id})");
        $this->newLine();

        // Test email configuration
        $this->info('Testing email configuration...');
        if ($otpService->testEmailConfiguration()) {
            $this->info('✓ Email configuration looks good');
        } else {
            $this->error('✗ Email configuration has issues');
            $this->warn('Check the logs for details');
        }
        $this->newLine();

        // Show current OTP status
        $this->info('Current OTP Status:');
        $this->line("- OTP Code: " . ($user->email_otp_code ?? 'None'));
        $this->line("- Expires At: " . ($user->email_otp_expires_at ?? 'None'));
        $this->line("- Attempts: " . ($user->email_otp_attempts ?? 0));
        $this->line("- Last Sent: " . ($user->email_otp_last_sent_at ?? 'None'));
        $this->line("- Is Rate Limited: " . ($otpService->isRateLimited($user) ? 'Yes' : 'No'));
        $this->line("- Is OTP Valid: " . ($otpService->isOtpValid($user) ? 'Yes' : 'No'));
        $this->newLine();

        // Test OTP generation
        if ($this->confirm('Generate and send OTP?')) {
            $this->info('Generating and sending OTP...');
            
            $result = $otpService->generateAndSendOtp($user);
            
            if ($result) {
                $this->info('✓ OTP generated and sent successfully');
                
                // Refresh user to get updated data
                $user->refresh();
                
                $this->info('Updated OTP Status:');
                $this->line("- OTP Code: " . $user->email_otp_code);
                $this->line("- Expires At: " . $user->email_otp_expires_at);
                $this->line("- Attempts: " . $user->email_otp_attempts);
                $this->line("- Last Sent: " . $user->email_otp_last_sent_at);
                
            } else {
                $this->error('✗ Failed to generate/send OTP');
                $this->warn('Check the logs for details');
            }
        }

        // Test OTP verification
        if ($user->email_otp_code && $this->confirm('Test OTP verification?')) {
            $code = $this->ask('Enter OTP code to verify');
            
            if ($code) {
                $this->info('Verifying OTP...');
                
                $result = $otpService->verifyOtp($user, $code);
                
                if ($result) {
                    $this->info('✓ OTP verified successfully');
                } else {
                    $this->error('✗ OTP verification failed');
                    $this->warn('Check the logs for details');
                }
            }
        }

        $this->newLine();
        $this->info('Debug completed. Check storage/logs/laravel.log for detailed logs.');
        
        return 0;
    }
}