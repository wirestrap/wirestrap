@props([
    'classTd' => '',
])

<td @if ($classTd) class="{{ $classTd }}" @endif>
    <input
        type="checkbox"
        data-ws-bulk x-bind="wsBulkCheck"
        {{ $attributes->merge(['class' => 'ws-form-check-input']) }}
    />

    {{ $slot }}
</td>
