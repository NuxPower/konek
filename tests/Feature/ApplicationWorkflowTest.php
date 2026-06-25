<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_freelancer_can_submit_application_and_job_count_is_updated(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->publishedJob($client);

        $response = $this->actingAs($freelancer)->post(route('freelancer.jobs.apply', $job), $this->applicationData());

        $application = Application::where('job_id', $job->id)->where('freelancer_id', $freelancer->id)->firstOrFail();
        $response->assertRedirect(route('freelancer.applications.show', $application));
        $this->assertSame('pending', $application->status);
        $this->assertSame(1, $job->refresh()->applications_count);
    }

    public function test_application_limit_is_enforced(): void
    {
        $client = User::factory()->client()->create();
        $firstFreelancer = User::factory()->freelancer()->create();
        $secondFreelancer = User::factory()->freelancer()->create();
        $job = $this->publishedJob($client, ['max_applications' => 1]);

        Application::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $firstFreelancer->id,
            'status' => 'pending',
        ]);
        $job->update(['applications_count' => 1]);

        $this->actingAs($secondFreelancer)
            ->post(route('freelancer.jobs.apply', $job), $this->applicationData())
            ->assertForbidden();

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_freelancer_can_edit_only_a_pending_application(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = $this->application($client, $freelancer, 'pending');

        $this->actingAs($freelancer)
            ->patch(route('freelancer.applications.update', $application), [
                ...$this->applicationData(),
                'cover_letter' => 'Updated cover letter with enough detail to explain my relevant experience and delivery approach.',
            ])
            ->assertRedirect();

        $this->assertStringStartsWith('Updated cover letter', $application->refresh()->cover_letter);

        $application->update(['status' => 'reviewing']);

        $this->actingAs($freelancer)
            ->patch(route('freelancer.applications.update', $application), $this->applicationData())
            ->assertForbidden();
    }

    public function test_withdrawal_keeps_record_and_reduces_active_count(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = $this->application($client, $freelancer, 'shortlisted');
        $application->job->update(['applications_count' => 1]);

        $this->actingAs($freelancer)
            ->delete(route('freelancer.applications.destroy', $application))
            ->assertRedirect(route('freelancer.applications.index'));

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'withdrawn',
        ]);
        $this->assertSame(0, $application->job->refresh()->applications_count);
    }

    public function test_client_can_review_shortlist_and_accept_application_with_timestamps(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = $this->application($client, $freelancer, 'pending');

        foreach (['reviewing', 'shortlisted', 'accepted'] as $status) {
            $this->actingAs($client)
                ->patch(route('client.applications.status', $application), ['status' => $status])
                ->assertRedirect();

            $this->assertSame($status, $application->refresh()->status);
        }

        $this->assertNotNull($application->reviewed_at);
        $this->assertNotNull($application->accepted_at);
    }

    public function test_terminal_application_status_cannot_be_changed(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = $this->application($client, $freelancer, 'accepted');

        $this->actingAs($client)
            ->from(route('client.applications.show', $application))
            ->patch(route('client.applications.status', $application), ['status' => 'rejected'])
            ->assertSessionHasErrors('status');

        $this->assertSame('accepted', $application->refresh()->status);
    }

    public function test_client_can_save_private_notes_on_own_application(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = $this->application($client, $freelancer);

        $this->actingAs($client)
            ->patch(route('client.applications.notes', $application), [
                'client_notes' => 'Strong portfolio. Ask about availability during the interview.',
            ])
            ->assertRedirect();

        $this->assertSame(
            'Strong portfolio. Ask about availability during the interview.',
            $application->refresh()->client_notes
        );
    }

    public function test_accepted_or_rejected_application_cannot_be_withdrawn(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();

        foreach (['accepted', 'rejected'] as $status) {
            $application = $this->application($client, $freelancer, $status);

            $this->actingAs($freelancer)
                ->delete(route('freelancer.applications.destroy', $application))
                ->assertForbidden();
        }
    }

    private function application(User $client, User $freelancer, string $status = 'pending'): Application
    {
        return Application::factory()->create([
            'job_id' => $this->publishedJob($client)->id,
            'freelancer_id' => $freelancer->id,
            'status' => $status,
        ]);
    }

    private function publishedJob(User $client, array $attributes = []): Job
    {
        return Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'status' => 'published',
            'deadline' => now()->addMonth(),
            'applications_count' => 0,
            ...$attributes,
        ]);
    }

    private function applicationData(): array
    {
        return [
            'cover_letter' => 'I have relevant experience for this opportunity and can provide a clear plan, regular updates, and reliable delivery.',
            'proposed_rate' => 12000,
            'rate_type' => 'fixed',
            'estimated_hours' => 40,
            'portfolio_links' => 'https://example.com/portfolio',
        ];
    }
}
