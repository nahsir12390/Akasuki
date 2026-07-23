@extends('layouts.admin')

@section('title', 'Admin Panel - ' . config('app.name'))
@section('admin-heading', 'Dashboard')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-shield-halved"></i> Admin Command</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Admin Panel</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Monitor the village, review activity, and manage community safety.</p>
        </div>
        <div class="flex gap-2">
            <x-ui.button :href="route('admin.users')" variant="secondary"><i class="fas fa-users"></i> Users</x-ui.button>
            <x-ui.button :href="route('admin.posts')" variant="secondary"><i class="fas fa-scroll"></i> Posts</x-ui.button>
            <x-ui.button :href="route('admin.quizzes')" variant="secondary"><i class="fas fa-clipboard-check"></i> Quizzes</x-ui.button>
            <x-ui.button :href="route('admin.courses')" variant="secondary"><i class="fas fa-graduation-cap"></i> Courses</x-ui.button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-ui.stat-card label="Users" :value="$stats['users']" icon="fas fa-user-group" :meta="$stats['new_users'] . ' new this week'" />
        <x-ui.stat-card label="Posts" :value="$stats['posts']" icon="fas fa-scroll" :meta="$stats['new_posts'] . ' new this week'" />
        <x-ui.stat-card label="Messages" :value="$stats['messages']" icon="fas fa-comments" meta="Private chat volume" />
        <x-ui.stat-card label="Courses" :value="$stats['active_courses'] . '/' . $stats['courses']" icon="fas fa-graduation-cap" meta="Active learning paths" />
        <x-ui.stat-card label="Quiz Reviews" :value="$stats['pending_quizzes']" icon="fas fa-clipboard-check" :meta="$stats['quiz_submissions'] . ' total submissions'" />
    </div>

    <x-ui.card class="mt-6 p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-950 dark:text-white">Course Control</h2>
            <x-ui.button :href="route('admin.courses.create')" class="min-h-10 px-3"><i class="fas fa-plus"></i> Add Course</x-ui.button>
        </div>
        <div class="grid gap-3 md:grid-cols-3">
            @foreach($recentCourses as $course)
                <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-lg border border-slate-200 p-4 transition hover:bg-orange-50 dark:border-slate-800 dark:hover:bg-orange-950/30">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300"><i class="{{ $course->icon }}"></i></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $course->title }}</span>
                            <span class="block text-xs font-semibold {{ $course->is_active ? 'text-green-600 dark:text-green-400' : 'text-slate-400' }}">{{ $course->is_active ? 'Active' : 'Hidden' }}</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </x-ui.card>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-ui.card class="p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Recent Users</h2>
                <a href="{{ route('admin.users') }}" class="text-sm font-black text-orange-600 hover:text-orange-700 dark:text-orange-300">View all</a>
            </div>
            <div class="space-y-2">
                @foreach($recentUsers as $user)
                    <a href="{{ route('user.profile', $user) }}" class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-orange-50 dark:border-slate-800 dark:hover:bg-orange-950/30">
                        <x-ui.avatar :user="$user" size="md" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $user->name }}</span>
                            <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                        </span>
                        @if($user->is_admin)
                            <span class="rank-badge"><i class="fas fa-shield"></i> Admin</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card class="p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Recent Posts</h2>
                <a href="{{ route('admin.posts') }}" class="text-sm font-black text-orange-600 hover:text-orange-700 dark:text-orange-300">Review all</a>
            </div>
            <div class="space-y-2">
                @foreach($recentPosts as $post)
                    <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('user.profile', $post->user) }}" class="truncate text-sm font-black text-slate-950 hover:text-orange-700 dark:text-white dark:hover:text-orange-300">{{ $post->user->name }}</a>
                            <span class="shrink-0 text-xs font-semibold text-slate-400">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $post->content ?: 'Media-only post' }}</p>
                        <div class="mt-3 flex gap-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                            <span><i class="fas fa-heart mr-1 text-red-500"></i>{{ $post->likes_count }}</span>
                            <span><i class="fas fa-comment mr-1 text-orange-500"></i>{{ $post->comments_count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>
</x-ui.page>
@endsection
