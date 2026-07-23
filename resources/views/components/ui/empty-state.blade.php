@props([
    'icon' => 'fas fa-scroll',
    'title',
    'description' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'ui-empty-state']) }}>
    <div>
        <div class="ui-empty-icon mx-auto">
            <i class="{{ $icon }} text-2xl"></i>
        </div>

        <h2 class="mt-5 text-2xl font-black tracking-normal text-slate-950 dark:text-white">{{ $title }}</h2>

        @if($description)
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>
        @endif

        @if($action)
            <div class="mt-5">
                {{ $action }}
            </div>
        @endif
    </div>
</div>
