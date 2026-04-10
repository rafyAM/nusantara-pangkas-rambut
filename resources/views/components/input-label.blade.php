@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm tracking-wide text-gray-300 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
