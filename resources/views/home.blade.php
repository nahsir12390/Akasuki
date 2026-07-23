@extends('layouts.app')

@section('title', 'Akatsuki Devs - Shinobi Developer Community')

@php
    $featuredCourses = \App\Models\Course::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('title')
        ->limit(6)
        ->get();

    $stats = [
        ['label' => 'Course Scrolls', 'value' => \App\Models\Course::where('is_active', true)->count(), 'icon' => 'fas fa-scroll', 'meta' => 'Advanced paths'],
        ['label' => 'Village Posts', 'value' => \App\Models\Post::count(), 'icon' => 'fas fa-users', 'meta' => 'Shared progress'],
        ['label' => 'Shinobi Devs', 'value' => \App\Models\User::count(), 'icon' => 'fas fa-user-ninja', 'meta' => 'Growing squad'],
    ];
@endphp

@section('hero')
<section class="relative overflow-hidden border-b border-orange-200/70 bg-white/86 dark:border-slate-800 dark:bg-slate-950/70">
    <div class="absolute inset-0 shinobi-grid opacity-70" aria-hidden="true"></div>

    <div class="relative mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:px-6 sm:py-12 lg:grid-cols-[1fr_420px] lg:px-8 lg:py-16">
        <div class="flex flex-col justify-center">
            <div class="flex flex-wrap gap-2">
                <span class="rank-badge"><i class="fas fa-cloud-sun"></i> Hidden Code Village</span>
                <span class="rank-badge"><i class="fas fa-bolt"></i> Learn. Build. Share.</span>
            </div>

            <h1 class="mt-5 max-w-4xl text-4xl font-black leading-tight tracking-normal text-slate-950 dark:text-white sm:text-5xl lg:text-6xl">
                Master code scrolls with a squad that keeps you moving.
            </h1>

            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-300 sm:text-lg">
                Akatsuki Devs is a Naruto-inspired developer village for advanced courses, progress posts, allies, chat, and profile growth.
            </p>

            <div class="mt-6 grid gap-3 sm:max-w-xl sm:grid-cols-2">
                <x-ui.button :href="auth()->check() ? route('show.post') : route('show.register')" class="w-full">
                    <i class="fas fa-fire"></i>
                    {{ auth()->check() ? 'Enter Village' : 'Start Training' }}
                </x-ui.button>
                <x-ui.button :href="$featuredCourses->first() ? route('tutorial.show', $featuredCourses->first()) : route('tutorial.html')" variant="secondary" class="w-full">
                    <i class="fas fa-scroll"></i>
                    Browse Scrolls
                </x-ui.button>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-2 sm:max-w-2xl sm:gap-3">
                @foreach($stats as $stat)
                    <div class="rounded-lg border border-slate-200 bg-white/82 p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900/76">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <span class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</span>
                            <i class="{{ $stat['icon'] }} text-orange-500"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-950 dark:text-white">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $stat['meta'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:py-2">
            <div class="scroll-panel overflow-hidden p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-orange-600 dark:text-orange-300">Training Board</p>
                        <h2 class="mt-1 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Today's Mission</h2>
                    </div>
                    <div class="grid h-12 w-12 place-items-center rounded-lg bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-lg shadow-orange-500/25">
                        <i class="fas fa-cloud-sun text-lg"></i>
                    </div>
                </div>

                <div class="mt-4 grid gap-3">
                    @foreach([
                        ['title' => 'Pick an advanced scroll', 'meta' => 'Follow a roadmap with projects', 'icon' => 'fas fa-scroll', 'tone' => 'green'],
                        ['title' => 'Post your build log', 'meta' => 'Turn learning into public momentum', 'icon' => 'fas fa-pen-nib', 'tone' => 'orange'],
                        ['title' => 'Form your squad', 'meta' => 'Add allies and continue in chat', 'icon' => 'fas fa-user-group', 'tone' => 'orange'],
                    ] as $mission)
                        <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white/82 p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900/78">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg {{ $mission['tone'] === 'green' ? 'bg-green-50 text-green-600 dark:bg-green-950/40 dark:text-green-300' : 'bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-300' }}">
                                <i class="{{ $mission['icon'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-black text-slate-950 dark:text-white">{{ $mission['title'] }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $mission['meta'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-lg bg-slate-950 p-4 text-left shadow-xl dark:bg-black">
                    <div class="mb-3 flex items-center gap-2 text-xs font-bold text-slate-400">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-orange-400"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        <span class="ml-2">mission.js</span>
                    </div>
                    <pre class="overflow-x-auto text-xs leading-6 text-slate-200 sm:text-sm"><code>const nextRank = await train({
  scroll: 'advanced',
  habit: 'build daily',
  squad: 'allies'
});</code></pre>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('content')
<section class="bg-white py-12 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="rank-badge"><i class="fas fa-graduation-cap"></i> Dynamic Scrolls</span>
                <h2 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Start with a real learning path.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Courses are managed from the admin panel, so the village can keep growing without changing code.</p>
            </div>
            <x-ui.button :href="$featuredCourses->first() ? route('tutorial.show', $featuredCourses->first()) : route('tutorial.html')" variant="secondary">
                <i class="fas fa-arrow-right"></i>
                View Courses
            </x-ui.button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($featuredCourses as $course)
                <a href="{{ route('tutorial.show', $course) }}" class="ui-card group p-5 transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-xl dark:hover:border-orange-800">
                    <div class="flex items-start justify-between gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                            <i class="{{ $course->icon }} text-xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-orange-500 transition group-hover:translate-x-1"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-black tracking-normal text-slate-950 group-hover:text-orange-700 dark:text-white dark:group-hover:text-orange-300">{{ $course->title }}</h3>
                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $course->subtitle }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-black">
                        <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-slate-600 dark:bg-slate-900 dark:text-slate-300">{{ $course->level }}</span>
                        <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-slate-600 dark:bg-slate-900 dark:text-slate-300">{{ $course->duration }}</span>
                    </div>
                </a>
            @empty
                <x-ui.card class="p-8 text-center md:col-span-2 xl:col-span-3">
                    <h3 class="text-xl font-black text-slate-950 dark:text-white">No active courses yet</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Admins can add courses from the course management panel.</p>
                </x-ui.card>
            @endforelse
        </div>
    </div>
</section>

<section class="border-y border-orange-200/70 bg-orange-50/65 py-12 dark:border-slate-800 dark:bg-slate-900/45">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
        <div>
            <span class="rank-badge"><i class="fas fa-map"></i> Village Workflow</span>
            <h2 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Everything points toward progress.</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">Learn from advanced scrolls, publish your progress, build allies, and use chat/notifications to keep the loop alive.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach([
                ['title' => 'Learn Scrolls', 'copy' => 'Follow advanced roadmaps with modules, projects, and resources.', 'icon' => 'fas fa-scroll'],
                ['title' => 'Post Progress', 'copy' => 'Share wins, blockers, screenshots, and code notes in the village.', 'icon' => 'fas fa-pen'],
                ['title' => 'Build Allies', 'copy' => 'Find developers, send requests, and grow your squad network.', 'icon' => 'fas fa-user-group'],
                ['title' => 'Keep Momentum', 'copy' => 'Use notifications and chat so important updates do not disappear.', 'icon' => 'fas fa-bell'],
            ] as $feature)
                <x-ui.card class="p-5">
                    <div class="mb-4 grid h-11 w-11 place-items-center rounded-lg bg-white text-orange-600 ring-1 ring-orange-100 dark:bg-slate-950 dark:text-orange-300 dark:ring-orange-900">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    <h3 class="font-black text-slate-950 dark:text-white">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $feature['copy'] }}</p>
                </x-ui.card>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-12 dark:bg-slate-950">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <span class="rank-badge"><i class="fas fa-fire"></i> Enter The Village</span>
        <h2 class="mt-4 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Ready for the next mission?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-400">Open your profile, join the feed, and start building a visible developer journey.</p>
        <div class="mt-7 grid gap-3 sm:mx-auto sm:max-w-md sm:grid-cols-2">
            <x-ui.button :href="auth()->check() ? route('user.myprofile') : route('show.register')" class="w-full">
                <i class="fas fa-user-ninja"></i>
                {{ auth()->check() ? 'Open Profile' : 'Create Account' }}
            </x-ui.button>
            <x-ui.button :href="auth()->check() ? route('friends.list') : route('show.login')" variant="secondary" class="w-full">
                <i class="fas fa-user-group"></i>
                {{ auth()->check() ? 'View Allies' : 'Login' }}
            </x-ui.button>
        </div>
    </div>
</section>
@endsection
