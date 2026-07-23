@extends('layouts.app')

@section('title', 'Forgot Password - Akatsuki Devs')

@section('content')
<x-ui.page width="max-w-6xl">
    <div class="grid min-h-[calc(100vh-220px)] items-center gap-10 lg:grid-cols-[1fr_420px]">
        <div class="hidden lg:block">
            <span class="rank-badge"><i class="fas fa-key"></i> Account Recovery</span>
            <h1 class="mt-5 max-w-xl text-5xl font-black leading-tight tracking-normal text-slate-950 dark:text-white">Recover your village access.</h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-slate-600 dark:text-slate-300">Enter your email and we’ll send a reset link so you can return to training.</p>
        </div>

        <x-ui.card padding="p-6 sm:p-8" class="mx-auto w-full max-w-md">
            <div class="mb-8">
                <div class="mb-4 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900/70">
                    <i class="fas fa-key text-xl"></i>
                </div>
                <h2 class="text-3xl font-black tracking-normal text-slate-950 dark:text-white">Forgot Password</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">We’ll email you a reset link.</p>
            </div>

            @if (session('status'))
                <x-ui.alert class="mb-6">{{ session('status') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error" class="mb-6">{{ $errors->first() }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('forgot.password') }}" class="space-y-5">
                @csrf
                <x-ui.input label="Email Address" name="email" type="email" icon="fas fa-envelope" :value="old('email')" placeholder="you@example.com" required autofocus />
                <x-ui.button type="submit" class="w-full">
                    <i class="fas fa-paper-plane"></i>
                    Send Reset Link
                </x-ui.button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                Remembered it?
                <a href="{{ route('show.login') }}" class="font-bold text-orange-600 hover:text-orange-700 dark:text-orange-300">Sign in</a>
            </p>
        </x-ui.card>
    </div>
</x-ui.page>
@endsection
