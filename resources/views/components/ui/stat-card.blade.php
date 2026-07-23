@props([
    'label',
    'value',
    'icon' => 'fas fa-chart-line',
    'meta' => null,
])

<x-ui.card padding="p-5" {{ $attributes }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $value }}</p>
            @if($meta)
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $meta }}</p>
            @endif
        </div>
        <div class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900/60">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</x-ui.card>
