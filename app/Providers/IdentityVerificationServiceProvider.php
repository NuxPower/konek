<?php

namespace App\Providers;

use App\Services\Identity\HttpIdentityAnalyzer;
use App\Services\Identity\IdentityAnalyzer;
use App\Services\Identity\ManualReviewIdentityAnalyzer;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\SmsApiPhSender;
use App\Services\Sms\SmsSender;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class IdentityVerificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $smsDriver = strtolower(trim((string) config('identity.sms.driver')));

        if ($this->app->environment('production') && in_array($smsDriver, ['', 'log'], true)) {
            throw new RuntimeException('SMS_DRIVER must use a production SMS provider; the log driver exposes OTP codes.');
        }

        if (config('identity.analyzer.driver') === 'http' && blank(config('identity.analyzer.token'))) {
            throw new RuntimeException('IDENTITY_ANALYZER_TOKEN is required when the HTTP identity analyzer is enabled.');
        }
    }

    public function register(): void
    {
        $this->app->bind(IdentityAnalyzer::class, match (config('identity.analyzer.driver')) {
            'http' => HttpIdentityAnalyzer::class,
            default => ManualReviewIdentityAnalyzer::class,
        });

        $this->app->bind(SmsSender::class, match (config('identity.sms.driver')) {
            'smsapiph' => SmsApiPhSender::class,
            default => LogSmsSender::class,
        });
    }
}
