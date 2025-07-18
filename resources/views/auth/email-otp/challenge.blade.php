<?php
// Email OTP Blade Views
// Save each section to the corresponding file path

/* 
=== resources/views/auth/email-otp/challenge.blade.php ===
*/
?>
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('For your security, we\'ve sent a 6-digit verification code to your email address.') }}
    </div>

    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">
                    Code sent to: {{ substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1] }}
                </p>
                @if($expiryTime)
                    <p class="text-xs text-blue-600" id="countdown">
                        Expires in: <span id="time-remaining"></span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('email-otp.verify') }}" id="otp-form">
        @csrf

        <div class="mb-4">
            <x-input-label for="otp_code" :value="__('Verification Code')" />
            <x-text-input id="otp_code" 
                          class="block mt-1 w-full text-center text-lg font-mono tracking-widest" 
                          type="text" 
                          name="otp_code" 
                          :value="old('otp_code')" 
                          required 
                          autofocus 
                          maxlength="6"
                          pattern="\d{6}"
                          placeholder="000000"
                          autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
            
            @if($remainingAttempts > 0)
                <p class="mt-1 text-xs text-gray-600">
                    {{ $remainingAttempts }} attempt(s) remaining
                </p>
            @endif
        </div>

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                @if($canResend)
                    <form method="POST" action="{{ route('email-otp.resend') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="text-sm text-blue-600 hover:text-blue-900 underline"
                                id="resend-btn">
                            {{ __('Resend Code') }}
                        </button>
                    </form>
                @else
                    <span class="text-sm text-gray-500" id="resend-cooldown">
                        Resend in <span id="cooldown-time">{{ $resendCooldown }}</span>s
                    </span>
                @endif

                <a href="{{ route('email-otp.cancel') }}" 
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Cancel') }}
                </a>
            </div>

            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when 6 digits are entered
            const otpInput = document.getElementById('otp_code');
            const form = document.getElementById('otp-form');
            
            otpInput.addEventListener('input', function() {
                // Remove non-digits
                this.value = this.value.replace(/\D/g, '');
                
                // Auto-submit when 6 digits are entered
                if (this.value.length === 6) {
                    setTimeout(() => form.submit(), 100);
                }
            });

            // OTP expiry countdown
            @if($expiryTime)
                const expiryTime = new Date('{{ $expiryTime->toISOString() }}');
                const countdownElement = document.getElementById('time-remaining');
                
                function updateCountdown() {
                    const now = new Date();
                    const timeLeft = expiryTime - now;
                    
                    if (timeLeft <= 0) {
                        countdownElement.textContent = 'Expired';
                        countdownElement.parentElement.classList.add('text-red-600');
                        return;
                    }
                    
                    const minutes = Math.floor(timeLeft / 60000);
                    const seconds = Math.floor((timeLeft % 60000) / 1000);
                    countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
                
                updateCountdown();
                setInterval(updateCountdown, 1000);
            @endif

            // Resend cooldown countdown
            @if(!$canResend && $resendCooldown > 0)
                let cooldownTime = {{ $resendCooldown }};
                const cooldownElement = document.getElementById('cooldown-time');
                const resendCooldownElement = document.getElementById('resend-cooldown');
                
                const cooldownInterval = setInterval(function() {
                    cooldownTime--;
                    cooldownElement.textContent = cooldownTime;
                    
                    if (cooldownTime <= 0) {
                        clearInterval(cooldownInterval);
                        location.reload(); // Refresh to show resend button
                    }
                }, 1000);
            @endif
        });
    </script>
</x-guest-layout>
