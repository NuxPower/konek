<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsApiPhSender implements SmsSender
{
    public function send(string $phone, string $message): void
    {
        $apiKey = config('identity.sms.smsapiph.api_key');

        if (! $apiKey) {
            throw new RuntimeException('SMS API PH key is not configured.');
        }

        $response = Http::timeout((int) config('identity.sms.smsapiph.timeout', 15))
            ->withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post((string) config('identity.sms.smsapiph.endpoint'), [
                'recipient' => $phone,
                'message' => $message,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException("SMS API PH returned HTTP {$response->status()}.");
        }

        $payload = $response->json();

        if (is_array($payload)) {
            $providerStatus = strtolower((string) ($payload['status'] ?? $payload['currentStatus'] ?? ''));

            Log::info('SMS API PH accepted message', [
                'message_id' => $payload['messageId'] ?? $payload['id'] ?? null,
                'status' => $payload['status'] ?? $payload['currentStatus'] ?? null,
                'recipient' => $phone,
            ]);

            if (in_array($providerStatus, ['failed', 'rejected', 'error'], true)) {
                throw new RuntimeException('SMS API PH rejected the message.');
            }
        }
    }
}
