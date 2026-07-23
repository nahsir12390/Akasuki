@props([
    'padding' => 'p-6',
])

<section {{ $attributes->merge([
    'class' => "ui-card {$padding}",
]) }}>
    {{ $slot }}
</section>
