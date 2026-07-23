@extends('layouts.app')

@section('title', 'Training Games - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-7xl">
    <section class="mb-6">
        <span class="rank-badge"><i class="fas fa-gamepad"></i> Training Games</span>
        <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Practice with quick developer challenges.</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">This is the games area. We can keep adding more game modes here without mixing them into courses or posts.</p>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <x-ui.card class="p-5 sm:p-6">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-950 dark:text-white">Code Memory</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Match the technology with its role.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-lg bg-green-50 px-3 py-2 text-xs font-black text-green-700 ring-1 ring-green-100 dark:bg-green-950/35 dark:text-green-300 dark:ring-green-900">
                        <i class="fas fa-check mr-1"></i><span data-match-count>0</span>/6 matched
                    </span>
                    <span class="rounded-lg bg-orange-50 px-3 py-2 text-xs font-black text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                        <i class="fas fa-bolt mr-1"></i><span data-attempt-count>0</span> tries
                    </span>
                    <button type="button" data-reset-game class="ui-btn ui-btn-secondary min-h-10 px-3"><i class="fas fa-rotate-right"></i> Reset</button>
                </div>
            </div>

            <div data-win-panel class="mb-5 hidden overflow-hidden rounded-lg border border-green-200 bg-green-50 p-5 text-green-800 shadow-lg shadow-green-500/10 dark:border-green-900 dark:bg-green-950/30 dark:text-green-200">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide">Mission Complete</p>
                        <h3 class="mt-1 text-2xl font-black">You cleared the Code Memory challenge.</h3>
                        <p class="mt-1 text-sm font-semibold">Nice work. Every technology found its correct role.</p>
                    </div>
                    <div class="grid h-16 w-16 place-items-center rounded-lg bg-white text-green-600 shadow-sm dark:bg-slate-900 dark:text-green-300">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                </div>
            </div>

            <div data-game-board class="grid gap-3 sm:grid-cols-3"></div>
        </x-ui.card>

        <x-ui.card class="p-5">
            <h2 class="text-lg font-black text-slate-950 dark:text-white">Game Roadmap</h2>
            <div class="mt-4 space-y-3">
                @foreach([
                    ['title' => 'Code Memory', 'meta' => 'Live now', 'icon' => 'fas fa-brain'],
                    ['title' => 'Syntax Sprint', 'meta' => 'Next game mode', 'icon' => 'fas fa-bolt'],
                    ['title' => 'Debug Challenge', 'meta' => 'Admin-created puzzles later', 'icon' => 'fas fa-bug'],
                ] as $item)
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300"><i class="{{ $item['icon'] }}"></i></span>
                        <span>
                            <span class="block text-sm font-black text-slate-950 dark:text-white">{{ $item['title'] }}</span>
                            <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $item['meta'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>
</x-ui.page>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const board = document.querySelector('[data-game-board]');
    const reset = document.querySelector('[data-reset-game]');
    const matchCount = document.querySelector('[data-match-count]');
    const attemptCount = document.querySelector('[data-attempt-count]');
    const winPanel = document.querySelector('[data-win-panel]');
    if (!board) return;

    const pairs = [
        { items: ['Laravel', 'PHP framework'], tone: 'orange' },
        { items: ['MySQL', 'Database'], tone: 'blue' },
        { items: ['Tailwind', 'Utility CSS'], tone: 'cyan' },
        { items: ['Flutter', 'Mobile UI'], tone: 'sky' },
        { items: ['JavaScript', 'Browser logic'], tone: 'yellow' },
        { items: ['Git', 'Version control'], tone: 'red' },
    ];

    let first = null;
    let locked = false;
    let matches = 0;
    let attempts = 0;

    const baseClass = 'min-h-24 rounded-lg border p-4 text-center text-sm font-black shadow-sm transition duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-400';
    const hiddenClass = 'border-slate-200 bg-white text-slate-700 hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-orange-950/25';
    const wrongClass = 'border-red-300 bg-red-50 text-red-700 ring-2 ring-red-200 dark:border-red-900 dark:bg-red-950/35 dark:text-red-300 dark:ring-red-900';
    const successClass = 'border-green-300 bg-green-50 text-green-800 ring-2 ring-green-200 dark:border-green-900 dark:bg-green-950/35 dark:text-green-200 dark:ring-green-900';
    const tones = {
        orange: 'border-orange-300 bg-orange-50 text-orange-800 ring-2 ring-orange-200 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200 dark:ring-orange-900',
        blue: 'border-blue-300 bg-blue-50 text-blue-800 ring-2 ring-blue-200 dark:border-blue-900 dark:bg-blue-950/35 dark:text-blue-200 dark:ring-blue-900',
        cyan: 'border-cyan-300 bg-cyan-50 text-cyan-800 ring-2 ring-cyan-200 dark:border-cyan-900 dark:bg-cyan-950/35 dark:text-cyan-200 dark:ring-cyan-900',
        sky: 'border-sky-300 bg-sky-50 text-sky-800 ring-2 ring-sky-200 dark:border-sky-900 dark:bg-sky-950/35 dark:text-sky-200 dark:ring-sky-900',
        yellow: 'border-yellow-300 bg-yellow-50 text-yellow-800 ring-2 ring-yellow-200 dark:border-yellow-900 dark:bg-yellow-950/35 dark:text-yellow-200 dark:ring-yellow-900',
        red: 'border-red-300 bg-red-50 text-red-800 ring-2 ring-red-200 dark:border-red-900 dark:bg-red-950/35 dark:text-red-200 dark:ring-red-900',
    };

    function build() {
        first = null;
        locked = false;
        matches = 0;
        attempts = 0;
        updateStats();
        winPanel?.classList.add('hidden');

        const cards = pairs.flatMap((pair, index) => pair.items.map((text) => ({ text, pair: index, tone: pair.tone })))
            .sort(() => Math.random() - 0.5);

        board.innerHTML = '';
        cards.forEach((card) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.pair = card.pair;
            button.dataset.text = card.text;
            button.dataset.tone = card.tone;
            button.className = `${baseClass} ${hiddenClass}`;
            button.textContent = 'Hidden Scroll';
            button.addEventListener('click', () => flip(button));
            board.appendChild(button);
        });
    }

    function flip(button) {
        if (locked || button.dataset.done === 'true' || button === first) return;
        reveal(button);

        if (!first) {
            first = button;
            return;
        }

        attempts += 1;
        updateStats();

        if (first.dataset.pair === button.dataset.pair) {
            first.dataset.done = 'true';
            button.dataset.done = 'true';
            markSuccess(first);
            markSuccess(button);
            first = null;
            matches += 1;
            updateStats();
            checkWin();
            return;
        }

        locked = true;
        markWrong(first);
        markWrong(button);
        setTimeout(() => {
            [first, button].forEach((item) => {
                hide(item);
            });
            first = null;
            locked = false;
        }, 800);
    }

    function reveal(button) {
        button.textContent = button.dataset.text;
        button.className = `${baseClass} ${tones[button.dataset.tone]}`;
    }

    function hide(button) {
        button.textContent = 'Hidden Scroll';
        button.className = `${baseClass} ${hiddenClass}`;
    }

    function markWrong(button) {
        button.className = `${baseClass} ${wrongClass}`;
    }

    function markSuccess(button) {
        button.className = `${baseClass} ${successClass}`;
        button.innerHTML = `<span class="block">${button.dataset.text}</span><span class="mt-2 inline-flex items-center gap-1 text-xs"><i class="fas fa-check"></i> Matched</span>`;
    }

    function updateStats() {
        if (matchCount) matchCount.textContent = matches;
        if (attemptCount) attemptCount.textContent = attempts;
    }

    function checkWin() {
        if (matches !== pairs.length) return;
        winPanel?.classList.remove('hidden');
        winPanel?.animate([
            { transform: 'scale(0.96)', opacity: 0 },
            { transform: 'scale(1)', opacity: 1 },
        ], { duration: 320, easing: 'ease-out' });
    }

    reset?.addEventListener('click', build);
    build();
});
</script>
@endpush
