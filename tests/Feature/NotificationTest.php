<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use App\Services\ApplicationService;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_is_notified_when_a_freelancer_applies(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->job($client);

        app(ApplicationService::class)->submitApplication([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
            'cover_letter' => 'I have the relevant experience and can deliver this work reliably.',
            'proposed_rate' => 10000,
            'rate_type' => 'fixed',
        ]);

        $notification = $client->notifications()->firstOrFail();

        $this->assertSame('New application received', $notification->data['title']);
        $this->assertStringContainsString($freelancer->name, $notification->data['message']);
    }

    public function test_freelancer_is_notified_when_application_status_changes(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);

        app(ApplicationService::class)->changeStatus($application, 'shortlisted');

        $notification = $freelancer->notifications()->firstOrFail();

        $this->assertSame('Application status updated', $notification->data['title']);
        $this->assertStringContainsString('shortlisted', $notification->data['message']);
    }

    public function test_user_can_open_own_notification_and_it_is_marked_read(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);
        app(ApplicationService::class)->changeStatus($application, 'reviewing');
        $notification = $freelancer->notifications()->firstOrFail();

        $this->actingAs($freelancer)
            ->get(route('notifications.open', $notification))
            ->assertRedirect(route('member.applications.show', $application));

        $this->assertNotNull($notification->refresh()->read_at);
    }

    public function test_user_cannot_open_another_users_notification(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $otherFreelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);
        app(ApplicationService::class)->changeStatus($application, 'reviewing');
        $notification = $freelancer->notifications()->firstOrFail();

        $this->actingAs($otherFreelancer)
            ->get(route('notifications.open', $notification))
            ->assertNotFound();
    }

    public function test_applicant_is_notified_when_a_job_closes(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->job($client);
        $application = Application::factory()->pending()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
        ]);

        app(JobService::class)->changeStatus($job, 'closed');

        $notification = $freelancer->notifications()->firstOrFail();

        $this->assertSame('Job status updated', $notification->data['title']);
        $this->assertSame(route('member.applications.show', $application), $notification->data['url']);
    }

    public function test_client_is_notified_when_application_is_withdrawn(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);

        app(ApplicationService::class)->withdraw($application);

        $notification = $client->notifications()->firstOrFail();

        $this->assertSame('Application withdrawn', $notification->data['title']);
        $this->assertStringContainsString($freelancer->name, $notification->data['message']);
        $this->assertSame(route('member.received-applications.show', $application), $notification->data['url']);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();

        $firstApplication = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);
        $secondApplication = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);

        app(ApplicationService::class)->changeStatus($firstApplication, 'reviewing');
        app(ApplicationService::class)->changeStatus($secondApplication, 'shortlisted');

        $this->assertSame(2, $freelancer->unreadNotifications()->count());

        $this->actingAs($freelancer)
            ->patch(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $freelancer->refresh()->unreadNotifications()->count());
    }

    private function job(User $client): Job
    {
        return Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'status' => 'published',
            'deadline' => now()->addMonth(),
            'applications_count' => 0,
        ]);
    }
}
