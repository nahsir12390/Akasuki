<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Akatsuki Devs')</title>
    @include('components.pwa')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.awesomeFont')
    <link rel="stylesheet" href="https://cdn.iconscout.com/unicons/css/unicons.css">

    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
@php
    $adminNav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fas fa-gauge-high'],
        ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'fas fa-users-gear'],
        ['label' => 'Posts', 'route' => 'admin.posts', 'icon' => 'fas fa-scroll'],
        ['label' => 'Quiz Reviews', 'route' => 'admin.quizzes', 'icon' => 'fas fa-clipboard-check'],
        ['label' => 'Courses', 'route' => 'admin.courses', 'icon' => 'fas fa-graduation-cap'],
    ];

    $workspaceNav = [
        ['label' => 'Village Feed', 'route' => 'show.post', 'icon' => 'fas fa-fire'],
        ['label' => 'Messages', 'route' => 'chat.index', 'icon' => 'fas fa-comments'],
        ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'fas fa-bell'],
        ['label' => 'Games', 'route' => 'games.index', 'icon' => 'fas fa-gamepad'],
        ['label' => 'Settings', 'route' => 'account.settings', 'icon' => 'fas fa-sliders'],
    ];
@endphp

<div class="app-background" aria-hidden="true"></div>

<div
    x-data="{ sidebarOpen: false }"
    class="relative z-10 min-h-screen lg:grid lg:grid-cols-[280px_1fr]"
>
    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-orange-200/70 bg-white/94 shadow-2xl shadow-slate-950/10 backdrop-blur-xl transition dark:border-slate-800 dark:bg-slate-950/94 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        aria-label="Admin sidebar"
    >
        <div class="flex min-h-20 items-center justify-between border-b border-orange-100 px-5 dark:border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900">
                    <i class="fas fa-cloud-sun text-xl"></i>
                </span>
                <span>
                    <span class="block text-lg font-black tracking-normal text-slate-950 dark:text-white">Admin Village</span>
                    <span class="block text-xs font-bold text-orange-600 dark:text-orange-300">Akatsuki Devs</span>
                </span>
            </a>
            <button type="button" @click="sidebarOpen = false" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-slate-500 dark:border-slate-800 dark:text-slate-300 lg:hidden" aria-label="Close admin menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-4 py-5">
            <div class="mb-5 rounded-lg border border-orange-100 bg-orange-50/70 p-4 dark:border-orange-900/60 dark:bg-orange-950/25">
                <div class="flex items-center gap-3">
                    <x-ui.avatar :user="auth()->user()" size="md" />
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black text-slate-950 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <span class="mt-3 inline-flex rounded-lg bg-green-50 px-3 py-1.5 text-xs font-black text-green-700 ring-1 ring-green-100 dark:bg-green-950/35 dark:text-green-300 dark:ring-green-900">
                    <i class="fas fa-shield-halved mr-2"></i> Admin Access
                </span>
            </div>

            <p class="px-3 text-xs font-black uppercase tracking-wide text-slate-400">Command</p>
            <nav class="mt-2 grid gap-1">
                @foreach($adminNav as $item)
                    <a href="{{ route($item['route']) }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-black transition {{ request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*') ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' }}">
                        <i class="{{ $item['icon'] }} w-5 text-center text-orange-500"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
                <a href="{{ route('admin.courses.create') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-black transition {{ request()->routeIs('admin.courses.create') ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' }}">
                    <i class="fas fa-plus w-5 text-center text-orange-500"></i>
                    <span>Add Course</span>
                </a>
            </nav>

            <p class="mt-6 px-3 text-xs font-black uppercase tracking-wide text-slate-400">Workspace</p>
            <nav class="mt-2 grid gap-1">
                @foreach($workspaceNav as $item)
                    <a href="{{ route($item['route']) }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-black text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white">
                        <i class="{{ $item['icon'] }} w-5 text-center text-orange-500"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
                <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-black text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white">
                    <i class="fas fa-arrow-up-right-from-square w-5 text-center text-orange-500"></i>
                    <span>Public Site</span>
                </a>
            </nav>
        </div>

        <div class="border-t border-orange-100 p-4 dark:border-slate-800">
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('user.profile', auth()->user()) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-black text-slate-600 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-user"></i> Profile
                </a>
                <button type="button" onclick="toggleDarkMode()" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-black text-slate-600 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                    <i data-theme-icon class="fas fa-moon text-orange-600 dark:text-orange-300"></i>
                    <span data-theme-label>Dark</span>
                </button>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-3 text-xs font-black text-red-700 ring-1 ring-red-100 transition hover:bg-red-100 dark:bg-red-950/30 dark:text-red-300 dark:ring-red-900">
                    <i class="fas fa-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="min-w-0">
        <header class="sticky top-0 z-30 border-b border-orange-200/70 bg-white/86 px-4 py-3 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/86 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" @click="sidebarOpen = true" class="grid h-11 w-11 place-items-center rounded-lg border border-slate-200 bg-white text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 lg:hidden" aria-label="Open admin menu">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="min-w-0">
                    <p class="text-xs font-black uppercase tracking-wide text-orange-600 dark:text-orange-300">Admin Control Panel</p>
                    <h1 class="truncate text-lg font-black text-slate-950 dark:text-white">@yield('admin-heading', 'Akatsuki Devs')</h1>
                </div>

                <div class="ml-auto hidden items-center gap-2 sm:flex">
                    <a href="{{ route('admin.courses.create') }}" class="ui-btn ui-btn-primary min-h-10 px-3">
                        <i class="fas fa-plus"></i>
                        Add Course
                    </a>
                    <a href="{{ route('home') }}" class="ui-btn ui-btn-secondary min-h-10 px-3">
                        <i class="fas fa-eye"></i>
                        View Site
                    </a>
                </div>
            </div>
        </header>

        <main class="min-h-[calc(100vh-70px)]">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
