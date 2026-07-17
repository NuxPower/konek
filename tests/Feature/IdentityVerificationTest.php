<?php

namespace Tests\Feature;

use App\Models\IdentityVerification;
use App\Models\User;
use App\Services\Identity\IdentityAnalysisResult;
use App\Services\Identity\IdentityAnalyzer;
use App\Services\Sms\SmsSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class IdentityVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_high_confidence_identity_submission_is_auto_verified(): void
    {
        Storage::fake('private');

        $this->app->bind(IdentityAnalyzer::class, fn () => new class implements IdentityAnalyzer
        {
            public function analyze(string $idDocumentPath, string $selfiePath, string $schoolId, User $user): IdentityAnalysisResult
            {
                return new IdentityAnalysisResult($schoolId, 98, true, true, 97);
            }
        });

        $user = User::factory()->member()->create();

        $this->actingAs($user)->post(route('member.identity.submit'), [
            'school_id' => '2024-12345',
            'id_document' => UploadedFile::fake()->image('id.jpg'),
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ])->assertRedirect(route('member.identity.edit'));

        $verification = $user->identityVerification()->first();

        $this->assertSame(IdentityVerification::STATUS_VERIFIED, $verification->status);
        $this->assertSame(80, $verification->proof_points);
        $this->assertNull($verification->id_document_path);
        $this->assertNull($verification->selfie_path);
    }

    public function test_uncertain_identity_submission_goes_to_admin_review(): void
    {
        Storage::fake('private');

        $this->app->bind(IdentityAnalyzer::class, fn () => new class implements IdentityAnalyzer
        {
            public function analyze(string $idDocumentPath, string $selfiePath, string $schoolId, User $user): IdentityAnalysisResult
            {
                return new IdentityAnalysisResult('DIFFERENT-ID', 85, true, true, 70, ['ID number mismatch.']);
            }
        });

        $user = User::factory()->member()->create();

        $this->actingAs($user)->post(route('member.identity.submit'), [
            'school_id' => '2024-12345',
            'id_document' => UploadedFile::fake()->image('id.jpg'),
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ])->assertRedirect(route('member.identity.edit'));

        $verification = $user->identityVerification()->first();

        $this->assertSame(IdentityVerification::STATUS_PENDING, $verification->status);
        $this->assertSame(0, $verification->proof_points);
        Storage::disk('private')->assertExists($verification->id_document_path);
        Storage::disk('private')->assertExists($verification->selfie_path);
    }

    public function test_admin_approval_awards_unified_proof_points_and_deletes_evidence(): void
    {
        Storage::fake('private');

        $user = User::factory()->member()->create();
        $admin = User::factory()->admin()->create();
        $idPath = UploadedFile::fake()->image('id.jpg')->store("identity/{$user->id}", 'private');
        $selfiePath = UploadedFile::fake()->image('selfie.jpg')->store("identity/{$user->id}", 'private');

        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'school_id' => '2024-12345',
            'status' => IdentityVerification::STATUS_PENDING,
            'id_document_path' => $idPath,
            'selfie_path' => $selfiePath,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.identity.approve', $verification))
            ->assertRedirect(route('admin.identity.show', $verification));

        $verification->refresh();

        $this->assertSame(IdentityVerification::STATUS_VERIFIED, $verification->status);
        $this->assertSame(80, $verification->proof_points);
        Storage::disk('private')->assertMissing($idPath);
        Storage::disk('private')->assertMissing($selfiePath);
    }

    public function test_phone_otp_verification_awards_phone_points(): void
    {
        $sender = new class implements SmsSender
        {
            public ?string $message = null;

            public function send(string $phone, string $message): void
            {
                $this->message = $message;
            }
        };

        $this->app->instance(SmsSender::class, $sender);

        $user = User::factory()->member()->create();

        $this->actingAs($user)->post(route('member.identity.phone.send'), [
            'phone' => '09171234567',
        ])->assertRedirect(route('member.identity.edit'));

        preg_match('/(\d{6})/', (string) $sender->message, $matches);

        $this->assertNotEmpty($matches[1] ?? null);

        $this->actingAs($user)->post(route('member.identity.phone.verify'), [
            'code' => $matches[1],
        ])->assertRedirect(route('member.identity.edit'));

        $verification = $user->identityVerification()->first();

        $this->assertNotNull($verification->phone_verified_at);
        $this->assertSame(20, $verification->proof_points);
    }

    public function test_failed_sms_does_not_change_phone_or_retain_verification_for_a_new_number(): void
    {
        $this->app->instance(SmsSender::class, new class implements SmsSender
        {
            public function send(string $phone, string $message): void
            {
                throw new RuntimeException('Provider unavailable.');
            }
        });

        $user = User::factory()->member()->create(['phone' => '09170000000']);
        $verification = $user->identityVerification()->create([
            'phone_verified_at' => now(),
        ]);

        $this->actingAs($user)->post(route('member.identity.phone.send'), [
            'phone' => '09171111111',
        ])->assertSessionHasErrors('phone');

        $this->assertSame('09170000000', $user->refresh()->phone);
        $this->assertNotNull($verification->refresh()->phone_verified_at);
        $this->assertNull($verification->phone_otp_hash);
    }

    public function test_admin_cannot_change_a_decided_identity_proof(): void
    {
        $admin = User::factory()->admin()->create();
        $verification = User::factory()->member()->create()->identityVerification()->create([
            'status' => IdentityVerification::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.identity.reject', $verification), ['rejection_reason' => 'Changed decision'])
            ->assertConflict();

        $this->assertSame(IdentityVerification::STATUS_VERIFIED, $verification->refresh()->status);
    }
}
