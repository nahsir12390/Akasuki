@props([
    'user',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-11 w-11 text-sm',
        'lg' => 'h-20 w-20 text-2xl',
        'xl' => 'h-32 w-32 text-4xl',
    ];

    $class = $sizes[$size] ?? $sizes['md'];
    $src = $user->profile_photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=F97316&color=fff');
@endphp

<img
    src="{{ $src }}"
    alt="{{ $user->name }}"
    {{ $attributes->merge(['class' => "{$class} rounded-full border border-white/80 object-cover shadow-sm ring-1 ring-slate-200 dark:border-slate-800 dark:ring-slate-700"]) }}
>
