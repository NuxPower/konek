<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_job_management(): void
    {
        $job = Job::factory()->create();

        $this->get(route('admin.jobs.index'))->assertForbidden();
        $this->get(route('admin.jobs.show', $job))->assertForbidden();
    }

    public function test_member_cannot_access_job_management(): void
    {
        $member = User::factory()->member()->create();
        $job = Job::factory()->create();

        $this->actingAs($member)->get(route('admin.jobs.index'))->assertForbidden();
        $this->actingAs($member)->get(route('admin.jobs.show', $job))->assertForbidden();
        $this->actingAs($member)->get(route('admin.jobs.edit', $job))->assertForbidden();
        $this->actingAs($member)->put(route('admin.jobs.update', $job), [])->assertForbidden();
        $this->actingAs($member)->delete(route('admin.jobs.destroy', $job))->assertForbidden();
        $this->actingAs($member)->patch(route('admin.jobs.toggle-status', $job))->assertForbidden();
    }

    public function test_admin_can_view_job_index(): void
    {
        $admin = User::factory()->admin()->create();
        Job::factory()->count(3)->create();

        $this->actingAs($admin)->get(route('admin.jobs.index'))->assertOk();
    }

    public function test_admin_can_view_job_show(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create();

        $this->actingAs($admin)->get(route('admin.jobs.show', $job))->assertOk();
    }

    public function test_admin_can_view_job_edit_with_active_clients_and_categories_only(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create();

        $activeClient = User::factory()->client()->create(['is_active' => true]);
        $inactiveClient = User::factory()->client()->create(['is_active' => false]);
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->inactive()->create();

        $response = $this->actingAs($admin)->get(route('admin.jobs.edit', $job));

        $response->assertOk();
        $response->assertViewHas('clients', function ($clients) use ($activeClient, $inactiveClient) {
            return $clients->contains('id', $activeClient->id) && ! $clients->contains('id', $inactiveClient->id);
        });
        $response->assertViewHas('categories', function ($categories) use ($activeCategory, $inactiveCategory) {
            return $categories->contains('id', $activeCategory->id) && ! $categories->contains('id', $inactiveCategory->id);
        });
    }

    public function test_admin_can_update_job(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.jobs.update', $job), [
            'title' => 'Updated Job Title',
            'description' => str_repeat('a', 40),
            'requirements' => str_repeat('b', 25),
            'client_id' => $client->id,
            'category_id' => $category->id,
            'type' => 'part-time',
            'experience_level' => 'entry',
            'budget_min' => 100,
            'budget_max' => 500,
            'budget_type' => 'fixed',
            'status' => 'published',
            'deadline' => now()->addMonth()->format('Y-m-d'),
            'max_applications' => 10,
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'published',
        ]);
    }

    public function test_update_fails_validation_with_missing_required_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['title' => 'Original Title']);

        $response = $this->actingAs($admin)->put(route('admin.jobs.update', $job), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors([
            'title', 'description', 'requirements', 'client_id', 'category_id',
            'type', 'experience_level', 'budget_type', 'status',
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_admin_can_delete_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.jobs.destroy', $job));

        $response->assertRedirect(route('admin.jobs.index'));
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    public function test_admin_can_toggle_job_status_published_to_paused(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->published()->create();

        $response = $this->actingAs($admin)->patch(route('admin.jobs.toggle-status', $job));

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'status' => 'paused']);
    }

    public function test_admin_can_toggle_job_status_paused_to_published(): void
    {
        $admin = User::factory()->admin()->create();
        $job = Job::factory()->create(['status' => 'paused']);

        $response = $this->actingAs($admin)->patch(route('admin.jobs.toggle-status', $job));

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'status' => 'published']);
    }
}
