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

    public function test_admin_workspace_rejects_members(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($member)->get(route('member.dashboard'))->assertOk();
    }

    public function test_member_identity_workspace_rejects_admins(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('member.identity.edit'))->assertForbidden();
    }

    public function test_admin_can_create_and_update_an_inactive_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Inactive Member',
            'email' => 'inactive-member@cmu.edu.ph',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
            'is_active' => false,
        ])->assertRedirect(route('admin.users.index'));

        $member = User::where('email', 'inactive-member@cmu.edu.ph')->firstOrFail();
        $this->assertFalse($member->is_active);

        $this->actingAs($admin)->put(route('admin.users.update', $member), [
            'name' => $member->name,
            'email' => $member->email,
            'role' => 'member',
            'is_active' => true,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue($member->refresh()->is_active);
    }

    public function test_client_cannot_access_another_clients_job(): void
    {
        $owner = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $job = $this->createJob($owner);

        $this->actingAs($otherClient)->get(route('member.posted-jobs.show', $job))->assertForbidden();
        $this->actingAs($otherClient)->get(route('member.posted-jobs.edit', $job))->assertForbidden();
        $this->actingAs($otherClient)->patch(route('member.posted-jobs.update', $job), [
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
            ->patch(route('member.received-applications.status', $application), ['status' => 'shortlisted'])
            ->assertRedirect();

        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'shortlisted']);

        $this->actingAs($otherClient)
            ->get(route('member.received-applications.show', $application))
            ->assertForbidden();

        $this->actingAs($otherClient)
            ->patch(route('member.received-applications.status', $application), ['status' => 'accepted'])
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
            ->get(route('member.applications.show', $application))
            ->assertForbidden();

        $this->actingAs($otherFreelancer)
            ->delete(route('member.applications.destroy', $application))
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
            ->get(route('member.jobs.apply.create', $publishedJob))
            ->assertForbidden();

        $this->actingAs($freelancer)
            ->get(route('member.jobs.show', $draftJob))
            ->assertNotFound();

        $this->actingAs($freelancer)
            ->post(route('member.jobs.apply', $draftJob), [
                'cover_letter' => 'I am interested in this role.',
            ])
            ->assertForbidden();
    }

    public function test_member_cannot_apply_to_own_job(): void
    {
        $member = User::factory()->member()->create();
        $job = $this->createJob($member);

        $this->actingAs($member)
            ->get(route('member.jobs.apply.create', $job))
            ->assertForbidden();

        $this->actingAs($member)
            ->post(route('member.jobs.apply', $job), [
                'cover_letter' => 'I should not be allowed to apply to work that I posted myself.',
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
