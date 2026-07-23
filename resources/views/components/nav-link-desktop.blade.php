@props(['active' => false, 'href'])

@php
    $classes = $active 
        ? 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-white bg-blue-600 transition'
        : 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>