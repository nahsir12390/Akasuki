@extends('layouts.app')

@section('title', $course->title . ' - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-7xl">
    <section class="ui-card overflow-hidden">
        <div class="relative bg-gradient-to-br from-slate-950 via-red-950 to-orange-700 p-5 text-white sm:p-8">
            <div class="absolute inset-0 shinobi-grid opacity-25"></div>
            <div class="relative grid gap-6 lg:grid-cols-[1fr_320px] lg:items-end">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-lg bg-white/12 px-3 py-2 text-xs font-black uppercase ring-1 ring-white/15">
                        <i class="{{ $course->icon }}"></i>
                        Advanced Jutsu Scroll
                    </span>
                    <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-normal sm:text-5xl">{{ $course->title }}</h1>
                    <p class="mt-4 max-w-3xl text-base font-medium leading-7 text-orange-50/90">{{ $course->subtitle }}</p>
                    <div class="mt-6 flex flex-wrap gap-2 text-xs font-black">
                        <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-signal mr-1"></i>{{ $course->level }}</span>
                        <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-calendar-week mr-1"></i>{{ $course->duration }}</span>
                        <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-video mr-1"></i>{{ count($videos) }} videos</span>
                    </div>
                </div>

                <div class="rounded-lg border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <h2 class="text-sm font-black uppercase text-orange-100">Mission Outcome</h2>
                    <p class="mt-2 text-sm leading-6 text-white/85">Finish this path with portfolio-ready projects, stronger fundamentals, and a clear checklist for production work.</p>
                    <x-ui.button :href="route('tutorial.show', $nextCourse)" class="mt-4 w-full bg-white text-orange-700 hover:bg-orange-50">
                        Next: {{ $nextCourse->title }}
                        <i class="fas fa-arrow-right"></i>
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Level" :value="$course->level" icon="fas fa-medal" meta="Skill target" />
        <x-ui.stat-card label="Duration" :value="$course->duration" icon="fas fa-clock" meta="Suggested pace" />
        <x-ui.stat-card label="Modules" :value="count($course->modules ?? [])" icon="fas fa-list-check" meta="Core lessons" />
        <x-ui.stat-card label="Projects" :value="count($course->projects ?? [])" icon="fas fa-diagram-project" meta="Portfolio tasks" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[320px_1fr]">
        <aside class="space-y-4 lg:sticky lg:top-36 lg:self-start">
            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Mastery Checklist</h2>
                <div class="mt-4 space-y-2">
                    @foreach($course->checklist ?? [] as $item)
                        <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 dark:border-slate-800 dark:text-slate-300">
                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            <span>{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Official Resources</h2>
                <div class="mt-4 space-y-2">
                    @foreach($course->resources ?? [] as $resource)
                        <a href="{{ $resource['url'] }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-orange-700 hover:bg-orange-50 dark:border-slate-800 dark:text-orange-300 dark:hover:bg-orange-950/30">
                            <span>{{ $resource['label'] }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Course Map</h2>
                <div class="mt-4 grid gap-2">
                    @foreach($allCourses as $item)
                        <a href="{{ route('tutorial.show', $item) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold transition {{ $item->is($course) ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/30 dark:hover:text-orange-300' }}">
                            <i class="{{ $item->icon }} text-orange-500"></i>
                            {{ str_replace('Advanced ', '', $item->title) }}
                        </a>
                    @endforeach
                </div>
            </x-ui.card>
        </aside>

        <main class="min-w-0 space-y-6">
            @if (session('success'))
                <x-ui.alert>{{ session('success') }}</x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error">{{ $errors->first() }}</x-ui.alert>
            @endif

            <x-ui.card class="p-5 sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <span class="rank-badge"><i class="fas fa-route"></i> Roadmap</span>
                        <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Advanced Learning Path</h2>
                    </div>
                </div>
                <div class="grid gap-3">
                    @foreach($course->modules ?? [] as $index => $module)
                        <div class="flex gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-orange-50 text-sm font-black text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">{{ $index + 1 }}</div>
                            <div>
                                <h3 class="font-black text-slate-950 dark:text-white">{{ $module }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">Study, build a small proof, then refactor it until the implementation is clear and reusable.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="rank-badge"><i class="fab fa-youtube"></i> Main Tutorial</span>
                        <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Watch The Core Lesson</h2>
                    </div>
                    @if($course->youtube_url)
                        <a href="{{ $course->youtube_url }}" target="_blank" rel="noopener" class="text-sm font-black text-orange-600 hover:text-orange-700 dark:text-orange-300">
                            Open on YouTube <i class="fas fa-arrow-up-right-from-square ml-1"></i>
                        </a>
                    @endif
                </div>

                @if($course->youtube_embed_url)
                    <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-950 shadow-lg dark:border-slate-800">
                        <iframe src="{{ $course->youtube_embed_url }}" title="{{ $course->title }} tutorial" class="aspect-video w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-orange-200 bg-orange-50/60 p-8 text-center dark:border-orange-900 dark:bg-orange-950/20">
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-white text-orange-600 shadow-sm dark:bg-slate-900 dark:text-orange-300">
                            <i class="fab fa-youtube text-xl"></i>
                        </div>
                        <h3 class="mt-4 font-black text-slate-950 dark:text-white">No main YouTube video yet</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">An admin can paste a YouTube URL into this course to make the main tutorial appear here.</p>
                    </div>
                @endif
            </x-ui.card>

            @if(count($course->quiz_questions ?? []) > 0)
                <x-ui.card class="p-5 sm:p-6">
                    <div class="mb-5">
                        <span class="rank-badge"><i class="fas fa-clipboard-question"></i> Quiz Challenge</span>
                        <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Submit Your Answers For Review</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Choose your answers after studying. Your result is sent to the admin panel for review.</p>
                    </div>

                    <form method="POST" action="{{ route('tutorial.quiz.submit', $course) }}" class="space-y-4">
                        @csrf
                        @foreach($course->quiz_questions ?? [] as $index => $question)
                            <fieldset class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                                <legend class="px-2 text-sm font-black text-slate-950 dark:text-white">{{ $index + 1 }}. {{ $question['question'] }}</legend>
                                <div class="mt-3 grid gap-2">
                                    @foreach($question['options'] ?? [] as $option)
                                        <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-orange-50 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-orange-950/25">
                                            <input type="radio" name="answers[{{ $index }}]" value="{{ $option }}" class="h-4 w-4 border-slate-300 text-orange-600 focus:ring-orange-500" required>
                                            <span>{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach

                        <x-ui.button type="submit" class="w-full sm:w-auto">
                            <i class="fas fa-paper-plane"></i>
                            Submit Quiz
                        </x-ui.button>
                    </form>
                </x-ui.card>
            @endif

            <x-ui.card class="p-5 sm:p-6">
                <span class="rank-badge"><i class="fas fa-hammer"></i> Portfolio Work</span>
                <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Build These Projects</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    @foreach($course->projects ?? [] as $project)
                        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                            <div class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                                <i class="fas fa-diagram-project"></i>
                            </div>
                            <h3 class="mt-4 font-black text-slate-950 dark:text-white">{{ $project }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Ship a polished version, write notes on your decisions, and add screenshots to your portfolio.</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="rank-badge"><i class="fas fa-play"></i> Video Training</span>
                        <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Curated Lessons</h2>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ count($videos) }} videos available</p>
                </div>

                @if(count($videos) > 0)
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach($videos as $index => $video)
                            @php
                                $snippet = $video['snippet'];
                                $videoId = $video['id']['videoId'] ?? null;
                                $thumbnail = $snippet['thumbnails']['high']['url'] ?? $snippet['thumbnails']['medium']['url'] ?? null;
                            @endphp
                            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
                                @if($thumbnail && $videoId)
                                    <a href="https://www.youtube.com/watch?v={{ $videoId }}" target="_blank" rel="noopener" class="group relative block aspect-video bg-slate-900">
                                        <img src="{{ $thumbnail }}" alt="{{ $snippet['title'] }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                        <span class="absolute inset-0 grid place-items-center bg-black/20 opacity-0 transition group-hover:opacity-100">
                                            <span class="grid h-12 w-12 place-items-center rounded-full bg-red-600 text-white"><i class="fas fa-play"></i></span>
                                        </span>
                                    </a>
                                @endif
                                <div class="p-4">
                                    <h3 class="line-clamp-2 text-sm font-black leading-6 text-slate-950 dark:text-white">{{ $snippet['title'] }}</h3>
                                    <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($snippet['description'] ?? '', 90) }}</p>
                                    <div class="mt-3 flex items-center justify-between gap-2 text-[11px] font-bold text-slate-400">
                                        <span class="truncate">{{ $snippet['channelTitle'] ?? 'YouTube' }}</span>
                                        <span>{{ isset($snippet['publishedAt']) ? date('M j, Y', strtotime($snippet['publishedAt'])) : '' }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-orange-200 bg-orange-50/60 p-8 text-center dark:border-orange-900 dark:bg-orange-950/20">
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-white text-orange-600 shadow-sm dark:bg-slate-900 dark:text-orange-300">
                            <i class="fas fa-video-slash text-xl"></i>
                        </div>
                        <h3 class="mt-4 font-black text-slate-950 dark:text-white">Video feed unavailable</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">Add a YouTube API key to show live curated videos. The roadmap, projects, and resources still work without it.</p>
                    </div>
                @endif
            </x-ui.card>
        </main>
    </div>
</x-ui.page>
@endsection
