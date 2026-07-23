@php
    $currentUser = auth()->user();
    $messageUnreadCount = $currentUser
        ? $currentUser->unreadNotifications()->where('type', 'App\Notifications\NewMessageNotification')->count()
        : 0;
    $notificationCount = $currentUser ? $currentUser->unreadNotifications()->count() : 0;
    $pendingRequests = $currentUser ? $currentUser->pendingFriendRequests()->count() : 0;
    $communityCount = $currentUser ? \App\Models\Post::where('user_id', '!=', $currentUser->id)->count() : 0;
@endphp

<header
    x-data="{
        mobileOpen: false,
        userOpen: false,
        searchOpen: false,
        query: '',
        results: [],
        loading: false,
        async search() {
            if (this.query.trim().length < 2) {
                this.results = [];
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`{{ route('users.quick-search') }}?q=${encodeURIComponent(this.query)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.results = response.ok ? await response.json() : [];
            } finally {
                this.loading = false;
            }
        }
    }"
    class="sticky top-0 z-50 border-b border-orange-200/80 bg-white/88 shadow-sm backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/88"
>
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center gap-4">
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-3">
                <span class="relative grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 transition group-hover:bg-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900">
                    <span class="absolute inset-0 rounded-lg bg-orange-400/20 blur-md opacity-0 transition group-hover:opacity-100"></span>
                    <i class="fas fa-cloud-sun relative text-xl"></i>
                </span>
                <span class="leading-tight">
                    <span class="block text-lg font-black tracking-normal text-slate-950 dark:text-white">Akatsuki Devs</span>
                    <span class="hidden text-xs font-semibold text-orange-600 dark:text-orange-300 sm:block">Hidden Code Village</span>
                </span>
            </a>

            <div class="hidden min-w-0 flex-1 justify-center lg:flex">
                <div class="relative w-full max-w-md">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="search"
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="searchOpen = true"
                        @click.outside="searchOpen = false"
                        placeholder="Search shinobi developers"
                        class="ui-input h-11 pl-10 pr-10"
                    >
                    <i x-show="loading" x-cloak class="fas fa-spinner fa-spin absolute right-3 top-1/2 -translate-y-1/2 text-orange-500"></i>

                    <div
                        x-show="searchOpen && query.length > 0"
                        x-cloak
                        x-transition
                        class="absolute left-0 right-0 top-full z-50 mt-2 max-h-96 overflow-y-auto rounded-lg border border-slate-200 bg-white py-2 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
                    >
                        <template x-if="results.length > 0">
                            <template x-for="user in results" :key="user.id">
                                <a :href="`/user/${user.id}`" @click="searchOpen = false" class="flex items-center gap-3 px-4 py-3 hover:bg-orange-50 dark:hover:bg-orange-950/35">
                                    <img :src="user.profile_photo_url" :alt="user.name" class="h-10 w-10 rounded-full border border-orange-200 object-cover">
                                    <span class="min-w-0">
                                        <span x-text="user.name" class="block truncate text-sm font-bold text-slate-900 dark:text-white"></span>
                                        <span x-text="user.email" class="block truncate text-xs text-slate-500 dark:text-slate-400"></span>
                                    </span>
                                </a>
                            </template>
                        </template>
                        <div x-show="results.length === 0 && query.length > 1 && !loading" class="px-4 py-6 text-center text-sm font-semibold text-slate-500">
                            No developers found.
                        </div>
                    </div>
                </div>
            </div>

            <div class="ml-auto hidden items-center gap-1 lg:flex">
                <a href="{{ route('show.post') }}" class="relative inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('show.post') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-users"></i>
                    Village
                    @if($communityCount > 0)
                        <span class="ml-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $communityCount }}</span>
                    @endif
                </a>

                <a href="{{ route('chat.index') }}" class="relative inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('chat.*') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-comments"></i>
                    Messages
                    @if($messageUnreadCount > 0)
                        <span class="ml-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $messageUnreadCount }}</span>
                    @endif
                </a>

                <a href="{{ route('games.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('games.*') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-gamepad"></i>
                    Games
                </a>

                <a href="{{ route('notifications.index') }}" class="relative grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35 {{ request()->routeIs('notifications.*') ? 'nav-link-active text-orange-600' : '' }}" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    @if($notificationCount > 0)
                        <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $notificationCount }}</span>
                    @endif
                </a>

                @if($currentUser->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('admin.*') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                        <i class="fas fa-shield-halved"></i>
                        Admin
                    </a>
                @endif

                <x-ui.button :href="route('createData')" class="min-h-10 px-3">
                    <i class="fas fa-plus"></i>
                    Scroll
                </x-ui.button>

                <button type="button" onclick="toggleDarkMode()" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35" aria-label="Toggle theme">
                    <i data-theme-icon class="fas fa-moon text-orange-600 dark:text-orange-300"></i>
                </button>

                <div class="relative" @click.outside="userOpen = false">
                    <button type="button" @click="userOpen = !userOpen" class="flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm font-bold text-slate-700 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-800 dark:hover:bg-orange-950/35">
                        <x-ui.avatar :user="$currentUser" size="sm" />
                        <span class="max-w-28 truncate">{{ $currentUser->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-slate-400" :class="{ 'rotate-180': userOpen }"></i>
                    </button>

                    <div x-show="userOpen" x-cloak x-transition class="absolute right-0 top-full z-50 mt-2 w-72 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                        <div class="border-b border-slate-100 px-4 py-4 dark:border-slate-800">
                            <p class="truncate text-sm font-black text-slate-950 dark:text-white">{{ $currentUser->name }}</p>
                            <p class="truncate text-xs font-medium text-slate-500 dark:text-slate-400">{{ $currentUser->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('user.myprofile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300"><i class="fas fa-user text-orange-500"></i> My Profile</a>
                            <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300">
                                <i class="fas fa-bell text-orange-500"></i>
                                Notifications
                                @if($notificationCount > 0)
                                    <span class="ml-auto rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $notificationCount }}</span>
                                @endif
                            </a>
                            @if($currentUser->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300"><i class="fas fa-shield-halved text-orange-500"></i> Admin Panel</a>
                                <a href="{{ route('admin.courses') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300"><i class="fas fa-graduation-cap text-orange-500"></i> Manage Courses</a>
                            @endif
                            <a href="{{ route('friends.list') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300">
                                <i class="fas fa-user-friends text-orange-500"></i>
                                Allies
                                @if($pendingRequests > 0)
                                    <span class="ml-auto rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $pendingRequests }}</span>
                                @endif
                            </a>
                            <a href="{{ route('account.settings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300"><i class="fas fa-cog text-orange-500"></i> Settings</a>
                            <form action="{{ route('logout') }}" method="POST" class="mt-2 border-t border-slate-100 pt-2 dark:border-slate-800">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ml-auto flex items-center gap-2 lg:hidden">
                <button type="button" onclick="toggleDarkMode()" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300" aria-label="Toggle theme">
                    <i data-theme-icon class="fas fa-moon text-orange-600 dark:text-orange-300"></i>
                </button>
                <a href="{{ route('chat.index') }}" class="relative grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300" aria-label="Messages">
                    <i class="fas fa-comments"></i>
                    @if($messageUnreadCount > 0)
                        <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $messageUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('notifications.index') }}" class="relative grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    @if($notificationCount > 0)
                        <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $notificationCount }}</span>
                    @endif
                </a>
                <button type="button" @click="mobileOpen = !mobileOpen" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" aria-label="Toggle navigation">
                    <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="border-t border-slate-100 py-4 dark:border-slate-800 lg:hidden">
            <div class="mb-4">
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="search" x-model="query" @input.debounce.300ms="search()" placeholder="Search developers" class="ui-input pl-10">
                </div>
                <div x-show="query.length > 0" x-cloak class="mt-2 overflow-hidden rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                    <template x-for="user in results" :key="user.id">
                        <a :href="`/user/${user.id}`" class="flex items-center gap-3 px-3 py-3">
                            <img :src="user.profile_photo_url" :alt="user.name" class="h-9 w-9 rounded-full object-cover">
                            <span x-text="user.name" class="truncate text-sm font-bold"></span>
                        </a>
                    </template>
                    <div x-show="results.length === 0 && query.length > 1 && !loading" class="px-3 py-4 text-center text-sm font-semibold text-slate-500">No developers found.</div>
                </div>
            </div>

            <div class="grid gap-2">
                <a href="{{ route('show.post') }}" class="flex items-center justify-between rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('show.post') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><span><i class="fas fa-users mr-3 text-orange-500"></i>Village</span>@if($communityCount > 0)<span class="rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $communityCount }}</span>@endif</a>
                <a href="{{ route('games.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('games.*') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-gamepad mr-3 text-orange-500"></i>Games</a>
                <a href="{{ route('createData') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><i class="fas fa-plus mr-3 text-orange-500"></i>Create Scroll</a>
                <a href="{{ route('user.myprofile') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><i class="fas fa-user mr-3 text-orange-500"></i>My Profile</a>
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('notifications.*') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><span><i class="fas fa-bell mr-3 text-orange-500"></i>Notifications</span>@if($notificationCount > 0)<span class="rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $notificationCount }}</span>@endif</a>
                @if($currentUser->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('admin.*') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-shield-halved mr-3 text-orange-500"></i>Admin Panel</a>
                    <a href="{{ route('admin.courses') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><i class="fas fa-graduation-cap mr-3 text-orange-500"></i>Manage Courses</a>
                @endif
                <a href="{{ route('friends.list') }}" class="flex items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><span><i class="fas fa-user-friends mr-3 text-orange-500"></i>Allies</span>@if($pendingRequests > 0)<span class="rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">{{ $pendingRequests }}</span>@endif</a>
                <a href="{{ route('account.settings') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><i class="fas fa-cog mr-3 text-orange-500"></i>Settings</a>
                <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100 pt-2 dark:border-slate-800">
                    @csrf
                    <button type="submit" class="flex w-full items-center rounded-lg px-3 py-3 text-sm font-bold text-red-600 dark:text-red-400"><i class="fas fa-sign-out-alt mr-3"></i>Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <x-course-nav />
</header>
