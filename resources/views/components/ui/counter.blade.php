@props([
    'count' => 0,   /** @var int|float|string Numeric value or JS expression (e.g. "$wire.count") */
    'decimals' => config('wirestrap.counter.decimals', 0),
    'duration' => config('wirestrap.counter.duration', 1000),
])

@php
    $isExpression = !is_numeric($count);
@endphp

<div
    x-data="wsCounter"
    x-cloak
    @if($isExpression)
        x-effect="animate({{ $count }})"
    @else
        data-ws-count="{{ $count }}"
    @endif
    data-ws-decimals="{{ $decimals }}"
    data-ws-duration="{{ $duration }}"
    class="{{ $attributes->get('class', config('wirestrap.counter.class', '')) }}"
    {{
        $attributes->except([
            'x-data',
            'x-init',
            'x-text',
            'x-effect',
            'data-ws-count',
            'data-ws-decimals',
            'data-ws-duration',
            'class',
        ])
    }}
>
    <span wire:ignore x-text="count"></span>
</div>
