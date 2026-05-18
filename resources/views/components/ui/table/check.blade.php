@props([
    'classTd' => '',
    'disabled' => false,
])

<td @if ($classTd) class="{{ $classTd }}" @endif>
    <input
        type="checkbox"
        data-ws-bulk x-bind="wsBulkCheck"
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'ws-form-check-input']) }}
    />

    {{ $slot }}
</td>
