<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_workspaces_reject_other_roles(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($client)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($freelancer)->get(route('client.dashboard'))->assertForbidden();
        $this->actingAs($client)->get(route('freelancer.dashboard'))->assertForbidden();
    }

    public function test_client_cannot_access_another_clients_job(): void
    {
        $owner = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $job = $this->createJob($owner);

        $this->actingAs($otherClient)->get(route('client.jobs.show', $job))->assertForbidden();
        $this->actingAs($otherClient)->get(route('client.jobs.edit', $job))->assertForbidden();
        $this->actingAs($otherClient)->patch(route('client.jobs.update', $job), [
            'title' => 'Unauthorized change',
        ])->assertForbidden();

        $this->assertDatabaseMissing('jobs', ['id' => $job->id, 'title' => 'Unauthorized change']);
    }

    public function test_client_can_review_only_applications_for_own_jobs(): void
    {
        $owner = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->create([
            'job_id' => $this->createJob($owner)->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->patch(route('client.applications.status', $application), ['status' => 'shortlisted'])
            ->assertRedirect();

        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'shortlisted']);

        $this->actingAs($otherClient)
            ->get(route('client.applications.show', $application))
            ->assertForbidden();

        $this->actingAs($otherClient)
            ->patch(route('client.applications.status', $application), ['status' => 'accepted'])
            ->assertForbidden();
    }

    public function test_freelancer_cannot_access_another_freelancers_application(): void
    {
        $client = User::factory()->client()->create();
        $owner = User::factory()->freelancer()->create();
        $otherFreelancer = User::factory()->freelancer()->create();
        $application = Application::factory()->create([
            'job_id' => $this->createJob($client)->id,
            'freelancer_id' => $owner->id,
        ]);

        $this->actingAs($otherFreelancer)
            ->get(route('freelancer.applications.show', $application))
            ->assertForbidden();

        $this->actingAs($otherFreelancer)
            ->delete(route('freelancer.applications.destroy', $application))
            ->assertForbidden();

        $this->assertDatabaseHas('applications', ['id' => $application->id]);
    }

    public function test_freelancer_cannot_apply_twice_or_to_unpublished_job(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $publishedJob = $this->createJob($client);
        $draftJob = $this->createJob($client, ['status' => 'draft']);

        Application::factory()->create([
            'job_id' => $publishedJob->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($freelancer)
            ->get(route('freelancer.jobs.apply.create', $publishedJob))
            ->assertForbidden();

        $this->actingAs($freelancer)
            ->get(route('freelancer.jobs.show', $draftJob))
            ->assertNotFound();

        $this->actingAs($freelancer)
            ->post(route('freelancer.jobs.apply', $draftJob), [
                'cover_letter' => 'I am interested in this role.',
            ])
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    private function createJob(User $client, array $attributes = []): Job
    {
        return Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'status' => 'published',
            ...$attributes,
        ]);
    }
}
