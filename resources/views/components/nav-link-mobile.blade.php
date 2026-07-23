@props(['active' => false, 'href'])

@php
    $classes = $active 
        ? 'block px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 transition'
        : 'block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-white hover:bg-blue-600 dark:hover:bg-blue-600 transition';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>