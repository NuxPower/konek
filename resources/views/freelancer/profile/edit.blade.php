<x-app-layout>
@php
    $selectedSkills = collect(old('skills', $user->skills->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
    $skillsByCategory = $skills->groupBy(fn ($skill) => $skill->category?->name ?: 'General');
    $photoUrl = $user->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) : null;
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
@endphp

<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="page-eyebrow">Profile editor</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Edit professional profile</h1>
            <p class="mt-2 max-w-2xl text-slate-600">Shape how clients see your experience, work samples, skills, and contact preferences.</p>
        </div>
        <a href="{{ route('member.profile.show') }}" class="btn btn-secondary">Back to profile</a>
    </div>

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="h-32 bg-gradient-to-r from-slate-900 via-emerald-900 to-teal-700"></div>
            <div class="px-5 pb-6 sm:px-8">
                <div class="-mt-14 grid gap-6 lg:grid-cols-[180px_minmax(0,1fr)]">
                    <div>
                        <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-slate-100 text-2xl font-bold text-slate-600 shadow-sm">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover object-top">
                            @else
                                {{ $initials ?: 'ME' }}
                            @endif
                        </div>
                    </div>
                    <div class="grid gap-5 pt-14 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="bio">Headline</label>
                            <input id="bio" name="bio" value="{{ old('bio', $user->bio) }}" maxlength="160" placeholder="Example: UI designer and Laravel developer for student-led teams">
                            <p class="mt-2 text-xs text-slate-500">A short LinkedIn-style line that appears under your name.</p>
                            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                        </div>
                        <div>
                            <label for="username">Public username</label>
                            <input id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="your-name">
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>
                        <div>
                            <label for="availability">Availability</label>
                            <input id="availability" name="availability" value="{{ old('availability', $user->availability) }}" placeholder="Weekdays after 4 PM">
                            <x-input-error :messages="$errors->get('availability')" class="mt-2" />
                        </div>
                        <div>
                            <label for="profile_photo">Profile photo</label>
                            <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                        </div>
                        <div>
                            <label for="resume">Resume PDF</label>
                            <input id="resume" type="file" name="resume" accept="application/pdf" class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                            <x-input-error :messages="$errors->get('resume')" class="mt-2" />
                        </div>
                        @if($photoUrl)
                            <label class="sm:col-span-2 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                                <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Remove current profile photo
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="page-eyebrow">About</p>
                            <h2>Professional summary</h2>
                        </div>
                    </div>
                    <textarea id="introduction" name="introduction" rows="7" placeholder="Describe your experience, strengths, notable class projects, tools, and the work you want clients to hire you for.">{{ old('introduction', $user->introduction) }}</textarea>
                    <x-input-error :messages="$errors->get('introduction')" class="mt-2" />
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="page-eyebrow">Skills</p>
                            <h2>Core competencies</h2>
                        </div>
                    </div>
                    <div class="mt-5 space-y-3">
                        @foreach($skillsByCategory as $category => $categorySkills)
                            @php
                                $categoryHasSelectedSkill = $categorySkills->contains(fn ($skill) => in_array($skill->id, $selectedSkills, true));
                            @endphp
                            <details class="group rounded-2xl border border-slate-200 bg-white shadow-sm" @open($categoryHasSelectedSkill)>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-900 marker:hidden hover:bg-slate-50">
                                    <span>{{ $category }}</span>
                                    <span class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                                        {{ $categorySkills->count() }} skills
                                        <span class="text-lg leading-none group-open:hidden">+</span>
                                        <span class="hidden text-lg leading-none group-open:inline">-</span>
                                    </span>
                                </summary>
                                <div class="grid gap-2 border-t border-slate-100 p-4 sm:grid-cols-2">
                                    @foreach($categorySkills as $skill)
                                        <label class="flex min-h-11 items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-200 hover:bg-emerald-50">
                                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(in_array($skill->id, $selectedSkills, true))>
                                            <span>{{ $skill->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                </section>
            </div>

            <aside class="space-y-6">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="page-eyebrow">Education</p>
                            <h2>Student details</h2>
                        </div>
                    </div>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="department">Department</label>
                            <input id="department" name="department" value="{{ old('department', $user->department) }}" placeholder="Department or college">
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>
                        <div>
                            <label for="year_level">Year level</label>
                            <input id="year_level" type="number" name="year_level" min="1" max="6" value="{{ old('year_level', $user->year_level) }}" placeholder="1">
                            <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
                        </div>
                        <div>
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+63 900 000 0000">
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="page-eyebrow">Links</p>
                            <h2>Work samples</h2>
                        </div>
                    </div>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="portfolio_url">Portfolio</label>
                            <input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $user->portfolio_url) }}" placeholder="https://">
                            <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                        </div>
                        <div>
                            <label for="linkedin_url">LinkedIn</label>
                            <input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/...">
                            <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                        </div>
                        <div>
                            <label for="github_url">GitHub</label>
                            <input id="github_url" name="github_url" value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/...">
                            <x-input-error :messages="$errors->get('github_url')" class="mt-2" />
                        </div>
                        <div>
                            <label for="facebook_url">Facebook</label>
                            <input id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $user->facebook_url) }}" placeholder="https://facebook.com/...">
                            <x-input-error :messages="$errors->get('facebook_url')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="page-eyebrow">Visibility</p>
                            <h2>Public profile</h2>
                        </div>
                    </div>
                    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        @foreach([
                            'is_profile_public' => ['Public profile', 'Allow clients and members to view this profile.'],
                            'show_email' => ['Show email', 'Display your email on your public profile.'],
                            'show_phone' => ['Show phone', 'Display your phone number on your public profile.'],
                            'show_links' => ['Show links', 'Display portfolio and social links publicly.'],
                        ] as $field => [$title, $description])
                            <label class="group flex cursor-pointer items-center justify-between gap-4 border-b border-slate-100 px-4 py-3 last:border-b-0 hover:bg-slate-50">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-900">{{ $title }}</span>
                                    <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ $description }}</span>
                                </span>
                                <span class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full border border-slate-200 bg-slate-200 transition group-has-[:checked]:border-emerald-600 group-has-[:checked]:bg-emerald-600">
                                    <input type="checkbox" name="{{ $field }}" value="1" class="peer sr-only" @checked(old($field, $user->{$field}))>
                                    <span class="ml-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>

        <div class="sticky bottom-4 z-20 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Save changes to update your private and public profile pages.</p>
            <div class="flex gap-3">
                <a href="{{ route('member.profile.show') }}" class="btn btn-secondary">Cancel</a>
                <x-primary-button>Save profile</x-primary-button>
            </div>
        </div>
    </form>
</div>
</x-app-layout>
