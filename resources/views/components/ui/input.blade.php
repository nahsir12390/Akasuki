@props([
    'label',
    'name',
    'type' => 'text',
    'icon' => null,
    'value' => null,
])

@php($id = $attributes->get('id', $name))

<div class="space-y-2">
    <label for="{{ $id }}" class="ui-label">
        @if($icon)
            <i class="{{ $icon }} text-orange-500"></i>
        @endif
        <span>{{ $label }}</span>
    </label>
    <div class="relative">
        @if($icon)
            <i class="{{ $icon }} pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        @endif
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->class([
                'ui-input',
                'pl-10' => $icon,
            ]) }}
        >
    </div>
    @error($name)
        <p class="ui-field-error">{{ $message }}</p>
    @enderror
</div>
