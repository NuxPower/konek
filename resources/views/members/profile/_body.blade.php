@php
    $verification = $user->identityVerification;
    $proofStatus = $verification?->status ?? 'unsubmitted';
    $proofLabels = [
        'unsubmitted' => 'Not submitted',
        'pending' => 'Pending review',
        'verified' => 'Verified',
        'rejected' => 'Rejected',
    ];
@endphp

<div class="mb-5 rounded-lg border border-emerald-100 bg-emerald-50 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-emerald-900">ID Proof</p>
            <p class="text-sm text-emerald-800">{{ $proofLabels[$proofStatus] ?? ucfirst($proofStatus) }}</p>
        </div>
        <p class="text-2xl font-bold text-emerald-900">{{ $user->id_proof_points }}/100</p>
    </div>
    @if($isOwner ?? false)
        <a href="{{ route('member.identity.edit') }}" class="mt-3 inline-flex text-sm font-semibold text-emerald-800">Manage ID Proof</a>
    @endif
</div>

<div class="page-header">
    <div>
        <p class="page-eyebrow">Member profile</p>
        <h1>{{ $user->name }}</h1>
        <p class="page-subtitle">{{ $user->introduction ?: 'This member has not added an introduction yet.' }}</p>
    </div>
    @if($isOwner)
        <a href="{{ route('member.profile.edit') }}" class="btn btn-primary">Edit profile</a>
    @endif
</div>

@if($isOwner && ! $user->is_profile_public)
    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800">
        Your profile is private. Other people cannot view your public profile page yet.
    </div>
@endif

<div class="grid gap-5 xl:grid-cols-[1.5fr_.9fr]">
    <section class="panel">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover object-top">
            @else
                <div class="grid h-24 w-24 shrink-0 place-items-center rounded-2xl bg-emerald-100 text-3xl font-bold text-emerald-800">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <p class="text-xl font-semibold text-slate-900">{{ $user->name }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ '@'.$user->username }}</p>
                @if($user->availability)
                    <p class="mt-3 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $user->availability }}</p>
                @endif
            </div>
        </div>

        <div class="my-7 border-t border-slate-100"></div>

        <h2>About</h2>
        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $user->bio ?: 'No bio added yet.' }}</p>

        <div class="mt-7">
            <h2>Skills</h2>
            @if($user->skills->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($user->skills as $skill)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $skill->name }}</span>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-slate-500">No skills added yet.</p>
            @endif
        </div>
    </section>

    <aside class="panel">
        <div class="space-y-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Department</p>
                <p class="mt-1 text-sm text-slate-700">{{ $user->department ?? 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Year level</p>
                <p class="mt-1 text-sm text-slate-700">{{ $user->year_level ?? 'Not provided' }}</p>
            </div>

            @if($user->show_email || $isOwner)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</p>
                    <p class="mt-1 break-all text-sm text-slate-700">{{ $user->email }}</p>
                </div>
            @endif

            @if(($user->show_phone || $isOwner) && $user->phone)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Phone</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $user->phone }}</p>
                </div>
            @endif

            @if(($user->show_links || $isOwner) && $profileLinks->isNotEmpty())
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Links</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($profileLinks as $label => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-emerald-700 hover:border-emerald-200 hover:bg-emerald-50">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Resume/CV</p>
                @if($canDownloadResume)
                    <a href="{{ $isOwner ? route('member.profile.resume.download') : route('members.profile.resume.download', $user->username) }}" class="mt-2 inline-flex text-sm font-semibold text-emerald-700">Download resume</a>
                @elseif($user->resume_path)
                    <p class="mt-1 text-sm text-slate-500">Sign in to download this member's resume.</p>
                @else
                    <p class="mt-1 text-sm text-slate-500">Not uploaded</p>
                @endif
            </div>
        </div>
    </aside>
</div>
