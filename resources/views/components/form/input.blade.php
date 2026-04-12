@props([
    'id' => null,
    'label' => null,   /** @var \Illuminate\View\ComponentSlot|string|null */
    'floating' => false,
    'type' => 'text',
    'placeholder' => null,
    'icon' => null,    /** @var string|array<string,mixed>|null */
    'iconPlacement' => 'start',
    'disabled' => false,
    'password' => false,
    'hasValidation' => config('wirestrap.input.has_validation', true),
    'hasValidationMessage' => config('wirestrap.input.has_validation_message', true),
    'defaultAttributes' => config('wirestrap.input.default_attributes', []),
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
    $hasPasswordToggle = $password;
    $needsIconWrapper = $hasIcon || $hasPasswordToggle;

    $passwordShowIcon = config('wirestrap.input.password_show_icon', 'bi-eye');
    $passwordHideIcon = config('wirestrap.input.password_hide_icon', 'bi-eye-slash');

    $inputClass = 'ws-form-input'
        . ($hasIcon ? ' ws-form-has-icon-' . $iconPlacement : '')
        . ($hasPasswordToggle ? ' ws-form-has-icon-end' : '')
        . ($hasError ? ' ' . $classInvalid : '');

    $resolvedPlaceholder = $floating ? ($placeholder ?? ' ') : $placeholder;

    $wrapperClass = implode(' ', array_filter([
        $floating ? 'ws-form-floating' : null,
        $attributes->get('class', config('wirestrap.input.class', '')) ?: null,
        $floating && $hasIcon ? 'ws-form-has-icon-' . $iconPlacement : null,
        $floating && $hasPasswordToggle ? 'ws-form-has-icon-end' : null,
        $floating && $hasError ? $classInvalid : null,
    ]));
@endphp

<div
    class="{{ $wrapperClass }}"
    @if ($hasPasswordToggle)
        x-data="wsInput"
        data-ws-label-show="{{ __('Show password') }}"
        data-ws-label-hide="{{ __('Hide password') }}"
    @endif
>
    {{-- Non-floating: label before the input --}}
    @if (!$floating && $hasLabel)
        <label
            @if ($resolvedId) for="{{ $resolvedId }}" @endif
            class="ws-form-label"
        >
            {{ $label }}
        </label>
    @endif

    {{-- Non-floating with icon/password: wrapper for icon positioning --}}
    @if (!$floating && $needsIconWrapper)
        <div class="ws-form-input-wrapper{{ $hasError ? ' ' . $classInvalid : '' }}">
    @endif

    <input
        @if ($resolvedId) id="{{ $resolvedId }}" @endif
        @if ($hasPasswordToggle)
            type="password"
            :type="showPassword ? 'text' : 'password'"
        @else
            type="{{ $type }}"
        @endif
        class="{{ $inputClass }}"
        @if ($resolvedPlaceholder !== null) placeholder="{{ $resolvedPlaceholder }}" @endif
        @disabled($disabled)
        {{ $attributes->except(['class'])->merge($defaultAttributes) }}
    />

    @if ($hasIcon)
        <span class="ws-form-input-icon ws-form-input-icon-{{ $iconPlacement }}" aria-hidden="true">
            <x-dynamic-component
                :component="\Wirestrap\Wirestrap::iconComponent()"
                :icon="$icon"
            />
        </span>
    @endif

    @if ($hasPasswordToggle)
        <button
            type="button"
            class="ws-form-input-icon ws-form-input-icon-end ws-form-input-password-toggle"
            x-on:click="toggle()"
            :aria-label="showPassword ? $root.getAttribute('data-ws-label-hide') : $root.getAttribute('data-ws-label-show')"
        >
            <span x-show="!showPassword">
                <x-dynamic-component
                    :component="\Wirestrap\Wirestrap::iconComponent()"
                    :icon="$passwordShowIcon"
                />
            </span>
            <span x-show="showPassword">
                <x-dynamic-component
                    :component="\Wirestrap\Wirestrap::iconComponent()"
                    :icon="$passwordHideIcon"
                />
            </span>
        </button>
    @endif

    {{-- Non-floating with icon/password: close the wrapper --}}
    @if (!$floating && $needsIconWrapper)
        </div>
    @endif

    {{-- Floating: label must come after the input (CSS sibling selector) --}}
    @if ($floating && $hasLabel)
        <label
            @if ($resolvedId) for="{{ $resolvedId }}" @endif
            class="ws-form-label"
        >
            {{ $label }}
        </label>
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
