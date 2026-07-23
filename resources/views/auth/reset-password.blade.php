@extends('layouts.app')

@section('title', 'Reset Password - Akatsuki Devs')

@section('content')
<x-ui.page width="max-w-6xl">
    <div class="grid min-h-[calc(100vh-220px)] items-center gap-10 lg:grid-cols-[1fr_420px]">
        <div class="hidden lg:block">
            <span class="rank-badge"><i class="fas fa-shield-halved"></i> Secure Reset</span>
            <h1 class="mt-5 max-w-xl text-5xl font-black leading-tight tracking-normal text-slate-950 dark:text-white">Set a fresh password.</h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-slate-600 dark:text-slate-300">Choose a strong password and get back to your developer mission.</p>
        </div>

        <x-ui.card padding="p-6 sm:p-8" class="mx-auto w-full max-w-md">
            <div class="mb-8">
                <div class="mb-4 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900/70">
                    <i class="fas fa-shield-halved text-xl"></i>
                </div>
                <h2 class="text-3xl font-black tracking-normal text-slate-950 dark:text-white">Reset Password</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Enter your new password below.</p>
            </div>

            @if (session('status'))
                <x-ui.alert class="mb-6">{{ session('status') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error" class="mb-6">{{ $errors->first() }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <x-ui.input label="New Password" name="password" type="password" icon="fas fa-lock" required />
                <x-ui.input label="Confirm Password" name="password_confirmation" type="password" icon="fas fa-shield-halved" required />

                <x-ui.button type="submit" class="w-full">
                    <i class="fas fa-check"></i>
                    Reset Password
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-ui.page>
@endsection
