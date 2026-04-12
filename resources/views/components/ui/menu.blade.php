@props([
    'id' => null,
    'single' => config('wirestrap.menu.single', false),
    'events' => config('wirestrap.menu.events', false),
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'menu');

    $vars = get_defined_vars();
    $items = [];

    foreach ($vars as $var => $value) {
        if (
            $var !== 'slot'
            && $value instanceof \Illuminate\View\ComponentSlot
            && $value->hasActualContent()
        ) {
            $items[$var] = $value;
        }
    }
@endphp

<nav
    x-data="wsMenu"
    wire:key="ws-menu-{{ $id }}"
    data-ws-single="{{ $single ? 'true' : 'false' }}"
    data-ws-events="{{ $events ? 'true' : 'false' }}"
    id="{{ $id }}"
    class="ws-menu {{ $attributes->get('class', config('wirestrap.menu.class', '')) }}"
    {{ $attributes->except(['class']) }}
>
    @foreach ($items as $key => $item)
        <div wire:key="ws-menu-{{ $id }}-{{ $key }}" class="ws-menu-item">
            {{ $item }}
        </div>
    @endforeach
</nav>
