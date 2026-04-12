@props([
    'id' => null,
    'label' => null,   /** @var \Illuminate\View\ComponentSlot|string|null */
    'reverse' => false,
    'inline' => false,
    'disabled' => false,
    'hasValidation' => config('wirestrap.checkbox.has_validation', true),
    'hasValidationMessage' => config('wirestrap.checkbox.has_validation_message', true),
    'defaultAttributes' => config('wirestrap.checkbox.default_attributes', []),
])

@php
    [
        'wiremodel' => $wiremodel,
        'resolvedId' => $resolvedId,
        'hasLabel' => $hasLabel,
        'hasError' => $hasError,
        'classInvalid' => $classInvalid,
        'classFeedbackInvalid' => $classFeedbackInvalid,
    ] = \Wirestrap\Wirestrap::resolveCheckField($attributes, $label, $slot, $hasValidation, $errors, $id);

    $wrapperClass = implode(' ', array_filter([
        'ws-form-check',
        $inline ? 'ws-form-check-inline' : null,
        $reverse ? 'ws-form-check-reverse' : null,
        $attributes->get('class', config('wirestrap.checkbox.class', '')) ?: null,
    ]));

    $inputClass = 'ws-form-check-input' . ($hasError ? ' ' . $classInvalid : '');
@endphp

<div class="{{ $wrapperClass }}">
    <input
        @if ($resolvedId) id="{{ $resolvedId }}" @endif
        type="checkbox"
        class="{{ $inputClass }}"
        @disabled($disabled)
        {{ $attributes->except(['class'])->merge($defaultAttributes) }}
    />

    @if ($hasLabel)
        <label
            @if ($resolvedId) for="{{ $resolvedId }}" @endif
            class="ws-form-check-label"
        >
            {{ $slot->hasActualContent() ? $slot : $label }}
        </label>
    @endif

    @if ($hasError && $hasValidationMessage)
        <div class="{{ $classFeedbackInvalid }}">
            {{ $errors->first($wiremodel) }}
        </div>
    @endif
</div>
