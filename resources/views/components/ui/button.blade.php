@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $classes = match ($variant) {
        'secondary' => 'ui-btn ui-btn-secondary',
        'ghost' => 'ui-btn ui-btn-ghost',
        'danger' => 'ui-btn ui-btn-danger',
        default => 'ui-btn ui-btn-primary',
    };
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
