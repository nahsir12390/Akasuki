@extends('layouts.app')

@section('title', 'Notifications - ' . config('app.name'))

@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

@section('content')
<x-ui.page width="max-w-5xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-bell"></i> Mission Signals</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Notifications</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Messages, ally requests, and important village activity in one place.</p>
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <x-ui.button type="submit" variant="secondary">
                    <i class="fas fa-check-double"></i>
                    Mark All Read
                </x-ui.button>
            </form>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Unread" :value="$unreadCount" icon="fas fa-bell" meta="Needs attention" />
        <x-ui.stat-card label="Total" :value="$notifications->total()" icon="fas fa-inbox" meta="Stored signals" />
        <x-ui.stat-card label="Latest" :value="optional($notifications->first()?->created_at)->diffForHumans() ?? 'None'" icon="fas fa-clock" meta="Most recent" />
    </div>

    <div class="mt-6 space-y-3">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = is_null($notification->read_at);
                $title = $data['title'] ?? match($notification->type) {
                    'App\\Notifications\\NewMessageNotification' => 'New squad message',
                    default => 'Notification',
                };
                $body = $data['body'] ?? ($data['message'] ?? 'Open this notification for details.');
                $icon = $data['icon'] ?? 'fas fa-bell';
                $actionUrl = $data['action_url'] ?? null;
                $avatar = $data['actor_avatar'] ?? $data['sender_avatar'] ?? null;
            @endphp

            <article class="ui-card overflow-hidden {{ $isUnread ? 'ring-1 ring-orange-200 dark:ring-orange-900' : '' }}">
                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:p-5">
                    <div class="flex min-w-0 gap-3">
                        <div class="shrink-0">
                            @if($avatar)
                                <img src="{{ $avatar }}" alt="" class="h-12 w-12 rounded-full border border-orange-200 object-cover ring-2 ring-white dark:border-orange-900 dark:ring-slate-900">
                            @else
                                <div class="grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                                    <i class="{{ $icon }}"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-black text-slate-950 dark:text-white">{{ $title }}</h2>
                                @if($isUnread)
                                    <span class="rounded-full bg-orange-600 px-2 py-0.5 text-[10px] font-black uppercase text-white">Unread</span>
                                @endif
                            </div>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $body }}</p>
                            <p class="mt-2 text-xs font-bold text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                        @if($actionUrl)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" class="min-h-10 px-3">
                                    <i class="fas fa-arrow-right"></i>
                                    Open
                                </x-ui.button>
                            </form>
                        @elseif($isUnread)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" variant="secondary" class="min-h-10 px-3">
                                    <i class="fas fa-check"></i>
                                    Read
                                </x-ui.button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" class="min-h-10 px-3">
                                <i class="fas fa-trash"></i>
                            </x-ui.button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <x-ui.card class="p-10 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                    <i class="fas fa-bell-slash text-2xl"></i>
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No notifications yet</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">When allies message you or send requests, they will appear here.</p>
            </x-ui.card>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $notifications->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
