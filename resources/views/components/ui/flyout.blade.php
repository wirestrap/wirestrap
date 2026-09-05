@props([
    'id' => null,
    'content' => '',    /** @var \Illuminate\View\ComponentSlot|string */
    'placement' => config('wirestrap.flyout.placement', 'bottom-start'),
    'teleport' => config('wirestrap.flyout.teleport', null),
    'trigger' => config('wirestrap.flyout.trigger', 'hover'),
    'offsetSkidding' => config('wirestrap.flyout.offset_skidding', 0),
    'offsetDistance' => config('wirestrap.flyout.offset_distance', 0),
    'position' => config('wirestrap.flyout.position', 'absolute'),
    'interactive' => config('wirestrap.flyout.interactive', true),
])

@php
    $isContentSlot = $content instanceof \Illuminate\View\ComponentSlot;
@endphp

<div
    data-ws-float-id="{{ $id }}"
    @if($id) id="{{ $id }}" @endif
    data-ws-placement="{{ $placement }}"
    data-ws-trigger="{{ $trigger }}"
    data-ws-offset-skidding="{{ $offsetSkidding }}"
    data-ws-offset-distance="{{ $offsetDistance }}"
    data-ws-position="{{ $position }}"
    @if($interactive) data-ws-interactive @endif
    {{
        $attributes->except([
            'id',
            'data-ws-float-id',
            'data-ws-placement',
            'data-ws-trigger',
            'data-ws-offset-skidding',
            'data-ws-offset-distance',
            'data-ws-position',
            'data-ws-anchor',
            'data-ws-float-trigger',
            'data-ws-interactive',
        ])
    }}
>
    <div data-ws-float-trigger>
        {{ $slot }}
    </div>

    @if ($teleport) @teleport($teleport) @endif
        <div
            @if($teleport) data-ws-teleported @endif
            data-ws-floatable
            class="ws-flyout {{ config('wirestrap.flyout.class', '') }}{{ $isContentSlot ? ' ' . $content->attributes->get('class') : '' }}"
            {{ $isContentSlot ? $content->attributes->except(['x-bind', 'class', 'data-ws-floatable', 'style']) : '' }}
            style="display: none;{{ $isContentSlot ? $content->attributes->get('style') : '' }}"
            wire:ignore.self
        >{{ $content }}</div>
    @if ($teleport) @endteleport @endif
</div>
