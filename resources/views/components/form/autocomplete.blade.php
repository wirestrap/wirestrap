@props([
    'id' => null,
    'label' => null,    /** @var \Illuminate\View\ComponentSlot|string|null */
    'floating' => false,
    'placeholder' => null,
    'icon' => null,     /** @var string|array<string,mixed>|null */
    'iconPlacement' => 'start',
    'disabled' => false,
    'multiple' => false,
    'live' => false,
    'wireOptions' => null,      /** @var string|list<mixed>|null */
    'wireOptionsWatch' => null, /** @var string|list<mixed>|null */
    'hasValidation' => config('wirestrap.input.has_validation', true),
    'hasValidationMessage' => config('wirestrap.input.has_validation_message', true),
    'defaultAttributes' => config('wirestrap.input.default_attributes', []),
    'dropdownOffset' => config('wirestrap.autocomplete.dropdown_offset', 0),
    'position' => config('wirestrap.autocomplete.position', 'absolute'),
    'teleport' => config('wirestrap.autocomplete.teleport', null),
])

@php
    [
        'wiremodel' => $wiremodel,
        'resolvedId' => $resolvedId,
        'hasLabel' => $hasLabel,
        'hasError' => $hasError,
        'classInvalid' => $classInvalid,
        'classFeedbackInvalid' => $classFeedbackInvalid,
    ] = \Wirestrap\Wirestrap::resolveFormField($attributes, $label, $hasValidation, $errors, $id);

    $hasIcon = $icon !== null;

    // Detect per-item errors in multiple mode (e.g. emails.0, emails.1)
    $invalidIndices = [];
    if ($multiple && $wiremodel && $hasValidation) {
        foreach ($errors->keys() as $key) {
            if (preg_match('/^' . preg_quote($wiremodel, '/') . '\.(\d+)$/', $key, $m)) {
                $invalidIndices[] = (int) $m[1];
            }
        }
        if (!$hasError && !empty($invalidIndices)) {
            $hasError = true;
        }
    }

    $firstErrorMessage = $errors->first($wiremodel)
        ?: (!empty($invalidIndices) ? $errors->first("{$wiremodel}.{$invalidIndices[0]}") : null);

    // In multiple mode, wire:model is managed by Alpine: not passed to the input
    $inputAttributes = $multiple
        ? $attributes->whereDoesntStartWith('wire:model')->except(['class'])->merge($defaultAttributes)
        : $attributes->except(['class'])->merge($defaultAttributes);

    $autocompleteClass = 'ws-autocomplete'
        . ($multiple ? ' ws-autocomplete-is-multiple' : '')
        . ($hasIcon ? ' ws-autocomplete-has-icon-' . $iconPlacement : '')
        . (!$floating && $hasError ? ' ' . $classInvalid : '');

    // In multiple mode, the input is invisible: invalid state goes on the wrapper instead
    $inputInvalidClass = ($hasError && !$multiple) ? ' ' . $classInvalid : '';
    $wrapperInvalidClass = ($hasError && $multiple) ? ' ' . $classInvalid : '';

    // In floating mode: placeholder is always forced to ' ' for :placeholder-shown CSS detection
    $resolvedPlaceholder = $floating ? ' ' : $placeholder;

    $wrapperClass = implode(' ', array_filter([
        $floating ? 'ws-form-floating' : null,
        $attributes->get('class', '') ?: null,
        $floating && $hasIcon ? 'ws-form-has-icon-' . $iconPlacement : null,
        $floating && $hasError ? $classInvalid : null,
    ]));

    // Parse wire-options: string = method, array = [method, ...params]
    $wireOptionsMethod = null;
    $wireOptionsParamsJson = null;
    if (is_array($wireOptions)) {
        $wireOptionsMethod = $wireOptions[0] ?? null;
        $params = array_slice($wireOptions, 1);
        $wireOptionsParamsJson = $params ? json_encode(array_values($params)) : null;
    } else {
        $wireOptionsMethod = $wireOptions;
    }

    // Parse wire-options-watch: string = property, array = [property, resetValue?, liveReset?]
    $wireOptionsWatchProp = null;
    $wireOptionsResetJson = null;
    $wireOptionsResetLive = false;
    if (is_array($wireOptionsWatch)) {
        $wireOptionsWatchProp = $wireOptionsWatch[0] ?? null;
        if (array_key_exists(1, $wireOptionsWatch)) {
            $wireOptionsResetJson = json_encode($wireOptionsWatch[1]);
            $wireOptionsResetLive = (bool) ($wireOptionsWatch[2] ?? false);
        }
    } else {
        $wireOptionsWatchProp = $wireOptionsWatch;
    }
@endphp

<div class="{{ $wrapperClass }}">
    {{-- Non-floating: label before the input --}}
    @if (!$floating && $hasLabel)
        <label @if ($resolvedId) for="{{ $resolvedId }}" @endif class="ws-form-label">
            {{ $label }}
        </label>
    @endif

    <div
        class="{{ $autocompleteClass }}"
        x-data="wsAutocomplete"
        x-bind="autocompleteRoot"
        @if ($wireOptionsMethod) data-ws-wire-options="{{ $wireOptionsMethod }}" @endif
        @if ($wireOptionsParamsJson !== null) data-ws-wire-options-params="{{ $wireOptionsParamsJson }}" @endif
        @if ($wireOptionsWatchProp) data-ws-wire-options-watch="{{ $wireOptionsWatchProp }}" @endif
        @if ($wireOptionsResetJson !== null) data-ws-wire-options-reset="{{ $wireOptionsResetJson }}" @endif
        @if ($wireOptionsResetLive) data-ws-wire-options-reset-live="true" @endif
        @if ($wiremodel) data-ws-wiremodel="{{ $wiremodel }}" @endif
        @if ($multiple && !empty($invalidIndices)) data-ws-invalid-indices="{{ json_encode($invalidIndices) }}" @endif
        data-ws-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-ws-live="{{ $live ? 'true' : 'false' }}"
        data-ws-dropdown-offset="{{ $dropdownOffset }}"
        data-ws-position="{{ $position }}"
        data-ws-label-remove="{{ __('Remove') }}"
        @if ($disabled) disabled @endif
    >
        <div
            class="ws-autocomplete-input-wrapper{{ $wrapperInvalidClass }}"
            x-bind="inputWrapper"
        >
            @if ($hasIcon && $multiple)
                <span class="ws-form-input-icon ws-form-input-icon-{{ $iconPlacement }}">
                    <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$icon" />
                </span>
            @endif

            <div wire:ignore class="ws-d-contents" x-ref="tagList"></div>

            <div class="ws-autocomplete-field">
                <div class="ws-autocomplete-ghost" aria-hidden="true">
                    <span class="ws-autocomplete-ghost-match" x-text="query"></span>
                    <span class="ws-autocomplete-ghost-suffix" x-text="ghostSuffix"></span>
                </div>

                <input
                    x-ref="input"
                    x-bind="autocompleteInput"
                    @if ($resolvedId) id="{{ $resolvedId }}" @endif
                    @if ($resolvedId) aria-controls="{{ $resolvedId }}-listbox" @endif
                    @if ($resolvedId && $teleport) aria-owns="{{ $resolvedId }}-listbox" @endif
                    type="text"
                    class="ws-form-input ws-autocomplete-input{{ $inputInvalidClass }}"
                    @if ($resolvedPlaceholder !== null) placeholder="{{ $resolvedPlaceholder }}" @endif
                    autocomplete="off"
                    @disabled($disabled)
                    {{ $inputAttributes }}
                />

                @if ($hasIcon && !$multiple)
                    <span class="ws-form-input-icon ws-form-input-icon-{{ $iconPlacement }}" aria-hidden="true">
                        <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$icon" />
                    </span>
                @endif
            </div>
        </div>

        <div wire:ignore>
            @if ($teleport) @teleport($teleport) @endif
            <div
                x-bind="floatable"
                class="ws-autocomplete-dropdown"
                data-ws-floatable
                data-floating-placement="bottom"
                style="display: none"
                @if ($resolvedId) id="{{ $resolvedId }}-listbox" @endif
            >
                <template x-for="suggestion in filteredSuggestions" :key="suggestion">
                    <div
                        class="ws-autocomplete-option"
                        role="option"
                        data-ws-option
                        x-text="suggestion"
                    ></div>
                </template>
            </div>
            @if ($teleport) @endteleport @endif
        </div>
    </div>

    {{-- Floating: label must come after the input (CSS sibling selector) --}}
    @if ($floating && $hasLabel)
        <label @if ($resolvedId) for="{{ $resolvedId }}" @endif class="ws-form-label">
            {{ $label }}
        </label>
    @endif

    {{-- Non-floating: error message inside the wrapper --}}
    @if (!$floating && $hasError && $hasValidationMessage)
        <div class="{{ $classFeedbackInvalid }}">{{ $firstErrorMessage }}</div>
    @endif

</div>

{{-- Floating: error message must be outside the wrapper (CSS sibling selector) --}}
@if ($floating && $hasError && $hasValidationMessage)
    <div class="{{ $classFeedbackInvalid }}">{{ $firstErrorMessage }}</div>
@endif
