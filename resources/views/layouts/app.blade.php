<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Akatsuki Devs - Developer Community')</title>

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

    @livewireScripts
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
