<?php

return [
    'analyzer' => [
        'driver' => env('IDENTITY_ANALYZER', 'manual'),
        'url' => env('IDENTITY_ANALYZER_URL', 'http://127.0.0.1:8001/analyze-identity'),
        'token' => env('IDENTITY_ANALYZER_TOKEN'),
        'timeout' => env('IDENTITY_ANALYZER_TIMEOUT', 60),
    ],
    'auto_approve' => [
        'ocr_confidence' => env('IDENTITY_OCR_CONFIDENCE', 90),
        'biometric_score' => env('IDENTITY_BIOMETRIC_SCORE', 90),
    ],
    'phone_otp' => [
        'ttl_minutes' => env('PHONE_OTP_TTL_MINUTES', 10),
        'resend_seconds' => env('PHONE_OTP_RESEND_SECONDS', 60),
        'max_attempts' => env('PHONE_OTP_MAX_ATTEMPTS', 5),
    ],
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'smsapiph' => [
            'endpoint' => env('SMSAPIPH_ENDPOINT', 'https://smsapiph.onrender.com/api/v1/send/sms'),
            'api_key' => env('SMSAPIPH_API_KEY'),
            'timeout' => env('SMSAPIPH_TIMEOUT', 15),
        ],
    ],
];
