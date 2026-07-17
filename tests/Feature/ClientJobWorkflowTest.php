<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientJobWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_save_a_job_as_draft_with_skills(): void
    {
        $client = User::factory()->client()->create();
        $category = Category::factory()->create();
        $skills = Skill::factory()->count(2)->create();

        $response = $this->actingAs($client)->post(route('member.posted-jobs.store'), [
            ...$this->validJobData($category),
            'skills' => $skills->pluck('id')->all(),
            'submit_action' => 'draft',
        ]);

        $job = Job::where('client_id', $client->id)->firstOrFail();

        $response->assertRedirect(route('member.posted-jobs.show', $job));
        $this->assertSame('draft', $job->status);
        $this->assertNull($job->published_at);
        $this->assertEqualsCanonicalizing($skills->pluck('id')->all(), $job->skills()->pluck('skills.id')->all());
    }

    public function test_client_can_publish_a_job_immediately(): void
    {
        $client = User::factory()->client()->create();
        $category = Category::factory()->create();

        $this->actingAs($client)->post(route('member.posted-jobs.store'), [
            ...$this->validJobData($category),
            'submit_action' => 'publish',
        ])->assertRedirect();

        $job = Job::where('client_id', $client->id)->firstOrFail();

        $this->assertSame('published', $job->status);
        $this->assertNotNull($job->published_at);
    }

    public function test_client_can_update_all_job_details_and_skills_without_changing_status(): void
    {
        $client = User::factory()->client()->create();
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();
        $oldSkill = Skill::factory()->create();
        $newSkills = Skill::factory()->count(2)->create();
        $job = Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'published',
        ]);
        $job->skills()->attach($oldSkill);

        $response = $this->actingAs($client)->patch(route('member.posted-jobs.update', $job), [
            ...$this->validJobData($newCategory),
            'title' => 'Updated opportunity title',
            'skills' => $newSkills->pluck('id')->all(),
        ]);

        $response->assertRedirect(route('member.posted-jobs.show', $job));
        $job->refresh();

        $this->assertSame('Updated opportunity title', $job->title);
        $this->assertSame($newCategory->id, $job->category_id);
        $this->assertSame('published', $job->status);
        $this->assertEqualsCanonicalizing($newSkills->pluck('id')->all(), $job->skills()->pluck('skills.id')->all());
    }

    public function test_client_can_move_job_through_explicit_statuses(): void
    {
        $client = User::factory()->client()->create();
        $job = Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'status' => 'draft',
            'published_at' => null,
        ]);

        foreach (['published', 'paused', 'closed'] as $status) {
            $this->actingAs($client)
                ->patch(route('member.posted-jobs.status', $job), ['status' => $status])
                ->assertRedirect();

            $this->assertSame($status, $job->refresh()->status);
        }

        $this->assertNotNull($job->published_at);
    }

    public function test_duplicate_job_is_a_draft_and_copies_skills(): void
    {
        $client = User::factory()->client()->create();
        $skills = Skill::factory()->count(2)->create();
        $job = Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
            'title' => 'Original opportunity',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $job->skills()->attach($skills->pluck('id'));

        $response = $this->actingAs($client)->post(route('member.posted-jobs.duplicate', $job));

        $copy = Job::where('client_id', $client->id)->whereKeyNot($job->id)->firstOrFail();
        $response->assertRedirect(route('member.posted-jobs.edit', $copy));
        $this->assertSame('Original opportunity (Copy)', $copy->title);
        $this->assertSame('draft', $copy->status);
        $this->assertNull($copy->published_at);
        $this->assertEqualsCanonicalizing($skills->pluck('id')->all(), $copy->skills()->pluck('skills.id')->all());
    }

    public function test_job_validation_rejects_invalid_budget_range_and_past_deadline(): void
    {
        $client = User::factory()->client()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($client)->post(route('member.posted-jobs.store'), [
            ...$this->validJobData($category),
            'budget_min' => 10000,
            'budget_max' => 5000,
            'deadline' => now()->subDay()->format('Y-m-d'),
            'submit_action' => 'publish',
        ]);

        $response->assertSessionHasErrors(['budget_max', 'deadline']);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_client_can_delete_own_job(): void
    {
        $client = User::factory()->client()->create();
        $job = Job::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory(),
        ]);

        $this->actingAs($client)
            ->delete(route('member.posted-jobs.destroy', $job))
            ->assertRedirect(route('member.posted-jobs.index'));

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    private function validJobData(Category $category): array
    {
        return [
            'title' => 'Build a modern CMU project website',
            'description' => 'We need a developer to design and build a responsive project website with a clear content structure.',
            'requirements' => 'Experience with Laravel, responsive interfaces, Git, and clear written communication is required.',
            'category_id' => $category->id,
            'type' => 'contract',
            'experience_level' => 'intermediate',
            'budget_min' => 10000,
            'budget_max' => 20000,
            'budget_type' => 'fixed',
            'deadline' => now()->addMonth()->format('Y-m-d'),
            'max_applications' => 20,
        ];
    }
}
