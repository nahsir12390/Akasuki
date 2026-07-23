@extends('layouts.app')

@section('title', 'About Akatsuki Devs - Developer Village')

@section('content')
@php
    $activeCourses = \App\Models\Course::where('is_active', true)->count();
    $memberCount = \App\Models\User::count();
    $postCount = \App\Models\Post::count();
    $founderImage = asset('profile/founder.jpg');
    $fallbackImage = asset('profile/profile.png');
    $portfolioUrl = 'https://nasiru-portfolio.onrender.com/';
@endphp

<x-ui.page width="max-w-7xl">
    <section class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-center">
        <div>
            <span class="rank-badge"><i class="fas fa-cloud-sun"></i> About The Village</span>
            <h1 class="mt-5 max-w-4xl text-4xl font-black leading-tight tracking-normal text-slate-950 dark:text-white sm:text-5xl">
                A premium learning community for developers who want momentum, not noise.
            </h1>
            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 dark:text-slate-300">
                Built by Nasiru Zakari, a software developer in Keffi, Nigeria, this platform blends Laravel, responsive UI, dashboards, course paths, profiles, allies, chat, and notifications into one focused developer village.
            </p>

            <div class="mt-7 grid gap-3 sm:grid-cols-3">
                <x-ui.stat-card label="Scrolls" :value="$activeCourses" icon="fas fa-scroll" meta="Active courses" />
                <x-ui.stat-card label="Developers" :value="$memberCount" icon="fas fa-user-group" meta="Village members" />
                <x-ui.stat-card label="Posts" :value="$postCount" icon="fas fa-pen" meta="Shared progress" />
            </div>
        </div>

        <div class="ui-card overflow-hidden">
            <img src="{{ $founderImage }}" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" alt="Nasiru Zakari" class="aspect-[4/5] w-full object-cover">
            <div class="border-t border-orange-100 p-5 dark:border-slate-800">
                <p class="text-xs font-black uppercase tracking-wide text-orange-600 dark:text-orange-300">Founder & Lead Developer</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950 dark:text-white">Nasiru Zakari</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Software developer building responsive websites, Laravel applications, dashboards, and mobile-first product experiences.</p>
                <a href="{{ $portfolioUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 text-sm font-black text-orange-600 transition hover:text-orange-700 dark:text-orange-300 dark:hover:text-orange-200">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    View Portfolio
                </a>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-5 md:grid-cols-3">
        @foreach([
            ['title' => 'Laravel Foundation', 'copy' => 'The app is structured around real product features: authentication, admin tools, courses, posts, chat, notifications, and settings.', 'icon' => 'fab fa-laravel'],
            ['title' => 'Responsive Product UI', 'copy' => 'The interface is designed to work from phone screens to desktop dashboards with reusable cards, buttons, inputs, and page shells.', 'icon' => 'fas fa-mobile-screen'],
            ['title' => 'Community Learning', 'copy' => 'Course scrolls, public progress, allies, and conversations help learners stay consistent while building proof of skill.', 'icon' => 'fas fa-user-group'],
        ] as $item)
            <x-ui.card class="p-6">
                <div class="mb-5 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                    <i class="{{ $item['icon'] }} text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-950 dark:text-white">{{ $item['title'] }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">{{ $item['copy'] }}</p>
            </x-ui.card>
        @endforeach
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
        <div>
            <span class="rank-badge"><i class="fas fa-route"></i> Our Direction</span>
            <h2 class="mt-4 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Built from portfolio experience into a real community product.</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                Nasiru's portfolio highlights practical software work: responsive sites, Laravel web applications, admin dashboards, and Flutter mobile apps. Akatsuki Devs takes those skills and turns them into a platform learners can actually use every day.
            </p>
            <div class="mt-5">
                <x-ui.button :href="$portfolioUrl" variant="secondary" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-briefcase"></i>
                    Explore Nasiru's Work
                </x-ui.button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach([
                ['title' => 'Web Apps', 'copy' => 'Laravel features, clean database-backed pages, auth flows, dashboards, and practical user workflows.'],
                ['title' => 'Frontend Care', 'copy' => 'Responsive layouts, clear navigation, polished public pages, and reusable UI components.'],
                ['title' => 'Admin Systems', 'copy' => 'Dynamic course management, user management, content moderation, and platform visibility.'],
                ['title' => 'Mobile Mindset', 'copy' => 'Every public and community screen is shaped for small screens first, then scaled up for desktop.'],
            ] as $card)
                <x-ui.card class="p-5">
                    <h3 class="font-black text-slate-950 dark:text-white">{{ $card['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $card['copy'] }}</p>
                </x-ui.card>
            @endforeach
        </div>
    </section>

    <section class="mt-10 ui-card overflow-hidden">
        <div class="grid gap-0 lg:grid-cols-[360px_1fr]">
            <div class="bg-gradient-to-br from-slate-950 via-red-950 to-orange-700 p-6 text-white">
                <span class="inline-flex rounded-lg bg-white/12 px-3 py-2 text-xs font-black uppercase ring-1 ring-white/15">Founder Note</span>
                <h2 class="mt-5 text-3xl font-black tracking-normal">Why I built this</h2>
                <p class="mt-4 text-sm leading-7 text-orange-50/90">Because learning alone can make even talented people quit too early.</p>
            </div>
            <div class="p-6 sm:p-8">
                <div class="space-y-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                    <p>I started Akatsuki Devs around the feeling many self-taught developers know well: you have resources everywhere, but not enough structure, feedback, or people moving with you.</p>
                    <p>This app is built to close that gap. You can learn from advanced course scrolls, post your progress, make allies, chat privately, and keep your profile growing as proof of your journey.</p>
                    <p>The long-term goal is to turn learning into a social habit that still feels focused, premium, and useful.</p>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <x-ui.button :href="auth()->check() ? route('show.post') : route('show.register')"><i class="fas fa-fire"></i> {{ auth()->check() ? 'Enter Village' : 'Join The Village' }}</x-ui.button>
                    <x-ui.button :href="$portfolioUrl" variant="secondary" target="_blank" rel="noopener noreferrer"><i class="fas fa-arrow-up-right-from-square"></i> Portfolio</x-ui.button>
                    <x-ui.button :href="route('contact')" variant="ghost"><i class="fas fa-envelope"></i> Contact</x-ui.button>
                </div>
            </div>
        </div>
    </section>
</x-ui.page>
@endsection
