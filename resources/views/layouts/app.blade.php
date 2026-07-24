<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Akatsuki Devs - Developer Community')</title>
    @include('components.pwa')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.awesomeFont')
    <link rel="stylesheet" href="https://cdn.iconscout.com/unicons/css/unicons.css">

    @livewireStyles
    @stack('styles')
</head>
<body class="flex min-h-full flex-col text-slate-900 antialiased dark:text-slate-100">
    <div class="app-background" aria-hidden="true"></div>

    <div class="relative z-10 flex min-h-full flex-1 flex-col">
        @auth
            @include('components.navbar')
        @else
            @include('components.guest-navbar')
        @endauth

        <main class="flex-1">
            @yield('hero')
            @yield('content')
        </main>

        @include('components.footer')
    </div>

    <div id="pwaInstallPrompt" class="pwa-install-card ui-card-soft p-4" role="dialog" aria-live="polite" aria-label="Install Akatsuki Devs">
        <div class="flex gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-orange-600 text-white shadow-lg shadow-orange-600/20">
                <i class="fas fa-mobile-screen-button"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="font-black text-slate-950 dark:text-white">Install Akatsuki Devs</h2>
                <p id="pwaInstallCopy" class="mt-1 text-sm leading-5 text-slate-600 dark:text-slate-400">Open faster, keep your village close, and use the app from your home screen.</p>
                <div id="pwaManualSteps" class="mt-3 hidden rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-bold leading-5 text-orange-800 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200">
                    <span data-pwa-ios-steps class="hidden">Tap Share, then choose Add to Home Screen.</span>
                    <span data-pwa-android-steps class="hidden">Open your browser menu, then choose Install app or Add to Home screen.</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button id="pwaInstallButton" type="button" class="ui-btn ui-btn-primary min-h-10 px-3">
                        <i class="fas fa-download"></i>
                        Install
                    </button>
                    <button id="pwaDismissButton" type="button" class="ui-btn ui-btn-secondary min-h-10 px-3">
                        Later
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @auth
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    @endauth
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = 'IntersectionObserver' in window
                ? new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-fade-in-up');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' })
                : null;

            document.querySelectorAll('.fade-in-on-scroll').forEach((element) => {
                observer?.observe(element);
            });
        });
    </script>
</body>
</html>
