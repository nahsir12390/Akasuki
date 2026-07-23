@extends('layouts.app')

@section('title', 'Ally Requests - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-6xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-user-clock"></i> Ally Requests</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Requests</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Accept incoming allies and review requests you have sent.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('friends.list')" variant="secondary"><i class="fas fa-user-group"></i> Allies</x-ui.button>
            <x-ui.button :href="route('users.search')"><i class="fas fa-user-plus"></i> Find Allies</x-ui.button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Allies" :value="$stats['friends_count'] ?? 0" icon="fas fa-user-group" meta="Accepted squad" />
        <x-ui.stat-card label="Incoming" :value="$stats['pending_received_count'] ?? 0" icon="fas fa-inbox" meta="Needs review" />
        <x-ui.stat-card label="Sent" :value="$stats['pending_sent_count'] ?? 0" icon="fas fa-paper-plane" meta="Awaiting response" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-950 dark:text-white">Incoming Requests</h2>
                <span class="rank-badge">{{ $pendingRequests->total() }}</span>
            </div>

            <div class="space-y-3">
                @forelse($pendingRequests as $request)
                    <x-ui.card class="p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <a href="{{ route('user.profile', $request->sender) }}" class="flex min-w-0 items-center gap-3">
                                <x-ui.avatar :user="$request->sender" size="md" />
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $request->sender->name }}</span>
                                    <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $request->sender->email }}</span>
                                    <span class="mt-1 block text-xs font-semibold text-slate-400">Sent {{ $request->created_at->diffForHumans() }}</span>
                                </span>
                            </a>
                            <div class="flex gap-2">
                                <form action="{{ route('friends.accept', $request->sender) }}" method="POST">
                                    @csrf
                                    <x-ui.button type="submit" class="min-h-10 px-3"><i class="fas fa-check"></i> Accept</x-ui.button>
                                </form>
                                <form action="{{ route('friends.reject', $request->sender) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" class="min-h-10 px-3"><i class="fas fa-xmark"></i> Reject</x-ui.button>
                                </form>
                            </div>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.card class="p-8 text-center">
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                            <i class="fas fa-inbox text-xl"></i>
                        </div>
                        <h3 class="mt-4 font-black text-slate-950 dark:text-white">No incoming requests</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Your request inbox is clear.</p>
                    </x-ui.card>
                @endforelse
            </div>

            @if($pendingRequests->hasPages())
                <x-ui.card class="mt-4 p-4">{{ $pendingRequests->links() }}</x-ui.card>
            @endif
        </section>

        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-950 dark:text-white">Sent Requests</h2>
                <span class="rank-badge">{{ $sentRequests->total() }}</span>
            </div>

            <div class="space-y-3">
                @forelse($sentRequests as $request)
                    <x-ui.card class="p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <a href="{{ route('user.profile', $request->receiver) }}" class="flex min-w-0 items-center gap-3">
                                <x-ui.avatar :user="$request->receiver" size="md" />
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $request->receiver->name }}</span>
                                    <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $request->receiver->email }}</span>
                                    <span class="mt-1 block text-xs font-semibold text-slate-400">Sent {{ $request->created_at->diffForHumans() }}</span>
                                </span>
                            </a>
                            <form action="{{ route('friends.cancel', $request->receiver) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger" class="min-h-10 px-3"><i class="fas fa-ban"></i> Cancel</x-ui.button>
                            </form>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.card class="p-8 text-center">
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </div>
                        <h3 class="mt-4 font-black text-slate-950 dark:text-white">No sent requests</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">You have no pending outgoing ally requests.</p>
                    </x-ui.card>
                @endforelse
            </div>

            @if($sentRequests->hasPages())
                <x-ui.card class="mt-4 p-4">{{ $sentRequests->links() }}</x-ui.card>
            @endif
        </section>
    </div>
</x-ui.page>
@endsection
