@props([
    'id' => null,
    'label' => null,    /** @var \Illuminate\View\ComponentSlot|string|null */
    'floating' => false,
    'icon' => null,     /** @var string|array<string,mixed>|null */
    'hasValidation' => config('wirestrap.select.has_validation', true),
    'hasValidationMessage' => config('wirestrap.select.has_validation_message', true),
    'defaultAttributes' => config('wirestrap.select.default_attributes', []),
    'multiple' => config('wirestrap.select.multiple', false),
    'search' => config('wirestrap.select.search', false),
    'placeholder' => config('wirestrap.select.placeholder', 'Select an option'),
    'searchPlaceholder' => config('wirestrap.select.search_placeholder', 'Search ...'),
    'emptyValue' => config('wirestrap.select.empty_value', null),
    'options' => null,
    'wireOptions' => null,
    'wireOptionsParams' => null,
    'wireOptionsWatch' => null,
    'live' => config('wirestrap.select.live', false),
    'dropdownOffset' => config('wirestrap.select.dropdown_offset', 0),
    'position' => config('wirestrap.select.position', 'absolute'),
    'teleport' => config('wirestrap.select.teleport', null),
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'select');

    [
        'wiremodel' => $wiremodel,
        'hasLabel' => $hasLabel,
        'hasError' => $hasError,
        'classInvalid' => $classInvalid,
        'classFeedbackInvalid' => $classFeedbackInvalid,
    ] = \Wirestrap\Wirestrap::resolveFormField($attributes, $label, $hasValidation, $errors);

    $hasIcon = $icon !== null;

    $selectionClass = 'ws-form-select'
        . ($hasIcon ? ' ws-form-has-icon-start' : '')
        . ($hasError ? ' ' . $classInvalid : '');

    # Wrapper class: floating adds ws-form-floating + icon/error classes at wrapper level
    $wrapperClass = implode(' ', array_filter([
        $floating ? 'ws-form-floating' : null,
        $attributes->get('class', config('wirestrap.select.class', '')) ?: null,
        $floating && $hasIcon ? 'ws-form-has-icon-start' : null,
        $floating && $hasError ? $classInvalid : null,
    ]));

    # ws-select error class: only when non-floating without icon (otherwise the wrapper carries it)
    $wsSelectClass = 'ws-select' . (!$floating && !$hasIcon && $hasError ? ' ' . $classInvalid : '');

    $normalizedOptions = null;
    if ($options !== null) {
        $normalized = [];
        foreach ($options as $key => $item) {
            $normalized[] = is_array($item) ? $item : ['value' => $key, 'label' => $item];
        }
        $normalizedOptions = json_encode($normalized);
    }
@endphp

<div class="{{ $wrapperClass }}">
    {{-- Non-floating: label before the input --}}
    @if (!$floating && $hasLabel)
        <span
            @if ($id) id="{{ $id }}-label" @endif
            class="ws-form-label"
        >
            {{ $label }}
        </span>
    @endif

    {{-- Non-floating with icon: wrapper for icon positioning --}}
    @if (!$floating && $hasIcon)
        <div class="ws-form-select-wrapper{{ $hasError ? ' ' . $classInvalid : '' }}">
    @endif

    <div
        x-data="wsSelect"
        x-bind="select"
        wire:key="ws-select-{{ $id }}"
        class="{{ $wsSelectClass }}"
        data-ws-wiremodel="{{ $wiremodel }}"
        data-ws-multiple="{{ $multiple === true ? 'true' : 'false' }}"
        data-ws-placeholder="{{ __($placeholder) }}"
        data-ws-live="{{ $live === true ? 'true' : 'false' }}"
        @if ($wireOptions) data-ws-wire-options="{{ $wireOptions }}" @endif
        @if ($wireOptionsParams !== null) data-ws-wire-options-params="{{ json_encode(is_array($wireOptionsParams) ? $wireOptionsParams : [$wireOptionsParams]) }}" @endif
        @if ($wireOptionsWatch) data-ws-wire-options-watch="{{ $wireOptionsWatch }}" @endif
        @if ($normalizedOptions !== null) data-ws-options="{{ $normalizedOptions }}" @endif
        @if ($emptyValue !== null) data-ws-empty-value="{{ $emptyValue }}" @endif
        data-ws-dropdown-offset="{{ $dropdownOffset }}"
        data-ws-position="{{ $position }}"
        {{
            $attributes->whereDoesntStartWith('wire:model')->except([
                'class', 'x-data', 'data-ws-wiremodel', 'data-ws-multiple', 'data-ws-placeholder',
                'data-ws-live', 'data-ws-wire-options', 'data-ws-wire-options-params', 'data-ws-wire-options-watch', 'data-ws-options',
                'data-ws-dropdown-offset', 'data-ws-position',
            ])->merge($defaultAttributes)
        }}
    >
        <div
            wire:ignore
            x-bind="selection"
            @if ($id) id="{{ $id }}" @endif
            @if ($id && $hasLabel) aria-labelledby="{{ $id }}-label" @endif
            @if ($id) aria-controls="{{ $id }}-listbox" @endif
            @if ($id && $teleport) aria-owns="{{ $id }}-listbox" @endif
            class="{{ $selectionClass }}"
        >
            <span class="ws-selection-placeholder">{{ __($placeholder) }}</span>
        </div>

        <div wire:ignore>
            @if ($teleport) @teleport($teleport) @endif
            <div
                x-bind="floatable"
                class="ws-select-dropdown"
                data-ws-floatable
                data-floating-placement="bottom"
                style="display: none"
                @if ($id) id="{{ $id }}-listbox" @endif
            >
                @if ($search)
                    <div class="ws-search-container">
                        <input
                            x-bind="searchInput"
                            type="text"
                            @if ($id) id="{{ $id }}-search" @endif
                            placeholder="{{ __($searchPlaceholder) }}"
                            aria-label="{{ __($searchPlaceholder) }}"
                        />
                    </div>
                @endif

                <div x-bind="loader"></div>

                <div wire:ignore class="ws-d-contents" x-bind="optionList"></div>
            </div>
            @if ($teleport) @endteleport @endif
        </div>
    </div>

    @if ($hasIcon)
        <span class="ws-form-input-icon ws-form-input-icon-start" aria-hidden="true">
            <x-dynamic-component
                :component="\Wirestrap\Wirestrap::iconComponent()"
                :icon="$icon"
            />
        </span>
    @endif

    {{-- Non-floating with icon: close the icon wrapper --}}
    @if (!$floating && $hasIcon)
        </div>
    @endif

    {{-- Floating: label must come after the input (CSS sibling selector) --}}
    @if ($floating && $hasLabel)
        <span
            @if ($id) id="{{ $id }}-label" @endif
            class="ws-form-label"
        >
            {{ $label }}
        </span>
    @endif

    {{-- Non-floating: error message inside the wrapper --}}
    @if (!$floating && $hasError && $hasValidationMessage)
        <div class="{{ $classFeedbackInvalid }}">
            {{ $errors->first($wiremodel) }}
        </div>
    @endif

</div>

{{-- Floating: error message must be outside the wrapper (CSS sibling selector) --}}
@if ($floating && $hasError && $hasValidationMessage)
    <div class="{{ $classFeedbackInvalid }}">
        {{ $errors->first($wiremodel) }}
    </div>
@endif
