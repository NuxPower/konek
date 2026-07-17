<?php

namespace App\Providers;

use App\Services\Identity\HttpIdentityAnalyzer;
use App\Services\Identity\IdentityAnalyzer;
use App\Services\Identity\ManualReviewIdentityAnalyzer;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\SmsApiPhSender;
use App\Services\Sms\SmsSender;
use Illuminate\Support\ServiceProvider;

class IdentityVerificationServiceProvider extends ServiceProvider
{
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
