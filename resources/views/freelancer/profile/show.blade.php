<x-app-layout>
@php
    $photoUrl = $user->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) : null;
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
    $headline = $user->bio ?: trim(collect([$user->department, $user->year_level ? 'Year '.$user->year_level : null])->filter()->join(' - '));
    $profileLinks = [
        'Portfolio' => $user->portfolio_url,
        'LinkedIn' => $user->linkedin_url,
        'GitHub' => $user->github_url,
        'Facebook' => $user->facebook_url,
    ];
@endphp

<div class="mx-auto max-w-6xl space-y-6">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="h-36 bg-gradient-to-r from-slate-900 via-emerald-900 to-teal-700"></div>
        <div class="px-5 pb-6 sm:px-8">
            <div class="-mt-16 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-100 text-3xl font-bold text-slate-600 shadow-sm">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover object-top">
                        @else
                            {{ $initials ?: 'ME' }}
                        @endif
                    </div>
                    <div class="pb-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-3xl font-bold tracking-tight text-slate-950">{{ $user->name }}</h1>
                            @if($user->is_profile_public)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Public profile</span>
                            @else
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Private profile</span>
                            @endif
                        </div>
                        <p class="mt-2 max-w-2xl text-base text-slate-700">{{ $headline ?: 'Add a headline to show clients what you do best.' }}</p>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ '@'.$user->username }}
                            @if($user->availability)
                                <span aria-hidden="true">&middot;</span> {{ $user->availability }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($user->is_profile_public)
                        <a href="{{ route('members.profile.show', $user) }}" class="btn btn-secondary">View public page</a>
                    @endif
                    <a href="{{ route('member.profile.edit') }}" class="btn btn-primary">Edit profile</a>
                </div>
            </div>
        </div>
    </section>


    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="space-y-6">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="page-eyebrow">About</p>
                        <h2>Professional summary</h2>
                    </div>
                </div>
                <p class="leading-7 text-slate-700">{{ $user->introduction ?: 'Write a short introduction that explains your strengths, projects, and the kind of work you want to take on.' }}</p>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="page-eyebrow">Skills</p>
                        <h2>Core competencies</h2>
                    </div>
                    <span class="text-sm font-semibold text-slate-500">{{ $user->skills->count() }} listed</span>
                </div>
                @if($user->skills->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($user->skills as $skill)
                            <span class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-800">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">Add skills so job recommendations and client searches can match your work.</p>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="page-eyebrow">Details</p>
                        <h2>Profile info</h2>
                    </div>
                </div>
                <dl class="mt-5 space-y-4">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Phone</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $user->show_phone ? ($user->phone ?: 'Not provided') : 'Hidden from public profile' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $user->show_email ? $user->email : 'Hidden from public profile' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Department</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $user->department ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Year level</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $user->year_level ? 'Year '.$user->year_level : 'Not provided' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="page-eyebrow">Proof</p>
                        <h2>Resume & links</h2>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @if($user->resume_path)
                        <a href="{{ route('member.profile.resume.download') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-emerald-700 hover:border-emerald-200 hover:bg-emerald-50">
                            <span>Download resume</span>
                            <span aria-hidden="true">-></span>
                        </a>
                    @else
                        <p class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">No resume uploaded yet.</p>
                    @endif

                    @foreach($profileLinks as $label => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-200 hover:bg-emerald-50">
                                <span>{{ $label }}</span>
                                <span aria-hidden="true">-></span>
                            </a>
                        @endif
                    @endforeach

                    @if(! collect($profileLinks)->filter()->count())
                        <p class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Add professional links to strengthen your profile.</p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
</x-app-layout>
