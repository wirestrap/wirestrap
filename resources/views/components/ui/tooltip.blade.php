@props([
    'id' => null,
    'content' => '',    /** @var \Illuminate\View\ComponentSlot|string */
    'placement' => config('wirestrap.tooltip.placement', 'top'),
    'teleport' => config('wirestrap.tooltip.teleport', null),
    'trigger' => config('wirestrap.tooltip.trigger', 'hover'),
    'offsetSkidding' => config('wirestrap.tooltip.offset_skidding', 0),
    'offsetDistance' => config('wirestrap.tooltip.offset_distance', 8),
    'position' => config('wirestrap.tooltip.position', 'absolute'),
    'arrow' => config('wirestrap.tooltip.arrow', true),
    'interactive' => config('wirestrap.tooltip.interactive', false),
])

@php
    if ($teleport && !$id) {
        throw new \Wirestrap\Exceptions\MissingComponentIdException('tooltip');
    }

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
    <div
        data-ws-float-trigger
        @if($id) aria-describedby="{{ $id }}-tip" @endif
    >
        {{ $slot }}
    </div>

    @if ($teleport) @teleport($teleport) @endif
        <div
            @if($teleport) data-ws-float-for="{{ $id }}" @endif
            @if($id) id="{{ $id }}-tip" @endif
            data-ws-floatable
            role="tooltip"
            class="ws-tooltip {{ config('wirestrap.tooltip.class', '') }}{{ $isContentSlot ? ' ' . $content->attributes->get('class') : '' }}"
            {{ $isContentSlot ? $content->attributes->except(['x-bind', 'class', 'data-ws-floatable', 'style']) : '' }}
            style="display: none;{{ $isContentSlot ? $content->attributes->get('style') : '' }}"
            wire:ignore.self
        >
            @if ($arrow) <div data-ws-arrow class="ws-tooltip-arrow"></div> @endif
            <div class="ws-tooltip-content">{{ $content }}</div>
        </div>
    @if ($teleport) @endteleport @endif
</div>
