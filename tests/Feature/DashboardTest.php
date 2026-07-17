<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_surfaces_review_queue_and_upcoming_deadlines(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->job($client, [
            'title' => 'Urgent platform audit',
            'deadline' => now()->addDays(5),
        ]);
        $application = Application::factory()->pending()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('applicationsNeedingReview', 1)
            ->assertViewHas('reviewQueue', fn ($queue) => $queue->contains($application))
            ->assertViewHas('upcomingDeadlines', fn ($jobs) => $jobs->contains($job));
    }

    public function test_client_dashboard_only_includes_the_clients_own_review_queue(): void
    {
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $ownApplication = Application::factory()->pending()->create([
            'job_id' => $this->job($client)->id,
            'freelancer_id' => $freelancer->id,
        ]);
        $otherApplication = Application::factory()->pending()->create([
            'job_id' => $this->job($otherClient)->id,
            'freelancer_id' => User::factory()->freelancer(),
        ]);

        $this->actingAs($client)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertViewHas('applicationsNeedingReview', 1)
            ->assertViewHas('reviewQueue', function ($queue) use ($ownApplication, $otherApplication) {
                return $queue->contains($ownApplication) && ! $queue->contains($otherApplication);
            });
    }

    public function test_freelancer_recommendations_exclude_applied_and_closed_jobs(): void
    {
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $skill = Skill::factory()->create();
        $freelancer->skills()->attach($skill);

        $recommendedJob = $this->job($client, ['title' => 'Recommended Laravel role']);
        $recommendedJob->skills()->attach($skill);
        $appliedJob = $this->job($client, ['title' => 'Already applied role']);
        $closedJob = $this->job($client, [
            'title' => 'Closed role',
            'status' => 'closed',
        ]);
        Application::factory()->pending()->create([
            'job_id' => $appliedJob->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($freelancer)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertViewHas('recommendedJobs', function ($jobs) use ($recommendedJob, $appliedJob, $closedJob) {
                return $jobs->contains($recommendedJob)
                    && ! $jobs->contains($appliedJob)
                    && ! $jobs->contains($closedJob);
            });
    }

    public function test_dashboard_stats_endpoints_return_real_role_scoped_counts(): void
    {
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $ownJob = $this->job($client);
        $this->job($otherClient);
        Application::factory()->pending()->create([
            'job_id' => $ownJob->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($client)
            ->getJson(route('member.dashboard.stats'))
            ->assertOk()
            ->assertJson([
                'posted_jobs' => 1,
                'published_jobs' => 1,
                'received_applications' => 1,
                'applications_needing_review' => 1,
            ]);

        $this->actingAs($freelancer)
            ->getJson(route('member.dashboard.stats'))
            ->assertOk()
            ->assertJson([
                'submitted_applications' => 1,
                'active_applications' => 1,
            ]);
    }

    private function job(User $client, array $attributes = []): Job
    {
        return Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'status' => 'published',
            'published_at' => now()->subDay(),
            'deadline' => now()->addMonth(),
            ...$attributes,
        ]);
    }
}
