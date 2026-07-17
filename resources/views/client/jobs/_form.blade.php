@php
    $selectedSkills = collect(old('skills', isset($job) ? $job->skills->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<div class="job-form-full">
    <label for="title">Job title</label>
    <input id="title" type="text" name="title" value="{{ old('title', $job->title ?? '') }}" placeholder="e.g. Laravel developer for inventory platform" required>
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="job-form-full">
    <label for="description">Job description</label>
    <textarea id="description" name="description" rows="7" placeholder="Explain the project, responsibilities, expected output, and working arrangement." required>{{ old('description', $job->description ?? '') }}</textarea>
    <p class="mt-2 text-xs text-slate-400">Give candidates enough context to understand the work before applying.</p>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="job-form-full">
    <label for="requirements">Requirements</label>
    <textarea id="requirements" name="requirements" rows="6" placeholder="List required experience, tools, availability, or deliverables." required>{{ old('requirements', $job->requirements ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
</div>

<div class="job-form-grid">
    <div>
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Choose a category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $job->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>
    <div>
        <label for="type">Work type</label>
        <select id="type" name="type" required>
            @foreach(['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $job->type ?? 'contract') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="job-form-grid">
    <div>
        <label for="experience_level">Experience level</label>
        <select id="experience_level" name="experience_level" required>
            @foreach(['entry' => 'Entry', 'intermediate' => 'Intermediate', 'expert' => 'Expert'] as $value => $label)
                <option value="{{ $value }}" @selected(old('experience_level', $job->experience_level ?? 'entry') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="budget_type">Budget type</label>
        <select id="budget_type" name="budget_type" required>
            @foreach(['fixed' => 'Fixed', 'hourly' => 'Hourly', 'negotiable' => 'Negotiable'] as $value => $label)
                <option value="{{ $value }}" @selected(old('budget_type', $job->budget_type ?? 'fixed') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="job-form-grid">
    <div>
        <label for="budget_min">Minimum budget</label>
        <input id="budget_min" type="number" min="0" step="0.01" name="budget_min" value="{{ old('budget_min', $job->budget_min ?? '') }}" placeholder="0.00">
    </div>
    <div>
        <label for="budget_max">Maximum budget</label>
        <input id="budget_max" type="number" min="0" step="0.01" name="budget_max" value="{{ old('budget_max', $job->budget_max ?? '') }}" placeholder="0.00">
        <x-input-error :messages="$errors->get('budget_max')" class="mt-2" />
    </div>
</div>

<div class="job-form-grid">
    <div>
        <label for="deadline">Application deadline</label>
        <input id="deadline" type="date" name="deadline" value="{{ old('deadline', isset($job) && $job->deadline ? $job->deadline->format('Y-m-d') : '') }}">
        <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
    </div>
    <div>
        <label for="max_applications">Application limit</label>
        <input id="max_applications" type="number" min="1" name="max_applications" value="{{ old('max_applications', $job->max_applications ?? '') }}" placeholder="Optional">
    </div>
</div>

<div class="job-form-full" x-data="{ skillSearch: '' }">
    <div class="mb-3 flex items-end justify-between">
        <div>
            <label class="mb-0">Required skills</label>
            <p class="mt-1 text-xs text-slate-400">Choose up to 12 skills.</p>
        </div>
        <span class="text-xs text-slate-400">{{ count($selectedSkills) }} selected</span>
    </div>
    <div class="mb-3">
        <label for="skill_search" class="sr-only">Search required skills</label>
        <input id="skill_search" type="search" x-model.debounce.150ms="skillSearch" placeholder="Search skills..." autocomplete="off">
    </div>
    <div class="skill-picker">
        @foreach($skills as $skill)
            <label class="skill-option" data-skill-name="{{ strtolower($skill->name) }}" x-show="$el.dataset.skillName.includes(skillSearch.toLowerCase())">
                <input type="checkbox" name="skills[]" value="{{ $skill->id }}" @checked(in_array($skill->id, $selectedSkills, true))>
                <span>{{ $skill->name }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('skills')" class="mt-2" />
</div>
