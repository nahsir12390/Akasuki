@extends('layouts.app')

@section('title', 'Contact Akatsuki Devs - Get In Touch')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
    @endif

    @if ($errors->any())
        <x-ui.alert type="error" class="mb-5">{{ $errors->first() }}</x-ui.alert>
    @endif

    <section class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
        <div>
            <span class="rank-badge"><i class="fas fa-envelope"></i> Contact The Village</span>
            <h1 class="mt-5 text-4xl font-black leading-tight tracking-normal text-slate-950 dark:text-white sm:text-5xl">Send a message scroll.</h1>
            <p class="mt-4 max-w-xl text-base leading-8 text-slate-600 dark:text-slate-300">
                Questions, support, feedback, collaboration, or course ideas. Send it here and the message will go directly to the Akatsuki Devs inbox.
            </p>

            <div class="mt-7 grid gap-3">
                @foreach([
                    ['title' => 'General Email', 'value' => 'contact@akatsukidevs.com', 'icon' => 'fas fa-envelope'],
                    ['title' => 'Support', 'value' => 'support@akatsukidevs.com', 'icon' => 'fas fa-shield-halved'],
                    ['title' => 'Community', 'value' => auth()->check() ? 'Open the Village feed' : 'Create an account to join', 'icon' => 'fas fa-user-group'],
                ] as $item)
                    <x-ui.card class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                                <i class="{{ $item['icon'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-950 dark:text-white">{{ $item['title'] }}</p>
                                <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $item['value'] }}</p>
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </div>

        <x-ui.card class="p-5 sm:p-6">
            <div class="mb-6">
                <div class="mb-4 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
                <h2 class="text-2xl font-black tracking-normal text-slate-950 dark:text-white">Message Form</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Keep it clear. I will respond as soon as possible.</p>
            </div>

            <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input label="Your Name" name="name" icon="fas fa-user" :value="old('name')" required />
                    <x-ui.input label="Email Address" name="email" type="email" icon="fas fa-envelope" :value="old('email')" required />
                </div>

                <div>
                    <label for="subject" class="ui-label mb-2">
                        <i class="fas fa-tag text-orange-500"></i>
                        <span>Subject</span>
                    </label>
                    <select id="subject" name="subject" class="ui-input" required>
                        <option value="">Choose a topic</option>
                        @foreach(['Technical Support', 'Course Question', 'Partnership', 'Feedback', 'Other'] as $subject)
                            <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="message" class="ui-label mb-2">
                        <i class="fas fa-comment text-orange-500"></i>
                        <span>Message</span>
                    </label>
                    <textarea id="message" name="message" rows="6" class="ui-input resize-y" required placeholder="How can I help?">{{ old('message') }}</textarea>
                </div>

                <x-ui.button type="submit" class="w-full">
                    <i class="fas fa-paper-plane"></i>
                    Send Message
                </x-ui.button>
            </form>
        </x-ui.card>
    </section>

    <section class="mt-10 grid gap-4 md:grid-cols-3">
        @foreach([
            ['title' => 'Course Ideas', 'copy' => 'Suggest new learning paths that should appear in the admin-managed course system.', 'icon' => 'fas fa-graduation-cap'],
            ['title' => 'Bug Reports', 'copy' => 'Tell me what broke, where it happened, and what you expected instead.', 'icon' => 'fas fa-bug'],
            ['title' => 'Collaboration', 'copy' => 'Reach out for partnerships, community work, or developer education projects.', 'icon' => 'fas fa-handshake'],
        ] as $card)
            <x-ui.card class="p-6">
                <div class="mb-4 grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <h3 class="font-black text-slate-950 dark:text-white">{{ $card['title'] }}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $card['copy'] }}</p>
            </x-ui.card>
        @endforeach
    </section>
</x-ui.page>
@endsection
