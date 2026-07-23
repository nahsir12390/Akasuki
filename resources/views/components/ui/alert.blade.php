@props([
    'type' => 'success',
])

@php
    $classes = match ($type) {
        'error' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/70 dark:bg-red-950/50 dark:text-red-200',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/70 dark:bg-amber-950/50 dark:text-amber-100',
        default => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/70 dark:bg-emerald-950/50 dark:text-emerald-200',
    };

    $icon = match ($type) {
        'error' => 'fas fa-circle-exclamation',
        'warning' => 'fas fa-triangle-exclamation',
        default => 'fas fa-circle-check',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex gap-3 rounded-lg border px-4 py-3 text-sm {$classes}"]) }}>
    <i class="{{ $icon }} mt-0.5"></i>
    <div>{{ $slot }}</div>
</div>
