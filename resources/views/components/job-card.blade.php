@props(['job'])
<article class="job-listing-card">
    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">{{ $job->category->name ?? 'Opportunity' }}</p>
    <h3 class="mt-3 text-lg">{{ $job->title }}</h3>
    <p class="mt-2 text-sm text-slate-500">{{ $job->client->name ?? 'CMU Client' }} · {{ ucfirst(str_replace('-', ' ', $job->type)) }}</p>
</article>
