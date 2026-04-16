@props([
    'id' => null,
    'events' => config('wirestrap.accordion.events', false),
    'single' => config('wirestrap.accordion.single', false),
    'invalidFeedback' => config('wirestrap.accordion.invalid_feedback', false),
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'accordion');

    $vars = get_defined_vars();
    $items = [];

    foreach ($vars as $var => $value) {
        if (
            $var !== 'slot'
            && !Str::startsWith($var, 'label_')
            && $value instanceof \Illuminate\View\ComponentSlot
            && $value->hasActualContent()
        ) {
            $items[$var] = $value;
        }
    }
@endphp

<div
    x-data="wsAccordion"
    wire:key="ws-accordion-{{ $id }}"
    data-ws-single="{{ $single ? 'true' : 'false' }}"
    data-ws-events="{{ $events ? 'true' : 'false' }}"
    @if ($invalidFeedback) data-ws-invalid-class="{{ config('wirestrap.validation.invalid', 'ws-form-invalid') }}" @endif
    id="{{ $id }}"
    class="ws-accordion {{ $attributes->get('class', config('wirestrap.accordion.class', '')) }}"
    {{ $attributes->except(['class']) }}
>
    @foreach ($items as $key => $item)
        @php
            $labelSlotVar = 'label_' . $key;
            $itemIcon = $item->attributes->get('icon');
            $itemIconPlacement = $item->attributes->get('icon-placement', 'start');
            $isOpen = $item->attributes->get('open', false);
        @endphp

        <div wire:key="ws-accordion-{{ $id }}-{{ $key }}" class="ws-accordion-item">
            <div class="ws-accordion-header">
                <button
                    class="ws-accordion-button{{ $isOpen ? ' show' : '' }}"
                    x-bind="accordionTrigger"
                    data-ws-accordion-item="{{ $key }}"
                    type="button"
                    wire:ignore.self
                >
                    @if ($itemIcon && $itemIconPlacement === 'start')
                        <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$itemIcon" />
                    @endif

                    @if (isset($$labelSlotVar) && $$labelSlotVar instanceof \Illuminate\View\ComponentSlot)
                        {{ $$labelSlotVar }}
                    @else
                        {{ $item->attributes->get('label', $key) }}
                    @endif

                    @if ($itemIcon && $itemIconPlacement === 'end')
                        <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$itemIcon" />
                    @endif
                </button>
            </div>

            <div
                wire:ignore.self
                class="ws-accordion-panel"
                data-ws-accordion-panel="{{ $key }}"
                @if ($isOpen) data-ws-open="true" @else style="display: none" @endif
            >
                <div
                    class="ws-accordion-body{{ $item->attributes->get('class') ? ' ' . $item->attributes->get('class') : '' }}"
                    {{ $item->attributes->except(['icon', 'icon-placement', 'label', 'open', 'class']) }}
                >
                    {{ $item }}
                </div>
            </div>
        </div>
    @endforeach
</div>
