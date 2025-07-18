<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailOtpController extends Controller
{
    public function __construct(
        protected EmailOtpService $emailOtpService
    ) {}

    /**
     * Show the email OTP verification page
     * FIXED: Changed return type to allow both View and RedirectResponse
     */
    public function challenge(): View|RedirectResponse
    {
        $userId = session('login.id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if OTP is still valid
        if (!$this->emailOtpService->isOtpValid($user)) {
            // Generate new OTP if none exists or expired
            if (!$this->emailOtpService->generateAndSendOtp($user)) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Failed to send verification code. Please try again.']);
            }
        }

        return view('auth.email-otp.challenge', [
            'user' => $user,
            'expiryTime' => $this->emailOtpService->getOtpExpiryTime($user),
            'remainingAttempts' => $this->emailOtpService->getRemainingAttempts($user),
            'canResend' => true, // FIXED: Always true (rate limiting disabled)
            'resendCooldown' => 0, // FIXED: Always 0 (no cooldown)
        ]);
    }

    /**
     * Verify the email OTP code
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp_code.required' => 'Please enter the verification code.',
            'otp_code.size' => 'Verification code must be 6 digits.',
            'otp_code.regex' => 'Verification code must contain only numbers.',
        ]);

        $userId = session('login.id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['otp_code' => 'Session expired. Please log in again.']);
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['otp_code' => 'User not found. Please log in again.']);
        }

        // Verify the OTP
        if ($this->emailOtpService->verifyOtp($user, $request->otp_code)) {
            // Clear the login session data
            session()->forget(['login.id', 'login.remember']);
            
            // Log the user in
            Auth::login($user, session('login.remember', false));
            
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('status', 'Successfully logged in!');
        }

        // Get updated remaining attempts after failed verification
        $remainingAttempts = $this->emailOtpService->getRemainingAttempts($user);
        
        if ($remainingAttempts <= 0) {
            // Max attempts exceeded - redirect to login
            session()->forget(['login.id', 'login.remember']);
            
            return redirect()->route('login')
                ->withErrors(['email' => 'Too many failed verification attempts. Please log in again.']);
        }

        return back()->withErrors([
            'otp_code' => "Invalid verification code. You have {$remainingAttempts} attempt(s) remaining.",
        ]);
    }

    /**
     * Resend the OTP code
     * FIXED: Removed rate limiting check
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('login.id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return redirect()->route('login');
        }

        // REMOVED: Rate limiting check
        // if ($this->emailOtpService->isRateLimited($user)) {
        //     $remainingTime = $this->emailOtpService->getRateLimitRemainingTime($user);
        //     return back()->withErrors([
        //         'otp_code' => "Please wait {$remainingTime} seconds before requesting a new code.",
        //     ]);
        // }

        // Resend OTP without rate limiting
        if ($this->emailOtpService->resendOtp($user)) {
            return back()->with('status', 'A new verification code has been sent to your email.');
        }

        return back()->withErrors([
            'otp_code' => 'Failed to send verification code. Please try again.',
        ]);
    }

    /**
     * Cancel OTP verification and return to login
     */
    public function cancel(): RedirectResponse
    {
        $userId = session('login.id');
        
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                // Clear OTP data
                $this->emailOtpService->clearOtp($user);
            }
        }

        // Clear session data
        session()->forget(['login.id', 'login.remember']);
        
        return redirect()->route('login')
            ->with('status', 'Verification cancelled. Please log in again.');
    }
}