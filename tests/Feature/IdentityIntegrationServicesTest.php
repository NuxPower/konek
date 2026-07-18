<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\IdentityVerificationServiceProvider;
use App\Services\Identity\HttpIdentityAnalyzer;
use App\Services\Sms\SmsApiPhSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class IdentityIntegrationServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_refuses_to_boot_with_log_sms_driver(): void
    {
        $originalEnvironment = $this->app['env'];
        $this->app['env'] = 'production';
        Config::set('identity.sms.driver', 'log');

        try {
            (new IdentityVerificationServiceProvider($this->app))->boot();
            $this->fail('Production boot should reject the log SMS driver.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('log driver exposes OTP codes', $exception->getMessage());
        } finally {
            $this->app['env'] = $originalEnvironment;
        }
    }

    public function test_http_identity_analyzer_requires_a_token(): void
    {
        Config::set('identity.analyzer.driver', 'http');
        Config::set('identity.analyzer.token');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('IDENTITY_ANALYZER_TOKEN is required');

        (new IdentityVerificationServiceProvider($this->app))->boot();
    }

    public function test_http_identity_analyzer_parses_success_response(): void
    {
        Storage::fake('private');
        Http::fake([
            'matcher.test/analyze' => Http::response([
                'extracted_school_id' => '2024-12345',
                'ocr_confidence' => 96,
                'id_face_detected' => true,
                'selfie_face_detected' => true,
                'biometric_score' => 95,
                'warnings' => [],
            ]),
        ]);

        Config::set('identity.analyzer.url', 'https://matcher.test/analyze');
        Config::set('identity.analyzer.token', 'secret');

        $user = User::factory()->member()->create();
        $idPath = UploadedFile::fake()->image('id.jpg')->store("identity/{$user->id}", 'private');
        $selfiePath = UploadedFile::fake()->image('selfie.jpg')->store("identity/{$user->id}", 'private');

        $result = app(HttpIdentityAnalyzer::class)->analyze($idPath, $selfiePath, '2024-12345', $user);

        $this->assertSame('2024-12345', $result->extractedSchoolId);
        $this->assertSame(96, $result->ocrConfidence);
        $this->assertTrue($result->idFaceDetected);
        $this->assertTrue($result->selfieFaceDetected);
        $this->assertSame(95, $result->biometricScore);

        Http::assertSent(fn ($request) => $request->hasHeader('x-analyzer-token', 'secret'));
    }

    public function test_http_identity_analyzer_falls_back_to_manual_review_on_failure(): void
    {
        Storage::fake('private');
        Http::fake(['matcher.test/analyze' => Http::response([], 500)]);

        Config::set('identity.analyzer.url', 'https://matcher.test/analyze');

        $user = User::factory()->member()->create();
        $idPath = UploadedFile::fake()->image('id.jpg')->store("identity/{$user->id}", 'private');
        $selfiePath = UploadedFile::fake()->image('selfie.jpg')->store("identity/{$user->id}", 'private');

        $result = app(HttpIdentityAnalyzer::class)->analyze($idPath, $selfiePath, '2024-12345', $user);

        $this->assertNull($result->extractedSchoolId);
        $this->assertFalse($result->idFaceDetected);
        $this->assertNotEmpty($result->warnings);
    }

    public function test_sms_api_ph_sender_posts_expected_payload(): void
    {
        Http::fake([
            'smsapiph.test/*' => Http::response([
                'messageId' => 'test-message-id',
                'status' => 'pending',
            ]),
        ]);

        Config::set('identity.sms.smsapiph.endpoint', 'https://smsapiph.test/send');
        Config::set('identity.sms.smsapiph.api_key', 'api-key');

        app(SmsApiPhSender::class)->send('+639171234567', 'Your verification code is 123456');

        Http::assertSent(fn ($request) => $request->hasHeader('x-api-key', 'api-key')
            && $request['recipient'] === '+639171234567'
            && $request['message'] === 'Your verification code is 123456');
    }

    public function test_sms_api_ph_sender_throws_on_failure(): void
    {
        Http::fake([
            'smsapiph.test/*' => Http::response([], 500),
        ]);

        Config::set('identity.sms.smsapiph.endpoint', 'https://smsapiph.test/send');
        Config::set('identity.sms.smsapiph.api_key', 'api-key');

        $this->expectException(RuntimeException::class);

        app(SmsApiPhSender::class)->send('+639171234567', 'Your verification code is 123456');
    }

    public function test_sms_api_ph_sender_throws_on_provider_rejection(): void
    {
        Http::fake([
            'smsapiph.test/*' => Http::response([
                'messageId' => 'test-message-id',
                'status' => 'failed',
            ]),
        ]);

        Config::set('identity.sms.smsapiph.endpoint', 'https://smsapiph.test/send');
        Config::set('identity.sms.smsapiph.api_key', 'api-key');

        $this->expectException(RuntimeException::class);

        app(SmsApiPhSender::class)->send('+639171234567', 'Your verification code is 123456');
    }
}
