<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_dashboard_uses_real_platform_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        $job = $this->job($client);
        Application::factory()->accepted()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertViewHas('totalUsers', 3)
            ->assertViewHas('totalJobs', 1)
            ->assertViewHas('totalApplications', 1)
            ->assertViewHas('acceptanceRate', 100.0);
    }

    public function test_job_report_filters_by_status_and_date(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $published = $this->job($client, [
            'title' => 'Published report job',
            'created_at' => now()->subDays(2),
        ]);
        $draft = $this->job($client, [
            'title' => 'Draft report job',
            'status' => 'draft',
            'created_at' => now()->subDays(2),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.jobs', [
                'status' => 'published',
                'from' => now()->subDays(3)->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_csv_export_contains_only_filtered_rows(): void
    {
        $admin = User::factory()->admin()->create();
        $activeClient = User::factory()->client()->create([
            'name' => 'Active CSV Client',
            'is_active' => true,
        ]);
        User::factory()->member()->create([
            'name' => 'Excluded CSV Member',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'report' => 'users',
            'role' => 'member',
            'active' => '1',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString($activeClient->name, $response->streamedContent());
        $this->assertStringNotContainsString('Excluded CSV Member', $response->streamedContent());
    }

    public function test_csv_export_neutralizes_formula_cells(): void
    {
        $admin = User::factory()->admin()->create();
        $dangerousNames = ['=2+2', '+2+2', '-2+2', '@SUM(A1:A2)'];

        foreach ($dangerousNames as $name) {
            User::factory()->member()->create([
                'name' => $name,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'report' => 'users',
            'role' => 'member',
            'active' => '1',
        ]);

        $response->assertOk();
        $rows = array_map('str_getcsv', preg_split('/\r\n|\r|\n/', trim($response->streamedContent())));
        $exportedNames = array_column(array_slice($rows, 1), 0);

        foreach ($dangerousNames as $name) {
            $this->assertContains("'{$name}", $exportedNames);
        }
    }

    public function test_non_admin_cannot_access_or_export_reports(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)
            ->get(route('admin.reports.index'))
            ->assertForbidden();

        $this->actingAs($freelancer)
            ->post(route('admin.reports.export'), ['report' => 'users'])
            ->assertForbidden();
    }

    private function job(User $client, array $attributes = []): Job
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
}
