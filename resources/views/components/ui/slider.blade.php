@props([
    'id' => null,
    'columns' => config('wirestrap.slider.columns', 3),
    'columnsSm' => config('wirestrap.slider.columns_sm', null),
    'columnsMd' => config('wirestrap.slider.columns_md', null),
    'columnsLg' => config('wirestrap.slider.columns_lg', null),
    'columnsXl' => config('wirestrap.slider.columns_xl', null),
    'columnsXxl' => config('wirestrap.slider.columns_xxl', null),
    'scrollBy' => config('wirestrap.slider.scroll_by', 1),
    'scrollPage' => config('wirestrap.slider.scroll_page', false),
    'arrows' => config('wirestrap.slider.arrows', true),
    'events' => config('wirestrap.slider.events', false),
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'slider');

    $vars = get_defined_vars();
    $items = [];

    foreach ($vars as $var => $value) {
        if ($var !== 'slot' && $value instanceof \Illuminate\View\ComponentSlot) {
            $items[$var] = $value;
        }
    }
@endphp

<div
    x-data="wsSlider"
    wire:key="ws-slider-{{ $id }}"
    data-ws-scroll-by="{{ $scrollBy }}"
    data-ws-scroll-page="{{ $scrollPage ? 'true' : 'false' }}"
    data-ws-events="{{ $events ? 'true' : 'false' }}"
    id="{{ $id }}"
    class="ws-slider {{ $attributes->get('class', config('wirestrap.slider.class', '')) }}"
    {{ $attributes->except(['class']) }}
>

    @if ($arrows)
        <button
            type="button"
            class="ws-slider-arrow ws-slider-arrow-prev"
            x-bind="prevArrow"
            aria-label="{{ __('Previous') }}"
        ></button>
    @endif

    <div class="ws-slider-track">
        <div class="ws-slider-inner" x-ref="inner" wire:ignore.self>
            @foreach ($items as $key => $item)
                <div wire:key="ws-slider-{{ $id }}-{{ $key }}" class="ws-slider-slide">
                    {{ $item }}
                </div>
            @endforeach
        </div>
    </div>

    @if ($arrows)
        <button
            type="button"
            class="ws-slider-arrow ws-slider-arrow-next"
            x-bind="nextArrow"
            aria-label="{{ __('Next') }}"
        ></button>
    @endif

    <style>
        #{{ $id }} { --ws-slider-columns: {{ $columns }}; }
        @if ($columnsSm)
        @media (min-width: 576px) { #{{ $id }} { --ws-slider-columns: {{ $columnsSm }}; } }
        @endif
        @if ($columnsMd)
        @media (min-width: 768px) { #{{ $id }} { --ws-slider-columns: {{ $columnsMd }}; } }
        @endif
        @if ($columnsLg)
        @media (min-width: 992px) { #{{ $id }} { --ws-slider-columns: {{ $columnsLg }}; } }
        @endif
        @if ($columnsXl)
        @media (min-width: 1200px) { #{{ $id }} { --ws-slider-columns: {{ $columnsXl }}; } }
        @endif
        @if ($columnsXxl)
        @media (min-width: 1400px) { #{{ $id }} { --ws-slider-columns: {{ $columnsXxl }}; } }
        @endif
    </style>
</div>
