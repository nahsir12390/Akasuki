@extends('layouts.app')

@section('title', $user->name . ' - Shinobi Profile')

@php
    $isOwnProfile = auth()->check() && auth()->id() === $user->id;
    $isOnline = $user->show_online_status !== false && $user->isOnline();
    $friendsCount = $user->getFriends()->count();
    $skills = collect($user->skills ?? [])->filter()->values();
    $interests = collect($user->interests ?? [])->filter()->values();
@endphp

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
    @endif

    <section class="ui-card overflow-hidden">
        <div class="relative min-h-56 bg-gradient-to-br from-slate-950 via-red-950 to-orange-700">
            <div class="absolute inset-0 shinobi-grid opacity-30"></div>
            <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/45 to-transparent"></div>
            <div class="relative flex min-h-56 flex-col justify-between p-5 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="rank-badge bg-white/90 text-orange-700"><i class="fas fa-cloud-sun"></i> Hidden Code Village</span>
                    <span class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2 text-xs font-black text-white ring-1 ring-white/15 backdrop-blur">
                        <span class="h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-green-400' : 'bg-slate-400' }}"></span>
                        {{ $isOnline ? 'Online now' : 'Offline' }}
                    </span>
                </div>

                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-end">
                        <div class="relative shrink-0">
                            <x-ui.avatar :user="$user" size="xl" class="h-28 w-28 border-4 border-white ring-orange-200 sm:h-36 sm:w-36" />
                            @if($isOwnProfile)
                                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="absolute bottom-1 right-1">
                                    @csrf
                                    <input id="profile-photo-input" type="file" name="profile_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('profile-photo-input').click()" class="grid h-10 w-10 place-items-center rounded-lg bg-white text-orange-600 shadow-lg transition hover:bg-orange-50" aria-label="Change profile photo">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="min-w-0 pb-1 text-white">
                            <h1 class="break-words text-3xl font-black tracking-normal sm:text-5xl">{{ $user->name }}</h1>
                            <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-orange-50/90">
                                {{ $user->bio ?: 'A developer in the Hidden Code Village, building skills one scroll at a time.' }}
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold">
                                <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-envelope mr-1"></i>{{ $user->email }}</span>
                                @if($user->location)
                                    <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-location-dot mr-1"></i>{{ $user->location }}</span>
                                @endif
                                <span class="rounded-lg bg-white/12 px-3 py-2 ring-1 ring-white/15"><i class="fas fa-calendar mr-1"></i>Joined {{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($isOwnProfile)
                            <x-ui.button :href="route('account.settings')" class="bg-white text-orange-700 hover:bg-orange-50">
                                <i class="fas fa-sliders"></i>
                                Settings
                            </x-ui.button>
                            <x-ui.button :href="route('createData')" variant="secondary" class="bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20">
                                <i class="fas fa-plus"></i>
                                New Scroll
                            </x-ui.button>
                        @elseif(auth()->check())
                            @include('components.friendship-buttons', ['user' => $user])
                            <x-ui.button :href="route('chat.index') . '?user=' . $user->id">
                                <i class="fas fa-message"></i>
                                Message
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Allies" :value="$friendsCount" icon="fas fa-user-group" meta="Accepted connections" />
        <x-ui.stat-card label="Scrolls" :value="$engagementStats['post_count']" icon="fas fa-scroll" meta="Published posts" />
        <x-ui.stat-card label="Chakra" :value="$engagementStats['engagement_score']" icon="fas fa-bolt" :meta="$engagementStats['total_likes'] . ' likes, ' . $engagementStats['total_comments'] . ' comments'" />
        <x-ui.stat-card label="Village Days" :value="round($user->created_at->diffInDays(now()))" icon="fas fa-calendar-days" meta="Member activity age" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[320px_1fr]">
        <aside class="space-y-4 lg:sticky lg:top-36 lg:self-start">
            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Profile Signal</h2>
                <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600 dark:text-slate-400">
                    <div class="flex items-center justify-between gap-3">
                        <span><i class="fas fa-globe mr-2 text-orange-500"></i>Profile</span>
                        <span class="text-slate-950 dark:text-white">{{ $user->public_profile !== false ? 'Public' : 'Private' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span><i class="fas fa-eye mr-2 text-orange-500"></i>Status</span>
                        <span class="text-slate-950 dark:text-white">{{ $user->show_online_status !== false ? 'Visible' : 'Hidden' }}</span>
                    </div>
                    @if($user->website)
                        <a href="{{ $user->website }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2 text-orange-700 hover:bg-orange-50 dark:border-slate-800 dark:text-orange-300 dark:hover:bg-orange-950/30">
                            <span><i class="fas fa-link mr-2"></i>Website</span>
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Skills</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($skills as $skill)
                        <span class="rounded-lg bg-orange-50 px-3 py-2 text-xs font-black text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">{{ $skill }}</span>
                    @empty
                        <p class="text-sm leading-6 text-slate-500 dark:text-slate-400">No skills added yet.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Interests</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($interests as $interest)
                        <span class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-black text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-800">{{ $interest }}</span>
                    @empty
                        <p class="text-sm leading-6 text-slate-500 dark:text-slate-400">No interests added yet.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </aside>

        <main id="posts" class="min-w-0">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="rank-badge"><i class="fas fa-scroll"></i> Personal Scrolls</span>
                    <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">{{ $isOwnProfile ? 'Your Posts' : $user->name . "'s Posts" }}</h2>
                </div>
                @if($isOwnProfile)
                    <x-ui.button :href="route('createData')" variant="secondary">
                        <i class="fas fa-plus"></i>
                        Create Scroll
                    </x-ui.button>
                @endif
            </div>

            <div class="space-y-5">
                @forelse($posts as $post)
                    <article id="post-{{ $post->id }}" class="ui-card overflow-hidden">
                        <div class="flex items-start justify-between gap-4 border-b border-orange-100 bg-gradient-to-r from-orange-50/80 to-white px-4 py-4 dark:border-slate-800 dark:from-orange-950/20 dark:to-slate-950 sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <x-ui.avatar :user="$post->user" size="md" />
                                <div class="min-w-0">
                                    <p class="truncate font-black text-slate-950 dark:text-white">{{ $post->user->name }}</p>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        <i class="fas fa-clock mr-1 text-orange-500"></i>{{ $post->created_at->diffForHumans() }}
                                        @if($post->updated_at->gt($post->created_at))
                                            <span class="mx-1">&bull;</span><span class="text-green-600 dark:text-green-400">Edited</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($isOwnProfile)
                                <a href="{{ route('editData', $post->id) }}" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-orange-600 transition hover:bg-orange-50 dark:border-slate-800 dark:text-orange-300 dark:hover:bg-orange-950/30" aria-label="Edit post">
                                    <i class="fas fa-pen"></i>
                                </a>
                            @endif
                        </div>

                        <div class="p-4 sm:p-5">
                            @if($post->content)
                                <p class="whitespace-pre-wrap break-words text-base leading-8 text-slate-800 dark:text-slate-200">{{ $post->content }}</p>
                            @endif

                            @if($post->has_media)
                                <div class="mt-4 overflow-hidden rounded-lg border border-orange-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-950">
                                    @if($post->is_image)
                                        <button type="button" onclick="openMediaModal(@js($post->media_url), 'image')" class="block w-full cursor-zoom-in">
                                            <img src="{{ $post->media_url }}" alt="Post image" class="max-h-[460px] w-full object-contain">
                                        </button>
                                    @elseif($post->is_video)
                                        <video src="{{ $post->media_url }}" controls poster="{{ $post->thumbnail_url }}" preload="metadata" class="max-h-[460px] w-full bg-black"></video>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
                                <div class="flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    <span><i class="fas fa-heart mr-1 text-red-500"></i>{{ $post->likes_count }} {{ \Illuminate\Support\Str::plural('like', $post->likes_count) }}</span>
                                    <livewire:post-comment-count :post="$post" :key="'profile-comment-count-'.$post->id" />
                                </div>
                                @auth
                                    <div class="flex gap-2">
                                        <livewire:post-like-button :post="$post" :full-width="false" :show-count="false" :key="'profile-like-'.$post->id" />
                                        <button type="button" onclick="toggleComments({{ $post->id }})" class="ui-btn ui-btn-secondary">
                                            <i class="far fa-comment"></i>
                                            Comment
                                        </button>
                                    </div>
                                @endauth
                            </div>

                            <div id="comments-section-{{ $post->id }}" class="mt-4 hidden border-t border-slate-200 pt-4 dark:border-slate-800">
                                <livewire:post-comments :post="$post" :key="'profile-comments-'.$post->id" />
                            </div>
                        </div>
                    </article>
                @empty
                    <x-ui.card class="p-10 text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                            <i class="fas fa-scroll text-2xl"></i>
                        </div>
                        <h3 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No scrolls yet</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">
                            {{ $isOwnProfile ? 'Share your first update with the village.' : $user->name . ' has not published any posts yet.' }}
                        </p>
                    </x-ui.card>
                @endforelse
            </div>

            @if($posts->hasPages())
                <x-ui.card class="mt-6 p-4">
                    {{ $posts->onEachSide(1)->links() }}
                </x-ui.card>
            @endif
        </main>
    </div>
</x-ui.page>

<div id="mediaModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 p-4 backdrop-blur">
    <button type="button" onclick="closeMediaModal()" class="absolute right-4 top-4 grid h-11 w-11 place-items-center rounded-lg bg-white/10 text-white hover:bg-white/20" aria-label="Close media">
        <i class="fas fa-times"></i>
    </button>
    <div id="mediaModalContent" class="flex max-h-full max-w-6xl items-center justify-center"></div>
</div>
@endsection

@push('scripts')
<script>
    function openMediaModal(mediaUrl, mediaType) {
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('mediaModalContent');

        content.innerHTML = mediaType === 'image'
            ? `<img src="${mediaUrl}" alt="Full size image" class="max-h-[88vh] max-w-full rounded-lg object-contain shadow-2xl">`
            : `<video src="${mediaUrl}" controls autoplay class="max-h-[88vh] max-w-full rounded-lg shadow-2xl"></video>`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        const video = modal.querySelector('video');
        if (video) video.pause();
    }

    function toggleComments(postId) {
        document.getElementById(`comments-section-${postId}`)?.classList.toggle('hidden');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMediaModal();
    });

    document.getElementById('mediaModal')?.addEventListener('click', function (event) {
        if (event.target === this) closeMediaModal();
    });
</script>
@endpush
