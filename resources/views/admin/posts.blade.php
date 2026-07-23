@extends('layouts.admin')

@section('title', 'Admin Posts - ' . config('app.name'))
@section('admin-heading', 'Post Moderation')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-scroll"></i> Post Moderation</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Posts</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Review village content and remove posts when needed.</p>
        </div>
        <x-ui.button :href="route('admin.dashboard')" variant="secondary"><i class="fas fa-arrow-left"></i> Dashboard</x-ui.button>
    </div>

    <x-ui.card class="mb-5 p-4">
        <form method="GET" action="{{ route('admin.posts') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by content, author, or email" class="ui-input pl-10">
            </div>
            <x-ui.button type="submit"><i class="fas fa-search"></i> Search</x-ui.button>
        </form>
    </x-ui.card>

    <div class="space-y-4">
        @forelse($posts as $post)
            <article class="ui-card overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-slate-200 p-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('user.profile', $post->user) }}" class="flex min-w-0 items-center gap-3">
                        <x-ui.avatar :user="$post->user" size="md" />
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $post->user->name }}</span>
                            <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $post->user->email }} &bull; {{ $post->created_at->format('M j, Y h:i A') }}</span>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Remove this post from the village?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" class="min-h-10 px-3">
                            <i class="fas fa-trash"></i>
                            Remove
                        </x-ui.button>
                    </form>
                </div>
                <div class="p-4 sm:p-5">
                    <p class="whitespace-pre-wrap break-words text-sm leading-7 text-slate-700 dark:text-slate-300">{{ $post->content ?: 'Media-only post' }}</p>
                    @if($post->has_media)
                        <a href="{{ $post->media_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-orange-50 px-3 py-2 text-xs font-black text-orange-700 ring-1 ring-orange-100 hover:bg-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                            <i class="fas {{ $post->is_image ? 'fa-image' : 'fa-video' }}"></i>
                            View media
                        </a>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-4 border-t border-slate-200 pt-4 text-xs font-bold text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        <span><i class="fas fa-heart mr-1 text-red-500"></i>{{ $post->likes_count }} likes</span>
                        <span><i class="fas fa-comment mr-1 text-orange-500"></i>{{ $post->comments_count }} comments</span>
                        <span><i class="fas fa-hashtag mr-1 text-slate-400"></i>{{ $post->id }}</span>
                    </div>
                </div>
            </article>
        @empty
            <x-ui.card class="p-10 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                    <i class="fas fa-scroll text-2xl"></i>
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No posts found</h2>
            </x-ui.card>
        @endforelse
    </div>

    @if($posts->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $posts->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
