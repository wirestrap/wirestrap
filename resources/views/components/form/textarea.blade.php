@props([
    'id' => null,
    'label' => null,   /** @var \Illuminate\View\ComponentSlot|string|null */
    'floating' => false,
    'placeholder' => null,
    'icon' => null,    /** @var string|array<string,mixed>|null */
    'iconPlacement' => 'start',
    'disabled' => false,
    'rows' => config('wirestrap.textarea.rows', null),
    'autosize' => config('wirestrap.textarea.autosize', true),
    'hasValidation' => config('wirestrap.textarea.has_validation', true),
    'hasValidationMessage' => config('wirestrap.textarea.has_validation_message', true),
    'defaultAttributes' => config('wirestrap.textarea.default_attributes', []),
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
    $excludedAttributes = $autosize
        ? ['class', 'x-data', 'x-bind', 'data-ws-wiremodel']
        : ['class'];

    $textareaClass = 'ws-form-textarea'
        . ($hasIcon ? ' ws-form-has-icon-' . $iconPlacement : '')
        . ($hasError ? ' ' . $classInvalid : '');

    $resolvedPlaceholder = $floating ? ($placeholder ?? ' ') : $placeholder;

    $wrapperClass = implode(' ', array_filter([
        $floating ? 'ws-form-floating' : null,
        $attributes->get('class', config('wirestrap.textarea.class', '')) ?: null,
        $floating && $hasIcon ? 'ws-form-has-icon-' . $iconPlacement : null,
    ]));

    // wire:ignore.self must be on the direct parent of <textarea> for autosize
    // In non-floating + hasIcon: the inner wrapper is the direct parent
    // Otherwise: the outer wrapper is the direct parent
    $outerWireIgnoreSelf = $autosize && (!$hasIcon || $floating);
    $innerWireIgnoreSelf = $autosize && $hasIcon && !$floating;
@endphp

<div
    class="{{ $wrapperClass }}"
    @if ($outerWireIgnoreSelf) wire:ignore.self @endif
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

    {{-- Non-floating with icon: wrapper for icon positioning --}}
    @if (!$floating && $hasIcon)
        <div
            class="ws-form-textarea-wrapper{{ $hasError ? ' ' . $classInvalid : '' }}"
            @if ($innerWireIgnoreSelf) wire:ignore.self @endif
        >
    @endif

    <textarea
        @if ($resolvedId) id="{{ $resolvedId }}" @endif
        class="{{ $textareaClass }}"
        @if ($resolvedPlaceholder !== null) placeholder="{{ $resolvedPlaceholder }}" @endif
        @if ($rows !== null) rows="{{ $rows }}" @endif
        @disabled($disabled)
        {{ $attributes->except($excludedAttributes)->merge($defaultAttributes) }}
        @if ($autosize)
            x-data="wsTextarea"
            x-bind="textarea"
            @if ($wiremodel) data-ws-wiremodel="{{ $wiremodel }}" @endif
        @endif
    ></textarea>

    @if ($hasIcon)
        <span class="ws-form-input-icon ws-form-input-icon-{{ $iconPlacement }}" aria-hidden="true">
            <x-dynamic-component
                :component="\Wirestrap\Wirestrap::iconComponent()"
                :icon="$icon"
            />
        </span>
    @endif

    {{-- Non-floating with icon: close the wrapper --}}
    @if (!$floating && $hasIcon)
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
