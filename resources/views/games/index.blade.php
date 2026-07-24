@extends('layouts.app')

@section('title', 'Training Games - ' . config('app.name'))

@section('content')
<x-ui.page width="max-w-7xl">
    <section class="mb-6">
        <span class="rank-badge"><i class="fas fa-gamepad"></i> Training Games</span>
        <div class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-normal text-slate-950 dark:text-white">Developer training arcade.</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Quick challenges for memory, focus, syntax, and debugging. Every mode is built to work well on mobile and desktop.</p>
            </div>
            <div class="grid grid-cols-3 gap-2 rounded-lg border border-orange-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-950 sm:flex">
                <button type="button" data-game-tab="memory" class="game-tab is-active"><i class="fas fa-brain"></i><span>Memory</span></button>
                <button type="button" data-game-tab="sequence" class="game-tab"><i class="fas fa-bolt"></i><span>Sequence</span></button>
                <button type="button" data-game-tab="syntax" class="game-tab"><i class="fas fa-code"></i><span>Syntax</span></button>
                <button type="button" data-game-tab="debug" class="game-tab"><i class="fas fa-bug"></i><span>Debug</span></button>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <section class="min-w-0">
            <x-ui.card data-game-panel="memory" class="game-panel p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950 dark:text-white">Code Memory</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Match each technology with its role.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="game-stat"><i class="fas fa-check text-green-500"></i><span data-memory-matches>0</span>/6 matched</span>
                        <span class="game-stat"><i class="fas fa-bolt text-orange-500"></i><span data-memory-attempts>0</span> tries</span>
                        <button type="button" data-memory-reset class="ui-btn ui-btn-secondary min-h-10 px-3"><i class="fas fa-rotate-right"></i> Reset</button>
                    </div>
                </div>
                <div data-memory-win class="game-win hidden">
                    <p class="text-xs font-black uppercase tracking-wide">Mission Complete</p>
                    <h3 class="mt-1 text-xl font-black">Every scroll matched.</h3>
                </div>
                <div data-memory-board class="grid gap-3 sm:grid-cols-3"></div>
            </x-ui.card>

            <x-ui.card data-game-panel="sequence" class="game-panel hidden p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950 dark:text-white">Chakra Sequence</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Watch the color pattern, then repeat it.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="game-stat"><i class="fas fa-layer-group text-orange-500"></i>Level <span data-sequence-level>1</span></span>
                        <span class="game-stat"><i class="fas fa-heart text-red-500"></i><span data-sequence-lives>3</span> lives</span>
                        <button type="button" data-sequence-start class="ui-btn ui-btn-primary min-h-10 px-3"><i class="fas fa-play"></i> Start</button>
                    </div>
                </div>
                <div data-sequence-message class="mb-4 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-bold text-orange-800 dark:border-orange-900 dark:bg-orange-950/30 dark:text-orange-200">Press start and follow the flashes.</div>
                <div data-sequence-win class="game-win hidden">
                    <p class="text-xs font-black uppercase tracking-wide">Focus Mastered</p>
                    <h3 class="mt-1 text-xl font-black">You completed the color sequence.</h3>
                </div>
                <div class="mx-auto grid max-w-md grid-cols-2 gap-3">
                    <button type="button" data-sequence-pad="0" class="sequence-pad bg-red-500" aria-label="Red pad"></button>
                    <button type="button" data-sequence-pad="1" class="sequence-pad bg-blue-500" aria-label="Blue pad"></button>
                    <button type="button" data-sequence-pad="2" class="sequence-pad bg-green-500" aria-label="Green pad"></button>
                    <button type="button" data-sequence-pad="3" class="sequence-pad bg-yellow-400" aria-label="Yellow pad"></button>
                </div>
            </x-ui.card>

            <x-ui.card data-game-panel="syntax" class="game-panel hidden p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950 dark:text-white">Syntax Sprint</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pick the correct answer before moving to the next prompt.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="game-stat"><i class="fas fa-star text-yellow-500"></i><span data-syntax-score>0</span> score</span>
                        <span class="game-stat"><i class="fas fa-fire text-orange-500"></i><span data-syntax-streak>0</span> streak</span>
                        <button type="button" data-syntax-reset class="ui-btn ui-btn-secondary min-h-10 px-3"><i class="fas fa-rotate-right"></i> Reset</button>
                    </div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-950 p-4 text-slate-100 shadow-sm dark:border-slate-800">
                    <p class="text-xs font-black uppercase tracking-wide text-orange-300">Question <span data-syntax-position>1</span>/6</p>
                    <pre data-syntax-question class="mt-3 overflow-x-auto whitespace-pre-wrap text-sm leading-6"></pre>
                </div>
                <div data-syntax-options class="mt-4 grid gap-3 sm:grid-cols-2"></div>
                <div data-syntax-feedback class="mt-4 hidden rounded-lg border px-4 py-3 text-sm font-bold"></div>
            </x-ui.card>

            <x-ui.card data-game-panel="debug" class="game-panel hidden p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950 dark:text-white">Debug Hunt</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Read the snippet and identify the bug.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="game-stat"><i class="fas fa-bug text-red-500"></i><span data-debug-fixed>0</span>/5 fixed</span>
                        <button type="button" data-debug-reset class="ui-btn ui-btn-secondary min-h-10 px-3"><i class="fas fa-rotate-right"></i> Reset</button>
                    </div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
                    <p data-debug-title class="text-sm font-black text-slate-950 dark:text-white"></p>
                    <pre data-debug-code class="mt-3 overflow-x-auto rounded-lg bg-slate-950 p-4 text-sm leading-6 text-slate-100"></pre>
                </div>
                <div data-debug-options class="mt-4 grid gap-3"></div>
                <div data-debug-feedback class="mt-4 hidden rounded-lg border px-4 py-3 text-sm font-bold"></div>
            </x-ui.card>
        </section>

        <aside class="space-y-4">
            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Game Modes</h2>
                <div class="mt-4 space-y-3">
                    @foreach([
                        ['title' => 'Code Memory', 'meta' => 'Match pairs and train recall', 'icon' => 'fas fa-brain'],
                        ['title' => 'Chakra Sequence', 'meta' => 'Colors show the path to repeat', 'icon' => 'fas fa-bolt'],
                        ['title' => 'Syntax Sprint', 'meta' => 'Fast multiple-choice coding prompts', 'icon' => 'fas fa-code'],
                        ['title' => 'Debug Hunt', 'meta' => 'Find the broken line or concept', 'icon' => 'fas fa-bug'],
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

            <x-ui.card class="p-5">
                <h2 class="text-lg font-black text-slate-950 dark:text-white">Progress Tips</h2>
                <div class="mt-4 grid gap-3 text-sm font-semibold leading-6 text-slate-600 dark:text-slate-400">
                    <p>Use sequence for focus, syntax for speed, memory for recall, and debug hunt for careful reading.</p>
                    <p>Scores reset on reload for now. Later we can store game results in the database and show leaderboards.</p>
                </div>
            </x-ui.card>
        </aside>
    </div>
</x-ui.page>
@endsection

@push('styles')
<style>
    .game-tab {
        display: inline-flex;
        min-height: 2.75rem;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border-radius: .5rem;
        padding: 0 .85rem;
        font-size: .8rem;
        font-weight: 900;
        color: rgb(71 85 105);
        transition: .2s ease;
    }
    .game-tab.is-active {
        background: linear-gradient(135deg, #f97316, #dc2626);
        color: white;
        box-shadow: 0 12px 30px rgba(249, 115, 22, .22);
    }
    .game-stat {
        display: inline-flex;
        min-height: 2.5rem;
        align-items: center;
        gap: .5rem;
        border-radius: .5rem;
        border: 1px solid rgb(226 232 240);
        background: white;
        padding: 0 .75rem;
        font-size: .75rem;
        font-weight: 900;
        color: rgb(51 65 85);
    }
    .dark .game-stat {
        border-color: rgb(30 41 59);
        background: rgb(2 6 23);
        color: rgb(226 232 240);
    }
    .game-win {
        margin-bottom: 1rem;
        border-radius: .5rem;
        border: 1px solid rgb(187 247 208);
        background: rgb(240 253 244);
        padding: 1rem;
        color: rgb(22 101 52);
    }
    .dark .game-win {
        border-color: rgb(20 83 45);
        background: rgba(20, 83, 45, .24);
        color: rgb(187 247 208);
    }
    .sequence-pad {
        aspect-ratio: 1 / 1;
        border-radius: .65rem;
        border: 4px solid rgba(255, 255, 255, .85);
        box-shadow: inset 0 -12px 0 rgba(0, 0, 0, .12), 0 18px 40px rgba(15, 23, 42, .16);
        transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
    }
    .sequence-pad.is-lit {
        filter: brightness(1.35) saturate(1.2);
        transform: scale(.96);
        box-shadow: 0 0 0 5px rgba(249, 115, 22, .2), 0 20px 50px rgba(249, 115, 22, .28);
    }
</style>
@endpush

@push('scripts')
<script>
@verbatim
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[data-game-tab]');
    const panels = document.querySelectorAll('[data-game-panel]');
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
            panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.gamePanel !== tab.dataset.gameTab));
        });
    });

    const buttonClass = 'min-h-24 rounded-lg border p-4 text-center text-sm font-black shadow-sm transition duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-400';
    const hiddenClass = 'border-slate-200 bg-white text-slate-700 hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-orange-950/25';
    const successClass = 'border-green-300 bg-green-50 text-green-800 ring-2 ring-green-200 dark:border-green-900 dark:bg-green-950/35 dark:text-green-200 dark:ring-green-900';
    const wrongClass = 'border-red-300 bg-red-50 text-red-800 ring-2 ring-red-200 dark:border-red-900 dark:bg-red-950/35 dark:text-red-200 dark:ring-red-900';

    function shuffle(items) {
        return [...items].sort(() => Math.random() - 0.5);
    }

    const memoryPairs = [
        ['Laravel', 'PHP framework'],
        ['PostgreSQL', 'Database'],
        ['Tailwind', 'Utility CSS'],
        ['PWA', 'Installable web app'],
        ['Reverb', 'Realtime WebSocket'],
        ['Git', 'Version control'],
    ];
    const memoryBoard = document.querySelector('[data-memory-board]');
    let memoryFirst = null;
    let memoryLocked = false;
    let memoryMatches = 0;
    let memoryAttempts = 0;

    function buildMemory() {
        memoryFirst = null;
        memoryLocked = false;
        memoryMatches = 0;
        memoryAttempts = 0;
        document.querySelector('[data-memory-win]')?.classList.add('hidden');
        document.querySelector('[data-memory-matches]').textContent = '0';
        document.querySelector('[data-memory-attempts]').textContent = '0';
        memoryBoard.innerHTML = '';
        shuffle(memoryPairs.flatMap((pair, index) => pair.map((text) => ({ text, pair: index })))).forEach((card) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `${buttonClass} ${hiddenClass}`;
            button.textContent = 'Hidden Scroll';
            button.dataset.text = card.text;
            button.dataset.pair = card.pair;
            button.addEventListener('click', () => flipMemory(button));
            memoryBoard.appendChild(button);
        });
    }

    function flipMemory(button) {
        if (memoryLocked || button.dataset.done === 'true' || button === memoryFirst) return;
        button.textContent = button.dataset.text;
        button.className = `${buttonClass} border-orange-300 bg-orange-50 text-orange-800 ring-2 ring-orange-200 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200`;
        if (!memoryFirst) {
            memoryFirst = button;
            return;
        }
        memoryAttempts += 1;
        document.querySelector('[data-memory-attempts]').textContent = memoryAttempts;
        if (memoryFirst.dataset.pair === button.dataset.pair) {
            [memoryFirst, button].forEach((item) => {
                item.dataset.done = 'true';
                item.className = `${buttonClass} ${successClass}`;
                item.innerHTML = `${item.dataset.text}<span class="mt-2 block text-xs"><i class="fas fa-check"></i> Matched</span>`;
            });
            memoryFirst = null;
            memoryMatches += 1;
            document.querySelector('[data-memory-matches]').textContent = memoryMatches;
            if (memoryMatches === memoryPairs.length) document.querySelector('[data-memory-win]')?.classList.remove('hidden');
            return;
        }
        memoryLocked = true;
        [memoryFirst, button].forEach((item) => item.className = `${buttonClass} ${wrongClass}`);
        window.setTimeout(() => {
            [memoryFirst, button].forEach((item) => {
                item.textContent = 'Hidden Scroll';
                item.className = `${buttonClass} ${hiddenClass}`;
            });
            memoryFirst = null;
            memoryLocked = false;
        }, 700);
    }

    document.querySelector('[data-memory-reset]')?.addEventListener('click', buildMemory);
    buildMemory();

    const sequencePads = [...document.querySelectorAll('[data-sequence-pad]')];
    let sequence = [];
    let sequenceInput = [];
    let sequencePlaying = false;
    let sequenceLives = 3;
    const sequenceMessage = document.querySelector('[data-sequence-message]');

    function sequenceStatus(text, tone = 'orange') {
        sequenceMessage.textContent = text;
        sequenceMessage.className = `mb-4 rounded-lg border px-4 py-3 text-sm font-bold ${tone === 'red' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200' : tone === 'green' ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950/30 dark:text-green-200' : 'border-orange-200 bg-orange-50 text-orange-800 dark:border-orange-900 dark:bg-orange-950/30 dark:text-orange-200'}`;
    }

    function resetSequence() {
        sequence = [];
        sequenceInput = [];
        sequenceLives = 3;
        document.querySelector('[data-sequence-level]').textContent = '1';
        document.querySelector('[data-sequence-lives]').textContent = sequenceLives;
        document.querySelector('[data-sequence-win]')?.classList.add('hidden');
        sequenceStatus('Watch the colors, then repeat them.');
        addSequenceStep();
    }

    function addSequenceStep() {
        sequence.push(Math.floor(Math.random() * sequencePads.length));
        document.querySelector('[data-sequence-level]').textContent = sequence.length;
        playSequence();
    }

    async function playSequence() {
        sequencePlaying = true;
        sequenceInput = [];
        for (const index of sequence) {
            await new Promise((resolve) => setTimeout(resolve, 280));
            sequencePads[index].classList.add('is-lit');
            await new Promise((resolve) => setTimeout(resolve, 420));
            sequencePads[index].classList.remove('is-lit');
        }
        sequencePlaying = false;
        sequenceStatus('Your turn. Repeat the pattern.');
    }

    sequencePads.forEach((pad, index) => {
        pad.addEventListener('click', () => {
            if (sequencePlaying || !sequence.length) return;
            pad.classList.add('is-lit');
            window.setTimeout(() => pad.classList.remove('is-lit'), 180);
            sequenceInput.push(index);
            const expected = sequence[sequenceInput.length - 1];
            if (index !== expected) {
                sequenceLives -= 1;
                document.querySelector('[data-sequence-lives]').textContent = sequenceLives;
                if (sequenceLives <= 0) {
                    sequenceStatus('Mission failed. Start again and rebuild focus.', 'red');
                    sequence = [];
                    return;
                }
                sequenceStatus('Wrong color. Watch the pattern again.', 'red');
                window.setTimeout(playSequence, 700);
                return;
            }
            if (sequenceInput.length === sequence.length) {
                if (sequence.length >= 8) {
                    document.querySelector('[data-sequence-win]')?.classList.remove('hidden');
                    sequenceStatus('You mastered the full sequence.', 'green');
                    return;
                }
                sequenceStatus('Correct. Next level.', 'green');
                window.setTimeout(addSequenceStep, 650);
            }
        });
    });
    document.querySelector('[data-sequence-start]')?.addEventListener('click', resetSequence);

    const syntaxQuestions = [
        { q: 'Which selector targets an element with id="app"?', options: ['#app', '.app', 'app', '*app'], answer: '#app' },
        { q: 'Which method creates a new array without changing the original?', options: ['map()', 'push()', 'splice()', 'sort()'], answer: 'map()' },
        { q: 'Which Laravel command runs migrations?', options: ['php artisan migrate', 'php artisan make:model', 'php artisan serve', 'php artisan route:list'], answer: 'php artisan migrate' },
        { q: 'Which HTTP status means unauthorized?', options: ['401', '200', '302', '500'], answer: '401' },
        { q: 'Which SQL clause filters rows?', options: ['WHERE', 'ORDER BY', 'GROUP BY', 'LIMIT'], answer: 'WHERE' },
        { q: 'Which attribute improves image accessibility?', options: ['alt', 'src', 'href', 'target'], answer: 'alt' },
    ];
    let syntaxIndex = 0;
    let syntaxScore = 0;
    let syntaxStreak = 0;

    function renderSyntax() {
        const current = syntaxQuestions[syntaxIndex];
        document.querySelector('[data-syntax-position]').textContent = syntaxIndex + 1;
        document.querySelector('[data-syntax-question]').textContent = current.q;
        document.querySelector('[data-syntax-options]').innerHTML = shuffle(current.options).map((option) => `<button type="button" class="syntax-option min-h-14 rounded-lg border border-slate-200 bg-white px-4 text-left text-sm font-black text-slate-700 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">${option}</button>`).join('');
        document.querySelectorAll('.syntax-option').forEach((button) => button.addEventListener('click', () => answerSyntax(button.textContent)));
    }

    function answerSyntax(answer) {
        const current = syntaxQuestions[syntaxIndex];
        const feedback = document.querySelector('[data-syntax-feedback]');
        const correct = answer === current.answer;
        syntaxScore += correct ? 10 : 0;
        syntaxStreak = correct ? syntaxStreak + 1 : 0;
        document.querySelector('[data-syntax-score]').textContent = syntaxScore;
        document.querySelector('[data-syntax-streak]').textContent = syntaxStreak;
        feedback.classList.remove('hidden');
        feedback.className = `mt-4 rounded-lg border px-4 py-3 text-sm font-bold ${correct ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950/30 dark:text-green-200' : 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200'}`;
        feedback.textContent = correct ? 'Correct. Keep moving.' : `Not quite. Correct answer: ${current.answer}`;
        syntaxIndex = (syntaxIndex + 1) % syntaxQuestions.length;
        window.setTimeout(() => {
            feedback.classList.add('hidden');
            renderSyntax();
        }, 900);
    }

    document.querySelector('[data-syntax-reset]')?.addEventListener('click', () => {
        syntaxIndex = 0;
        syntaxScore = 0;
        syntaxStreak = 0;
        document.querySelector('[data-syntax-score]').textContent = '0';
        document.querySelector('[data-syntax-streak]').textContent = '0';
        renderSyntax();
    });
    renderSyntax();

    const debugChallenges = [
        { title: 'Missing comparison', code: 'if ($user->id = $post->user_id) {\n    return true;\n}', answer: 'Single equals assigns instead of comparing.', options: ['Single equals assigns instead of comparing.', 'The variable names are too short.', 'The return should be false.', 'Curly braces are not allowed.'] },
        { title: 'Unsafe output', code: '<p>{!! $comment->body !!}</p>', answer: 'Raw output can allow unsafe HTML.', options: ['Raw output can allow unsafe HTML.', 'Paragraph tags cannot contain variables.', 'Blade cannot render comments.', 'The body field must be an integer.'] },
        { title: 'Wrong method', code: 'Route::get("/logout", [AuthController::class, "logout"]);', answer: 'Logout should use POST to avoid accidental requests.', options: ['Logout should use POST to avoid accidental requests.', 'The route path must be uppercase.', 'Controllers cannot be arrays.', 'GET routes are always faster.'] },
        { title: 'N plus one', code: '@foreach($posts as $post)\n    {{ $post->user->name }}\n@endforeach', answer: 'Load users with the posts before looping.', options: ['Load users with the posts before looping.', 'Loops are not valid Blade.', 'User names cannot be displayed.', 'The foreach needs a semicolon.'] },
        { title: 'Missing validation', code: '$user->update($request->all());', answer: 'Only validated fields should be updated.', options: ['Only validated fields should be updated.', 'Update cannot receive arrays.', 'Requests cannot be used in controllers.', 'The model must be deleted first.'] },
    ];
    let debugIndex = 0;
    let debugFixed = 0;

    function renderDebug() {
        const current = debugChallenges[debugIndex];
        document.querySelector('[data-debug-title]').textContent = current.title;
        document.querySelector('[data-debug-code]').textContent = current.code;
        document.querySelector('[data-debug-options]').innerHTML = shuffle(current.options).map((option) => `<button type="button" class="debug-option min-h-12 rounded-lg border border-slate-200 bg-white px-4 text-left text-sm font-black text-slate-700 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">${option}</button>`).join('');
        document.querySelectorAll('.debug-option').forEach((button) => button.addEventListener('click', () => answerDebug(button.textContent)));
    }

    function answerDebug(answer) {
        const current = debugChallenges[debugIndex];
        const feedback = document.querySelector('[data-debug-feedback]');
        const correct = answer === current.answer;
        if (correct) debugFixed = Math.min(debugFixed + 1, debugChallenges.length);
        document.querySelector('[data-debug-fixed]').textContent = debugFixed;
        feedback.classList.remove('hidden');
        feedback.className = `mt-4 rounded-lg border px-4 py-3 text-sm font-bold ${correct ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-900 dark:bg-green-950/30 dark:text-green-200' : 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200'}`;
        feedback.textContent = correct ? 'Bug fixed.' : `Check again. The issue is: ${current.answer}`;
        debugIndex = (debugIndex + 1) % debugChallenges.length;
        window.setTimeout(() => {
            feedback.classList.add('hidden');
            renderDebug();
        }, 1000);
    }

    document.querySelector('[data-debug-reset]')?.addEventListener('click', () => {
        debugIndex = 0;
        debugFixed = 0;
        document.querySelector('[data-debug-fixed]').textContent = '0';
        renderDebug();
    });
    renderDebug();
});
@endverbatim
</script>
@endpush
