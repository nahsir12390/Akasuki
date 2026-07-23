@extends('layouts.admin')

@section('title', 'Admin Users - ' . config('app.name'))
@section('admin-heading', 'User Management')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-users-gear"></i> User Management</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Users</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Search members and manage admin access.</p>
        </div>
        <x-ui.button :href="route('admin.dashboard')" variant="secondary"><i class="fas fa-arrow-left"></i> Dashboard</x-ui.button>
    </div>

    <x-ui.card class="mb-5 p-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by name or email" class="ui-input pl-10">
            </div>
            <x-ui.button type="submit"><i class="fas fa-search"></i> Search</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-black uppercase text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Activity</th>
                        <th class="px-4 py-3">Joined</th>
                        <th class="px-4 py-3 text-right">Access</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="bg-white dark:bg-slate-950">
                            <td class="px-4 py-4">
                                <a href="{{ route('user.profile', $user) }}" class="flex items-center gap-3">
                                    <x-ui.avatar :user="$user" size="md" />
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $user->name }}</span>
                                        <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-600 dark:text-slate-400">
                                {{ $user->posts_count }} posts &bull; {{ $user->comments_count }} comments &bull; {{ $user->likes_count }} likes
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-4 py-4 text-right">
                                @if($user->is_admin)
                                    <span class="mr-2 inline-flex rounded-lg bg-green-50 px-3 py-2 text-xs font-black text-green-700 ring-1 ring-green-100 dark:bg-green-950/35 dark:text-green-300 dark:ring-green-900">Admin</span>
                                @endif
                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="{{ $user->is_admin ? 'danger' : 'secondary' }}" class="min-h-10 px-3" :disabled="$user->is(auth()->user())">
                                        <i class="fas {{ $user->is_admin ? 'fa-user-minus' : 'fa-user-shield' }}"></i>
                                        {{ $user->is_admin ? 'Remove' : 'Make Admin' }}
                                    </x-ui.button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-sm font-semibold text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($users->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $users->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
