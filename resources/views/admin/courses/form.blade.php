@extends('layouts.admin')

@section('title', ($course->exists ? 'Edit Course' : 'Add Course') . ' - ' . config('app.name'))
@section('admin-heading', $course->exists ? 'Edit Course' : 'Add Course')

@php
    $modules = old('modules', implode("\n", $course->modules ?? []));
    $projects = old('projects', implode("\n", $course->projects ?? []));
    $checklist = old('checklist', implode("\n", $course->checklist ?? []));
    $resources = old('resources', collect($course->resources ?? [])->map(fn ($resource) => ($resource['label'] ?? '') . ' | ' . ($resource['url'] ?? ''))->implode("\n"));
    $quizQuestions = old('quiz_questions', collect($course->quiz_questions ?? [])->map(fn ($item) => ($item['question'] ?? '') . ' | ' . implode('; ', $item['options'] ?? []) . ' | ' . ($item['answer'] ?? ''))->implode("\n"));
@endphp

@section('content')
<x-ui.page width="max-w-5xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-graduation-cap"></i> {{ $course->exists ? 'Edit Course' : 'New Course' }}</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">{{ $course->exists ? $course->title : 'Add Course' }}</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Build a course from a YouTube tutorial, roadmap, projects, quiz questions, and resources.</p>
        </div>
        <x-ui.button :href="route('admin.courses')" variant="secondary"><i class="fas fa-arrow-left"></i> Courses</x-ui.button>
    </div>

    @if ($errors->any())
        <x-ui.alert type="error" class="mb-5">{{ $errors->first() }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.courses.store') }}" class="space-y-5">
        <x-ui.card class="p-5 sm:p-6">
            @csrf
            @if($course->exists)
                @method('PUT')
            @endif

            <div class="mb-5">
                <span class="rank-badge"><i class="fas fa-circle-info"></i> Course Identity</span>
                <h2 class="mt-3 text-xl font-black text-slate-950 dark:text-white">What users will see first</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep the title clear and the subtitle outcome-focused.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input label="Title" name="title" icon="fas fa-heading" :value="old('title', $course->title)" required />
                <x-ui.input label="Slug" name="slug" icon="fas fa-link" :value="old('slug', $course->slug)" placeholder="advanced-typescript" />
                <x-ui.input label="Icon Class" name="icon" icon="fas fa-icons" :value="old('icon', $course->icon ?: 'fas fa-code')" required />
                <x-ui.input label="Level" name="level" icon="fas fa-medal" :value="old('level', $course->level ?: 'Intermediate')" required />
                <x-ui.input label="Duration" name="duration" icon="fas fa-clock" :value="old('duration', $course->duration ?: '4 weeks')" required />
                <x-ui.input label="Sort Order" name="sort_order" type="number" icon="fas fa-arrow-down-1-9" :value="old('sort_order', $course->sort_order ?? 0)" />
            </div>

            <div>
                <label for="subtitle" class="mb-2 block text-sm font-black text-slate-700 dark:text-slate-200">Subtitle</label>
                <textarea id="subtitle" name="subtitle" rows="3" class="ui-input resize-y" required>{{ old('subtitle', $course->subtitle) }}</textarea>
            </div>
        </x-ui.card>

        <x-ui.card class="p-5 sm:p-6">
            <div class="mb-5">
                <span class="rank-badge"><i class="fab fa-youtube"></i> YouTube Training</span>
                <h2 class="mt-3 text-xl font-black text-slate-950 dark:text-white">Connect the tutorial source</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Paste one main YouTube video, then add a search phrase for extra curated lessons.</p>
            </div>

            <div class="grid gap-4">
                <x-ui.input label="Main YouTube Video URL" name="youtube_url" icon="fab fa-youtube" :value="old('youtube_url', $course->youtube_url)" placeholder="https://www.youtube.com/watch?v=..." />
                <x-ui.input label="YouTube Search Query" name="query" icon="fas fa-search" :value="old('query', $course->query)" placeholder="advanced typescript full course" />
            </div>
        </x-ui.card>

        <x-ui.card class="p-5 sm:p-6">
            <div class="mb-5">
                <span class="rank-badge"><i class="fas fa-route"></i> Learning Structure</span>
                <h2 class="mt-3 text-xl font-black text-slate-950 dark:text-white">Roadmap, projects, and checklist</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use one item per line. These become the course map users follow.</p>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label for="modules" class="mb-2 block text-sm font-black text-slate-700 dark:text-slate-200">Roadmap Modules</label>
                    <textarea id="modules" name="modules" rows="7" class="ui-input resize-y">{{ $modules }}</textarea>
                </div>
                <div>
                    <label for="projects" class="mb-2 block text-sm font-black text-slate-700 dark:text-slate-200">Portfolio Projects</label>
                    <textarea id="projects" name="projects" rows="7" class="ui-input resize-y">{{ $projects }}</textarea>
                </div>
                <div>
                    <label for="checklist" class="mb-2 block text-sm font-black text-slate-700 dark:text-slate-200">Mastery Checklist</label>
                    <textarea id="checklist" name="checklist" rows="7" class="ui-input resize-y">{{ $checklist }}</textarea>
                </div>
                <div>
                    <label for="resources" class="mb-2 block text-sm font-black text-slate-700 dark:text-slate-200">Resources</label>
                    <textarea id="resources" name="resources" rows="7" class="ui-input resize-y" placeholder="Laravel Docs | https://laravel.com/docs">{{ $resources }}</textarea>
                    <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400">Format: Label | https://example.com</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-5 sm:p-6">
            <div class="mb-5">
                <span class="rank-badge"><i class="fas fa-clipboard-question"></i> Quiz Builder</span>
                <h2 class="mt-3 text-xl font-black text-slate-950 dark:text-white">Questions users submit to admin</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">One question per line. Format: Question | option A; option B; option C | correct answer</p>
            </div>

            <textarea id="quiz_questions" name="quiz_questions" rows="8" class="ui-input resize-y" placeholder="What does HTML stand for? | HyperText Markup Language; HighText Machine Language; Home Tool Markup Language | HyperText Markup Language">{{ $quizQuestions }}</textarea>

            <div class="mt-4 grid gap-3 rounded-lg border border-orange-100 bg-orange-50/60 p-4 text-sm dark:border-orange-900 dark:bg-orange-950/20 md:grid-cols-3">
                <div><strong class="text-slate-950 dark:text-white">Question</strong><p class="mt-1 text-slate-600 dark:text-slate-400">What you want the learner to answer.</p></div>
                <div><strong class="text-slate-950 dark:text-white">Options</strong><p class="mt-1 text-slate-600 dark:text-slate-400">Separate choices with semicolons.</p></div>
                <div><strong class="text-slate-950 dark:text-white">Answer</strong><p class="mt-1 text-slate-600 dark:text-slate-400">Must exactly match one option.</p></div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-5 sm:p-6">
            <label class="inline-flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 dark:border-slate-800 dark:text-slate-300">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500" @checked(old('is_active', $course->exists ? $course->is_active : true))>
                Show this course in the app
            </label>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                <x-ui.button :href="route('admin.courses')" variant="secondary">Cancel</x-ui.button>
                <x-ui.button type="submit"><i class="fas fa-save"></i> Save Course</x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-ui.page>
@endsection
