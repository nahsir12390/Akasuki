@extends('layouts.app')

@section('title', 'Allies - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-user-group"></i> Squad Network</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Allies</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Manage your accepted squad, open chats, and discover new developers.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('friends.requests')" variant="secondary">
                <i class="fas fa-user-clock"></i>
                Requests
                @if(($stats['pending_received_count'] ?? 0) > 0)
                    <span class="rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $stats['pending_received_count'] }}</span>
                @endif
            </x-ui.button>
            <x-ui.button :href="route('users.search')"><i class="fas fa-user-plus"></i> Find Allies</x-ui.button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Allies" :value="$stats['friends_count'] ?? 0" icon="fas fa-user-group" meta="Accepted squad" />
        <x-ui.stat-card label="Incoming" :value="$stats['pending_received_count'] ?? 0" icon="fas fa-inbox" meta="Requests waiting" />
        <x-ui.stat-card label="Sent" :value="$stats['pending_sent_count'] ?? 0" icon="fas fa-paper-plane" meta="Awaiting response" />
    </div>

    <x-ui.card class="mt-6 p-4">
        <form method="GET" action="{{ route('friends.list') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search allies by name, email, or village" class="ui-input pl-10">
            </div>
            <x-ui.button type="submit"><i class="fas fa-search"></i> Search</x-ui.button>
            @if($search)
                <x-ui.button :href="route('friends.list')" variant="secondary">Clear</x-ui.button>
            @endif
        </form>
    </x-ui.card>

    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($friends as $friend)
            <article class="ui-card overflow-hidden">
                <div class="border-b border-orange-100 bg-gradient-to-r from-orange-50/80 to-white p-4 dark:border-slate-800 dark:from-orange-950/20 dark:to-slate-950">
                    <div class="flex items-start gap-3">
                        <a href="{{ route('user.profile', $friend) }}" class="relative shrink-0">
                            <x-ui.avatar :user="$friend" size="lg" class="h-16 w-16" />
                            <span class="absolute bottom-1 right-1 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $friend->isOnline() ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('user.profile', $friend) }}" class="block truncate text-lg font-black text-slate-950 hover:text-orange-700 dark:text-white dark:hover:text-orange-300">{{ $friend->name }}</a>
                            <p class="truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $friend->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs font-bold">
                                <span class="rounded-lg bg-white px-2 py-1 text-slate-600 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-800">{{ $friend->posts_count }} posts</span>
                                <span class="rounded-lg bg-white px-2 py-1 text-slate-600 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-800">{{ $friend->comments_count }} comments</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-center justify-between gap-3 text-sm font-semibold text-slate-500 dark:text-slate-400">
                        <span><i class="fas fa-signal mr-1 text-orange-500"></i>{{ $friend->isOnline() ? 'Online now' : 'Last seen ' . ($friend->last_seen?->diffForHumans() ?? 'unknown') }}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <x-ui.button :href="route('chat.index') . '?user=' . $friend->id" class="w-full">
                            <i class="fas fa-message"></i>
                            Message
                        </x-ui.button>
                        <form action="{{ route('friends.remove', $friend) }}" method="POST" onsubmit="return confirm('Remove {{ $friend->name }} from your allies?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" class="w-full">
                                <i class="fas fa-user-minus"></i>
                                Remove
                            </x-ui.button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <x-ui.card class="col-span-full p-10 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                    <i class="fas fa-user-group text-2xl"></i>
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">{{ $search ? 'No matching allies' : 'No allies yet' }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">Find developers in the village and send ally requests to start building your squad.</p>
                <x-ui.button :href="route('users.search')" class="mt-6"><i class="fas fa-search"></i> Find Developers</x-ui.button>
            </x-ui.card>
        @endforelse
    </div>

    @if($friends->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $friends->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
