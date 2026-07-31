<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class N8nJobWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creation_sends_the_konek_job_id_to_n8n(): void
    {
        Http::fake([
            'n8n.test/*' => Http::response(status: 202),
        ]);
        Config::set('services.n8n.new_job_webhook', 'https://n8n.test/webhook/konek-job-audit');
        Config::set('services.n8n.webhook_token', 'test-webhook-token');

        $client = User::factory()->client()->create();
        $category = Category::factory()->create();

        $job = app(JobService::class)->createJob($this->validJobData($client, $category));

        Http::assertSent(function (Request $request) use ($job, $client): bool {
            return $request->url() === 'https://n8n.test/webhook/konek-job-audit'
                && $request->hasHeader('X-Konek-Webhook-Token', 'test-webhook-token')
                && $request['event'] === 'job.created'
                && $request['job_id'] === $job->id
                && $request['posted_by'] === $client->name
                && $request['status'] === 'published';
        });
    }

    public function test_an_unconfigured_webhook_never_sends_a_request(): void
    {
        Http::fake();
        Config::set('services.n8n.new_job_webhook');

        $client = User::factory()->client()->create();
        $category = Category::factory()->create();

        app(JobService::class)->createJob($this->validJobData($client, $category));

        Http::assertNothingSent();
    }

    public function test_a_webhook_failure_does_not_roll_back_the_job(): void
    {
        Http::fake(fn () => throw new \RuntimeException('n8n unavailable'));
        Config::set('services.n8n.new_job_webhook', 'https://n8n.test/webhook/konek-job-audit');

        $client = User::factory()->client()->create();
        $category = Category::factory()->create();

        $job = app(JobService::class)->createJob($this->validJobData($client, $category));

        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
    }

    private function validJobData(User $client, Category $category): array
    {
        return [
            'title' => 'Build a modern CMU project website',
            'description' => 'We need a developer to design and build a responsive project website with a clear content structure.',
            'requirements' => 'Experience with Laravel, responsive interfaces, Git, and clear written communication is required.',
            'category_id' => $category->id,
            'client_id' => $client->id,
            'type' => 'contract',
            'experience_level' => 'intermediate',
            'budget_min' => 10000,
            'budget_max' => 20000,
            'budget_type' => 'fixed',
            'status' => 'published',
            'published_at' => now(),
            'deadline' => now()->addMonth(),
            'max_applications' => 20,
        ];
    }
}
