<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpToken;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Display the two-factor authentication challenge view.
     */
    public function create(): View
    {
        // Check if user has 2FA session
        if (!session('two_factor_user_id')) {
            return view('auth.login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Handle the two-factor authentication challenge.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('two_factor_user_id');
        $remember = session('remember', false);

        if (!$userId) {
            return redirect()->route('login');
        }

        // Rate limiting
        $key = 'two-factor-attempts:' . $userId;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'code' => [
                    'Too many attempts. Please try again in ' . $seconds . ' seconds.'
                ]
            ]);
        }

        $user = User::findOrFail($userId);

        // Find valid OTP token
        $otpToken = OtpToken::where('user_id', $user->id)
            ->where('type', OtpToken::TYPE_TWO_FACTOR)
            ->where('token', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpToken) {
            RateLimiter::hit($key);
            
            throw ValidationException::withMessages([
                'code' => ['The provided code is invalid or has expired.']
            ]);
        }

        // Delete the used token
        $otpToken->delete();

        // Clear rate limiting
        RateLimiter::clear($key);

        // Clear 2FA session data
        session()->forget(['two_factor_user_id', 'remember']);

        // Log the user in
        Auth::login($user, $remember);

        $request->session()->regenerate();

        // Redirect based on user role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Resend the two-factor authentication code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('two_factor_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        // Rate limiting for resend
        $key = 'two-factor-resend:' . $userId;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'code' => 'Too many resend attempts. Please try again in ' . $seconds . ' seconds.'
            ]);
        }

        $user = User::findOrFail($userId);

        // Delete existing tokens
        OtpToken::where('user_id', $user->id)
            ->where('type', OtpToken::TYPE_TWO_FACTOR)
            ->delete();

        // Generate new OTP token
        $otpToken = OtpToken::createForUser(
            $user,
            OtpToken::TYPE_TWO_FACTOR,
            5 // 5 minutes expiry
        );

        // Send new code
        $user->notify(new TwoFactorCodeNotification($otpToken->token));

        RateLimiter::hit($key);

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'client':
                return redirect()->intended(route('client.dashboard'));
            case 'freelancer':
                return redirect()->intended(route('freelancer.dashboard'));
            default:
                return redirect()->intended(route('dashboard'));
        }
    }
}