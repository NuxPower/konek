<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicMemberProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_member_profile_is_not_publicly_viewable(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'private-member',
            'is_profile_public' => false,
        ]);

        $this->get(route('members.profile.show', $member->username))
            ->assertNotFound();
    }

    public function test_published_profile_hides_contact_details_until_enabled(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'public-member',
            'is_profile_public' => true,
            'show_email' => false,
            'show_phone' => false,
            'show_links' => false,
            'phone' => '09171234567',
            'introduction' => 'Builds accessible Laravel apps.',
            'portfolio_url' => 'https://example.com',
        ]);

        $this->get(route('members.profile.show', $member->username))
            ->assertOk()
            ->assertSee($member->name)
            ->assertSee('Builds accessible Laravel apps.')
            ->assertDontSee($member->email)
            ->assertDontSee('09171234567')
            ->assertDontSee('https://example.com');
    }

    public function test_published_profile_can_show_allowed_contact_details(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'contact-member',
            'is_profile_public' => true,
            'show_email' => true,
            'show_phone' => true,
            'show_links' => true,
            'phone' => '09171234567',
            'portfolio_url' => 'https://example.com',
        ]);

        $this->get(route('members.profile.show', $member->username))
            ->assertOk()
            ->assertSee($member->email)
            ->assertSee('09171234567')
            ->assertSee('https://example.com');
    }

    public function test_owner_can_preview_private_profile(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'owner-preview',
            'is_profile_public' => false,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($member)
            ->get(route('members.profile.show', $member->username))
            ->assertOk()
            ->assertSee('Your profile is private');
    }

    public function test_resume_download_requires_authentication_and_visible_profile(): void
    {
        Storage::fake('local');

        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'resume-member',
            'is_profile_public' => true,
            'resume_path' => 'resumes/resume.pdf',
        ]);
        $viewer = User::factory()->create(['role' => 'member', 'email_verified_at' => now()]);

        Storage::disk('local')->put($member->resume_path, '%PDF-1.7 resume');

        $this->get(route('members.profile.resume.download', $member->username))
            ->assertRedirect(route('login'));

        $this->actingAs($viewer)
            ->get(route('members.profile.resume.download', $member->username))
            ->assertOk();
    }

    public function test_profile_update_stores_sanitized_photo_and_private_resume(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'upload-member',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($member)
            ->patch(route('member.profile.update'), [
                'username' => 'upload-member',
                'profile_photo' => UploadedFile::fake()->image('avatar.png', 400, 400),
                'resume' => UploadedFile::fake()->createWithContent('resume.pdf', '%PDF-1.7 safe resume'),
                'is_profile_public' => '1',
            ])
            ->assertRedirect(route('member.profile.show'));

        $member->refresh();

        $this->assertNotNull($member->profile_photo_path);
        $this->assertStringEndsWith('.jpg', $member->profile_photo_path);
        Storage::disk('public')->assertExists($member->profile_photo_path);
        Storage::disk('local')->assertExists($member->resume_path);
    }

    public function test_resume_upload_rejects_unsafe_pdf_content(): void
    {
        Storage::fake('local');

        $member = User::factory()->create([
            'role' => 'member',
            'username' => 'unsafe-upload',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($member)
            ->patch(route('member.profile.update'), [
                'username' => 'unsafe-upload',
                'resume' => UploadedFile::fake()->createWithContent('resume.pdf', '%PDF-1.7 /JavaScript alert(1)'),
            ])
            ->assertStatus(422);
    }

    public function test_changing_profile_phone_invalidates_phone_verification(): void
    {
        $member = User::factory()->member()->create([
            'phone' => '09170000000',
            'username' => 'phone-change',
        ]);
        $verification = $member->identityVerification()->create([
            'phone_verified_at' => now(),
            'phone_otp_hash' => 'old-code',
            'phone_otp_expires_at' => now()->addMinutes(5),
            'phone_otp_sent_at' => now(),
        ]);

        $this->actingAs($member)
            ->patch(route('member.profile.update'), [
                'username' => 'phone-change',
                'phone' => '09171111111',
            ])
            ->assertRedirect(route('member.profile.show'));

        $verification->refresh();

        $this->assertSame('09171111111', $member->refresh()->phone);
        $this->assertNull($verification->phone_verified_at);
        $this->assertNull($verification->phone_otp_hash);
        $this->assertSame(0, $verification->proof_points);
    }

    public function test_profile_stores_year_level_as_a_small_number(): void
    {
        $member = User::factory()->member()->create(['username' => 'year-level']);

        $this->actingAs($member)
            ->patch(route('member.profile.update'), [
                'username' => 'year-level',
                'year_level' => 3,
            ])
            ->assertRedirect(route('member.profile.show'));

        $this->assertSame(3, $member->refresh()->year_level);
    }
}
