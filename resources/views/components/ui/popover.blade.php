@props([
    'id' => null,
    'header' => '',     /** @var \Illuminate\View\ComponentSlot|string */
    'content' => '',    /** @var \Illuminate\View\ComponentSlot|string */
    'placement' => config('wirestrap.popover.placement', 'top'),
    'appendBody' => config('wirestrap.popover.append_body', false),
    'trigger' => config('wirestrap.popover.trigger', 'hover'),
    'offsetSkidding' => config('wirestrap.popover.offset_skidding', 0),
    'offsetDistance' => config('wirestrap.popover.offset_distance', 8),
    'position' => config('wirestrap.popover.position', 'absolute'),
    'arrow' => config('wirestrap.popover.arrow', true),
])

@php
    if ($appendBody && !$id) {
        throw new \Wirestrap\Exceptions\MissingComponentIdException('popover');
    }
    $isHeaderSlot = $header instanceof \Illuminate\View\ComponentSlot;
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
        ])
    }}
>
    <div data-ws-float-trigger>
        {{ $slot }}
    </div>

    @if ($appendBody) @teleport('body') @endif
        <div
            @if($appendBody) data-ws-float-for="{{ $id }}" @endif
            data-ws-floatable
            class="ws-popover {{ config('wirestrap.popover.class', '') }}"
            style="display: none"
            wire:ignore.self
        >
            @if ($arrow)
                <div data-ws-arrow class="ws-popover-arrow"></div>
            @endif

            <div
                class="ws-popover-header{{ $isHeaderSlot ? ' ' . $header->attributes->get('class') : '' }}"
                {{ $isHeaderSlot ? $header->attributes->except(['class']) : '' }}
            >{{ $header }}</div>

            <div
                class="ws-popover-body{{ $isContentSlot ? ' ' . $content->attributes->get('class') : '' }}"
                {{ $isContentSlot ? $content->attributes->except(['class']) : '' }}
            >{{ $content }}</div>
        </div>
    @if ($appendBody) @endteleport @endif
</div>
