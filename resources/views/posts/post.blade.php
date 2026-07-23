@extends('layouts.app')

@section('title', 'Village - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-7xl">
    <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="space-y-4 lg:sticky lg:top-36 lg:self-start">
            <x-ui.card class="scroll-panel p-5">
                <span class="rank-badge"><i class="fas fa-cloud-sun"></i> Hidden Code Village</span>
                <h1 class="mt-4 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Village</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">
                    Share scrolls, inspect builds, and keep up with your developer squad.
                </p>
                <x-ui.button :href="route('createData')" class="mt-5 w-full">
                    <i class="fas fa-plus"></i>
                    Create Scroll
                </x-ui.button>
            </x-ui.card>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <x-ui.stat-card label="Village Scrolls" :value="$posts->total()" icon="fas fa-scroll" meta="All posts" />
                <x-ui.stat-card label="Your Scrolls" :value="auth()->user()->posts()->count()" icon="fas fa-user-ninja" meta="Published by you" />
                <x-ui.stat-card label="Allies" :value="auth()->user()->getFriends()->count()" icon="fas fa-user-group" meta="Your squad" />
            </div>
        </aside>

        <main class="min-w-0">
            @if (session('success'))
                <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
            @endif

            <div class="space-y-5">
                @forelse ($posts as $post)
                    <article id="post-{{ $post->id }}" class="ui-card overflow-hidden">
                        <div class="border-b border-orange-100 bg-gradient-to-r from-orange-50/80 to-white px-4 py-4 dark:border-slate-800 dark:from-orange-950/20 dark:to-slate-950 sm:px-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <a href="{{ route('user.profile', $post->user->id) }}" class="shrink-0">
                                        <img src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}" class="h-12 w-12 rounded-full border border-orange-200 object-cover ring-2 ring-white dark:border-orange-900 dark:ring-slate-900">
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ route('user.profile', $post->user->id) }}" class="block truncate font-black text-slate-950 hover:text-orange-700 dark:text-white dark:hover:text-orange-300">
                                            {{ $post->user->name ?? 'Unknown User' }}
                                        </a>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            <span><i class="fas fa-clock mr-1 text-orange-500"></i>{{ $post->created_at->diffForHumans() }}</span>
                                            <span>&bull;</span>
                                            <span><i class="fas fa-eye mr-1 text-orange-500"></i>Village public</span>
                                            @if($post->updated_at->gt($post->created_at))
                                                <span>&bull;</span>
                                                <span class="text-green-600 dark:text-green-400"><i class="fas fa-edit mr-1"></i>Edited</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->id() === $post->user_id)
                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300" aria-label="Post actions">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-2 w-44 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                                            <a href="{{ route('editData', $post->id) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300">
                                                <i class="fas fa-edit text-orange-500"></i>
                                                Edit Scroll
                                            </a>
                                            <button type="button" onclick="confirmDelete({{ $post->id }})" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-bold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30">
                                                <i class="fas fa-trash"></i>
                                                Delete Scroll
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 sm:p-5">
                            @if($post->content)
                                <p class="whitespace-pre-wrap break-words text-base leading-8 text-slate-800 dark:text-slate-200">{{ $post->content }}</p>
                            @endif

                            @if($post->has_media)
                                <div class="mt-4 overflow-hidden rounded-lg border border-orange-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-950">
                                    @if($post->is_image)
                                        <button type="button" onclick="openMediaModal(@js($post->media_url), 'image')" class="block w-full cursor-zoom-in">
                                            <img src="{{ $post->media_url }}" alt="Post image" class="max-h-[520px] w-full object-contain">
                                        </button>
                                    @elseif($post->is_video)
                                        <video src="{{ $post->media_url }}" controls poster="{{ $post->thumbnail_url }}" preload="metadata" class="max-h-[520px] w-full bg-black"></video>
                                    @endif
                                    <div class="flex items-center justify-between border-t border-orange-200 bg-orange-50 px-3 py-2 text-xs font-bold text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                                        <span><i class="fas {{ $post->is_image ? 'fa-image' : 'fa-video' }} mr-1 text-orange-500"></i>{{ $post->is_image ? 'Image scroll' : 'Video scroll' }}</span>
                                        <a href="{{ $post->media_url }}" download class="text-orange-600 hover:text-orange-700 dark:text-orange-300"><i class="fas fa-download mr-1"></i>Download</a>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
                                <div class="flex flex-wrap items-center gap-3 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    <livewire:post-like-button :post="$post" :full-width="false" :show-count="true" :show-button="false" :key="'post-like-inline-'.$post->id" />
                                    <livewire:post-comment-count :post="$post" :key="'post-comment-count-'.$post->id" />
                                </div>
                                <button type="button" onclick="sharePost({{ $post->id }})" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 dark:border-slate-800 dark:text-slate-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35 dark:hover:text-orange-300">
                                    <i class="fas fa-share"></i>
                                    Share
                                </button>
                            </div>

                            @auth
                                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-200 pt-4 dark:border-slate-800 sm:flex">
                                    <livewire:post-like-button :post="$post" :full-width="true" :show-count="true" :key="'post-like-'.$post->id" />
                                    <button type="button" onclick="toggleComments({{ $post->id }})" class="ui-btn ui-btn-secondary flex-1">
                                        <i class="far fa-comment"></i>
                                        Comment
                                    </button>
                                </div>
                            @endauth

                            <div id="comments-section-{{ $post->id }}" class="mt-4 hidden border-t border-slate-200 pt-4 dark:border-slate-800">
                                <livewire:post-comments :post="$post" :key="'post-comments-'.$post->id" />
                            </div>
                        </div>
                    </article>
                @empty
                    <x-ui.card class="p-10 text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                            <i class="fas fa-scroll text-2xl"></i>
                        </div>
                        <h2 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No village scrolls yet</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">Start the village feed by sharing your first lesson, build, or question.</p>
                        <x-ui.button :href="route('createData')" class="mt-6">
                            <i class="fas fa-plus"></i>
                            Create First Scroll
                        </x-ui.button>
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

<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
    <x-ui.card class="w-full max-w-sm p-6 text-center">
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-red-50 text-red-600 dark:bg-red-950/35 dark:text-red-300">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <h2 class="mt-4 text-xl font-black text-slate-950 dark:text-white">Delete this scroll?</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">This action cannot be undone.</p>
        <div class="mt-6 grid grid-cols-2 gap-3">
            <x-ui.button type="button" variant="secondary" onclick="closeDeleteModal()">Cancel</x-ui.button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" class="w-full">Delete</x-ui.button>
            </form>
        </div>
    </x-ui.card>
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

    function confirmDelete(postId) {
        const modal = document.getElementById('deleteModal');
        document.getElementById('deleteForm').action = `{{ url('post') }}/${postId}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function sharePost(postId) {
        const postUrl = `${window.location.origin}${window.location.pathname}#post-${postId}`;

        if (navigator.share) {
            navigator.share({ title: 'Akatsuki Devs Village Scroll', url: postUrl }).catch(() => {});
            return;
        }

        navigator.clipboard?.writeText(postUrl);
    }

    function toggleComments(postId) {
        document.getElementById(`comments-section-${postId}`)?.classList.toggle('hidden');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMediaModal();
            closeDeleteModal();
        }
    });

    document.getElementById('mediaModal')?.addEventListener('click', function (event) {
        if (event.target === this) closeMediaModal();
    });

    document.getElementById('deleteModal')?.addEventListener('click', function (event) {
        if (event.target === this) closeDeleteModal();
    });
</script>
@endpush
