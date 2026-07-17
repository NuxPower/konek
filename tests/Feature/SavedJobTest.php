<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_freelancer_can_save_and_unsave_a_published_job(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->publishedJob();

        $this->actingAs($freelancer)
            ->post(route('member.saved-jobs.store', $job))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_jobs', [
            'user_id' => $freelancer->id,
            'job_id' => $job->id,
        ]);

        $this->actingAs($freelancer)
            ->delete(route('member.saved-jobs.destroy', $job))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_jobs', [
            'user_id' => $freelancer->id,
            'job_id' => $job->id,
        ]);
    }

    public function test_saving_the_same_job_twice_is_idempotent(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->publishedJob();

        $this->actingAs($freelancer)->post(route('member.saved-jobs.store', $job));
        $this->actingAs($freelancer)->post(route('member.saved-jobs.store', $job));

        $this->assertDatabaseCount('saved_jobs', 1);
    }

    public function test_saved_jobs_page_only_shows_the_current_freelancers_open_jobs(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $otherFreelancer = User::factory()->freelancer()->create();
        $ownJob = $this->publishedJob(['title' => 'My saved opportunity']);
        $otherJob = $this->publishedJob(['title' => 'Someone elses saved opportunity']);
        $closedJob = $this->publishedJob(['title' => 'Closed saved opportunity', 'status' => 'closed']);

        $freelancer->savedJobs()->attach([$ownJob->id, $closedJob->id]);
        $otherFreelancer->savedJobs()->attach($otherJob->id);

        $this->actingAs($freelancer)
            ->get(route('member.saved-jobs.index'))
            ->assertOk()
            ->assertSee('My saved opportunity')
            ->assertDontSee('Someone elses saved opportunity')
            ->assertDontSee('Closed saved opportunity');
    }

    public function test_freelancer_cannot_save_a_closed_or_expired_job(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $closedJob = $this->publishedJob(['status' => 'closed']);
        $expiredJob = $this->publishedJob(['deadline' => now()->subDay()]);

        $this->actingAs($freelancer)
            ->post(route('member.saved-jobs.store', $closedJob))
            ->assertNotFound();
        $this->actingAs($freelancer)
            ->post(route('member.saved-jobs.store', $expiredJob))
            ->assertNotFound();

        $this->assertDatabaseCount('saved_jobs', 0);
    }

    private function publishedJob(array $attributes = []): Job
    {
        return Job::factory()->create([
            'client_id' => User::factory()->client(),
            'category_id' => Category::factory(),
            'status' => 'published',
            'deadline' => now()->addMonth(),
            ...$attributes,
        ]);
    }
}
