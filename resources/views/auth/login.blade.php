@extends('layouts.app')

@section('title', 'Login - Akatsuki Devs')

@section('content')
@php($courseCount = \App\Models\Course::where('is_active', true)->count())
<x-ui.page width="max-w-6xl">
    <div class="grid min-h-[calc(100vh-220px)] items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="hidden lg:block">
            <p class="mb-4 inline-flex items-center gap-2 rounded-lg border border-orange-200 bg-white/80 px-3 py-2 text-sm font-semibold text-orange-700 shadow-sm dark:border-orange-900 dark:bg-slate-900/80 dark:text-orange-300">
                <i class="fas fa-cloud-sun"></i>
                Akatsuki Devs
            </p>
            <h1 class="max-w-xl text-5xl font-black leading-tight tracking-normal text-slate-950 dark:text-white">
                Return to your developer village.
            </h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-slate-600 dark:text-slate-300">
                Continue posting, learning, and collaborating with developers who are serious about getting better.
            </p>
            <div class="mt-8 grid max-w-lg grid-cols-3 gap-3">
                <x-ui.stat-card label="Community" value="Live" icon="fas fa-users" meta="Posts, friends, chat" />
                <x-ui.stat-card label="Learning" :value="$courseCount" icon="fas fa-scroll" meta="Course scrolls" />
                <x-ui.stat-card label="Focus" value="24/7" icon="fas fa-bolt" meta="Build momentum" />
            </div>
        </div>

        <x-ui.card padding="p-6 sm:p-8" class="mx-auto w-full max-w-md">
            <div class="mb-8">
                <div class="mb-4 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900/70">
                    <i class="fas fa-user-ninja text-xl"></i>
                </div>
                <h2 class="text-3xl font-black tracking-normal text-slate-950 dark:text-white">Welcome Back</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Sign in to continue your training.</p>
            </div>

            @if (session('success'))
                <x-ui.alert class="mb-6">{{ session('success') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error" class="mb-6">
                    {{ $errors->first() }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <x-ui.input
                    label="Email Address"
                    name="email"
                    type="email"
                    icon="fas fa-envelope"
                    placeholder="you@example.com"
                    required
                    autofocus
                />

                <div x-data="{ show: false }" class="space-y-2">
                    <label for="password" class="ui-label">
                        <i class="fas fa-lock text-orange-500"></i>
                        <span>Password</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required placeholder="Enter your password" class="ui-input pl-10 pr-12">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-orange-600" aria-label="Toggle password visibility">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="ui-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-4 text-sm">
                    <label class="inline-flex items-center gap-2 font-medium text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        Remember me
                    </label>
                    <a href="{{ route('show.forgot.password') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-300 dark:hover:text-orange-200">
                        Forgot password?
                    </a>
                </div>

                <x-ui.button type="submit" class="w-full">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Sign In
                </x-ui.button>
            </form>

            <div class="my-7 flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">or</span>
                <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <x-ui.button :href="route('auth.google')" variant="secondary">
                    <i class="fab fa-google text-red-500"></i>
                    Google
                </x-ui.button>
                <x-ui.button :href="route('auth.github')" variant="secondary">
                    <i class="fab fa-github"></i>
                    GitHub
                </x-ui.button>
            </div>

            <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                New here?
                <a href="{{ route('show.register') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-300">Create an account</a>
            </p>
        </x-ui.card>
    </div>
</x-ui.page>
@endsection
