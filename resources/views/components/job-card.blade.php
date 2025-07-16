<!-- Job card component -->
@props(['job'])
<div class="job-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; background: #fff;">
    <h3 style="margin: 0 0 0.5rem 0;">{{ $job->title }}</h3>
    <div><strong>Type:</strong> {{ $job->type }}</div>
    <div><strong>Company:</strong> {{ $job->client->name ?? 'N/A' }}</div>
    <a href="{{ route('jobs.public.show', $job->id) }}" style="color: #2563eb; text-decoration: underline;">View Details</a>
</div> 