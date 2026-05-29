@props([
    'id' => null,
    'events' => config('wirestrap.tabs.events', false),
    'default' => null,          /** @var string|null Key of the tab active by default (first tab if null) */
    'invalidFeedback' => config('wirestrap.tabs.invalid_feedback', false),
    'actions' => null,          /** @var \Illuminate\View\ComponentSlot|null Free slot rendered after the tab links */
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'tabs');

    $vars = get_defined_vars();
    $tabs = [];

    foreach ($vars as $var => $value) {
        if (
            $var !== 'slot'
            && $var !== 'actions'
            && !Str::startsWith($var, 'label_')
            && $value instanceof \Illuminate\View\ComponentSlot
            && $value->hasActualContent()
        ) {
            $tabs[$var] = $value;
        }
    }

    $defaultTab = ($default !== null && isset($tabs[$default]) ? $default : null) ?? array_key_first($tabs);
@endphp

<div
    x-data="wsTabs"
    wire:key="ws-tabs-{{ $id }}"
    data-ws-default="{{ $defaultTab }}"
    data-ws-events="{{ $events ? 'true' : 'false' }}"
    @if ($invalidFeedback) data-ws-invalid-class="{{ config('wirestrap.validation.invalid', 'ws-form-invalid') }}" @endif
    id="{{ $id }}"
    class="{{ $attributes->get('class', config('wirestrap.tabs.class', '')) }}"
    {{ $attributes->except(['class']) }}
>
    <ul class="ws-tabs-nav" role="tablist">
        @foreach ($tabs as $key => $tab)
            @php
                $labelSlotVar = 'label_' . $key;
                $tabIcon = $tab->attributes->get('icon');
                $tabIconPlacement = $tab->attributes->get('icon-placement', 'start');
            @endphp
            <li wire:key="ws-tabs-{{ $id }}-nav-{{ $key }}" role="presentation">
                <button
                    class="ws-tabs-nav-button"
                    x-bind="navLink"
                    id="ws-tabs-{{ $id }}-btn-{{ $key }}"
                    data-ws-tab="{{ $key }}"
                    type="button"
                    role="tab"
                    aria-controls="ws-tabs-{{ $id }}-panel-{{ $key }}"
                    wire:ignore.self
                >
                    @if ($tabIcon && $tabIconPlacement === 'start')
                        <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$tabIcon" />
                    @endif

                    @if (isset($$labelSlotVar) && $$labelSlotVar instanceof \Illuminate\View\ComponentSlot)
                        {{ $$labelSlotVar }}
                    @else
                        {{ $tab->attributes->get('label', $key) }}
                    @endif

                    @if ($tabIcon && $tabIconPlacement === 'end')
                        <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$tabIcon" />
                    @endif
                </button>
            </li>
        @endforeach
        @if ($actions instanceof \Illuminate\View\ComponentSlot && $actions->hasActualContent())
            <li class="ws-tabs-nav-actions">{{ $actions }}</li>
        @endif
    </ul>

    <div class="ws-tabs-content" x-ref="tabsContent">
        <div x-ref="tabsInner">
            @foreach ($tabs as $key => $tab)
                <div
                    wire:key="ws-tabs-{{ $id }}-panel-{{ $key }}"
                    wire:ignore.self
                    id="ws-tabs-{{ $id }}-panel-{{ $key }}"
                    data-ws-tab="{{ $key }}"
                    class="ws-tabs-panel{{ $key !== $defaultTab ? ' ws-tabs-panel--hidden' : '' }}"
                    role="tabpanel"
                    aria-labelledby="ws-tabs-{{ $id }}-btn-{{ $key }}"
                >
                    {{ $tab }}
                </div>
            @endforeach
        </div>
    </div>
</div>
