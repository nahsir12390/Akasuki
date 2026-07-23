<header
    x-data="{ mobileOpen: false }"
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

            <div class="ml-auto hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('home') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <a href="{{ route('about') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('about') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-info-circle"></i>
                    About
                </a>
                <a href="{{ route('contact') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('contact') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-envelope"></i>
                    Contact
                </a>
                <a href="https://nasiru-portfolio.onrender.com/" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold text-slate-600 transition hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300">
                    <i class="fas fa-briefcase"></i>
                    Portfolio
                </a>
                <a href="{{ route('show.login') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-bold transition {{ request()->routeIs('show.login') ? 'nav-link-active text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700 dark:text-slate-300 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
                <x-ui.button :href="route('show.register')" class="min-h-10 px-3">
                    <i class="fas fa-user-plus"></i>
                    Join
                </x-ui.button>
                <button type="button" onclick="toggleDarkMode()" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35" aria-label="Toggle theme">
                    <i data-theme-icon class="fas fa-moon text-orange-600 dark:text-orange-300"></i>
                </button>
            </div>

            <div class="ml-auto flex items-center gap-2 md:hidden">
                <button type="button" onclick="toggleDarkMode()" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300" aria-label="Toggle theme">
                    <i data-theme-icon class="fas fa-moon text-orange-600 dark:text-orange-300"></i>
                </button>
                <button type="button" @click="mobileOpen = !mobileOpen" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" aria-label="Toggle navigation">
                    <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="border-t border-slate-100 py-4 dark:border-slate-800 md:hidden">
            <div class="grid gap-2">
                <a href="{{ route('home') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('home') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-home mr-3 text-orange-500"></i>Home</a>
                <a href="{{ route('about') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('about') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-info-circle mr-3 text-orange-500"></i>About</a>
                <a href="{{ route('contact') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('contact') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-envelope mr-3 text-orange-500"></i>Contact</a>
                <a href="https://nasiru-portfolio.onrender.com/" target="_blank" rel="noopener noreferrer" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold text-slate-700 dark:text-slate-300"><i class="fas fa-briefcase mr-3 text-orange-500"></i>Portfolio</a>
                <a href="{{ route('show.login') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-bold {{ request()->routeIs('show.login') ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/35 dark:text-orange-300' : 'text-slate-700 dark:text-slate-300' }}"><i class="fas fa-sign-in-alt mr-3 text-orange-500"></i>Login</a>
                <x-ui.button :href="route('show.register')" class="mt-2 w-full">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </x-ui.button>
            </div>
        </div>
    </nav>

    <x-course-nav />
</header>
