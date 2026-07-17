<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_page_views_do_not_create_activity_log_rows(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get(route('member.dashboard'))->assertOk();

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_successful_mutations_are_logged(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->patch(route('member.profile.update'), [
                'name' => 'Updated Client',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $client->id,
            'description' => 'PATCH member/profile',
        ]);
    }

    public function test_freelancer_resume_is_stored_privately_and_downloadable_only_by_owner(): void
    {
        Storage::fake('local');
        $freelancer = User::factory()->freelancer()->create();
        $otherFreelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)
            ->post(route('member.profile.resume'), [
                'resume' => UploadedFile::fake()->createWithContent('resume.pdf', '%PDF-1.7 safe resume'),
            ])
            ->assertRedirect();

        $path = $freelancer->refresh()->resume_path;
        Storage::disk('local')->assertExists($path);

        $this->actingAs($freelancer)
            ->get(route('member.profile.resume.download'))
            ->assertOk();

        $this->actingAs($otherFreelancer)
            ->get(route('member.profile.resume.download'))
            ->assertNotFound();
    }
}
