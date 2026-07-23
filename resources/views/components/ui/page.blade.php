@props([
    'width' => 'max-w-7xl',
    'flush' => false,
])

<div {{ $attributes->merge([
    'class' => 'ui-page relative min-h-screen bg-slate-50 text-slate-950 dark:bg-slate-950 dark:text-slate-50',
]) }}>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(249,115,22,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(249,115,22,0.04)_1px,transparent_1px)] bg-[size:44px_44px] dark:bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)]"></div>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
    </div>

    <div class="relative {{ $flush ? '' : 'px-4 py-8 sm:px-6 lg:px-8' }}">
        <div class="{{ $width }} mx-auto">
            {{ $slot }}
        </div>
    </div>
</div>
