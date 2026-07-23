@extends('layouts.app')

@section('title', 'Register - Akatsuki Devs')

@section('content')
@php($courseCount = \App\Models\Course::where('is_active', true)->count())
<x-ui.page width="max-w-6xl">
    <div class="grid min-h-[calc(100vh-220px)] items-center gap-10 lg:grid-cols-[0.95fr_1.05fr]">
        <x-ui.card padding="p-6 sm:p-8" class="mx-auto w-full max-w-md">
            <div class="mb-8">
                <div class="mb-4 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900/70">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <h2 class="text-3xl font-black tracking-normal text-slate-950 dark:text-white">Create Account</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Join the developer community and start building momentum.</p>
            </div>

            @if ($errors->any())
                <x-ui.alert type="error" class="mb-6">
                    {{ $errors->first() }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <x-ui.input
                    label="Display Name"
                    name="name"
                    icon="fas fa-user"
                    placeholder="Your name"
                    required
                    autofocus
                />

                <x-ui.input
                    label="Email Address"
                    name="email"
                    type="email"
                    icon="fas fa-envelope"
                    placeholder="you@example.com"
                    required
                />

                <div x-data="{ show: false }" class="space-y-5">
                    <div class="space-y-2">
                        <label for="password" class="ui-label">
                            <i class="fas fa-lock text-orange-500"></i>
                            <span>Password</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="password" name="password" :type="show ? 'text' : 'password'" required placeholder="Minimum 6 characters" class="ui-input pl-10 pr-12">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-orange-600" aria-label="Toggle password visibility">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="ui-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="ui-label">
                            <i class="fas fa-shield-halved text-orange-500"></i>
                            <span>Confirm Password</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-shield-halved pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required placeholder="Repeat password" class="ui-input pl-10">
                        </div>
                    </div>
                </div>

                <label class="flex gap-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <input type="checkbox" required class="mt-1 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    <span>I agree to use Akatsuki Devs respectfully and keep the community helpful.</span>
                </label>

                <x-ui.button type="submit" class="w-full">
                    <i class="fas fa-user-plus"></i>
                    Create Account
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
                Already have an account?
                <a href="{{ route('show.login') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-300">Sign in</a>
            </p>
        </x-ui.card>

        <div class="hidden lg:block">
            <p class="mb-4 inline-flex items-center gap-2 rounded-lg border border-orange-200 bg-white/80 px-3 py-2 text-sm font-semibold text-orange-700 shadow-sm dark:border-orange-900 dark:bg-slate-900/80 dark:text-orange-300">
                <i class="fas fa-code"></i>
                Developer Community
            </p>
            <h1 class="max-w-xl text-5xl font-black leading-tight tracking-normal text-slate-950 dark:text-white">
                A sharper place to learn, post, and grow.
            </h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-slate-600 dark:text-slate-300">
                Build a profile, share progress, find allies, and keep your learning organized with a premium social workspace for developers.
            </p>
            <div class="mt-8 grid max-w-xl grid-cols-3 gap-3">
                <x-ui.stat-card label="Scrolls" :value="$courseCount" icon="fas fa-scroll" meta="Advanced paths" />
                <x-ui.stat-card label="Allies" value="Connect" icon="fas fa-user-group" meta="Friends-only chat" />
                <x-ui.stat-card label="Posts" value="Share" icon="fas fa-pen" meta="Track growth" />
            </div>
        </div>
    </div>
</x-ui.page>
@endsection
