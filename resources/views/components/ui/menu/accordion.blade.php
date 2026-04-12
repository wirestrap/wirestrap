@props([
    'id' => null,
    'open' => false,
    'label' => null,
    'trigger' => null,  /** @var \Illuminate\View\ComponentSlot|null Free slot for the trigger content */
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'menu.accordion');

    $vars = get_defined_vars();
    $items = [];

    foreach ($vars as $var => $value) {
        if (
            $var !== 'slot'
            && $var !== 'trigger'
            && $value instanceof \Illuminate\View\ComponentSlot
            && $value->hasActualContent()
        ) {
            $items[$var] = $value;
        }
    }
@endphp

<div
    class="ws-menu-accordion {{ $attributes->get('class', '') }}"
    wire:key="ws-menu-accordion-{{ $id }}"
    wire:ignore.self
    {{ $attributes->except(['class']) }}
>
    <button
        type="button"
        class="ws-menu-accordion-trigger{{ $open ? ' open' : '' }}"
        data-ws-menu-accordion="{{ $id }}"
        aria-controls="ws-menu-accordion-panel-{{ $id }}"
        x-bind="menuAccordionTrigger"
        wire:ignore.self
    >
        @if ($trigger instanceof \Illuminate\View\ComponentSlot && $trigger->hasActualContent())
            {{ $trigger }}
        @else
            {{ $label ?? $id }}
        @endif
    </button>

    <div
        class="ws-menu-accordion-panel"
        id="ws-menu-accordion-panel-{{ $id }}"
        data-ws-menu-accordion-panel="{{ $id }}"
        @if ($open) data-ws-open="true" @else style="display: none" @endif
        wire:ignore.self
    >
        @foreach ($items as $key => $item)
            <div wire:key="ws-menu-accordion-{{ $id }}-{{ $key }}" class="ws-menu-item">
                {{ $item }}
            </div>
        @endforeach
    </div>
</div>
