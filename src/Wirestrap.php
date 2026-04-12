<?php

namespace Wirestrap;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Wirestrap\Exceptions\MissingComponentIdException;

class Wirestrap
{
    public static function handleStrictMode(?string $id, string $component): void
    {
        if ($id === null && config('wirestrap.strict_mode', true)) {
            throw new MissingComponentIdException($component);
        }
    }

    /**
     * Resolve common form field state shared across form components.
     *
     * @return array{
     *     wiremodel: string|null,
     *     resolvedId: string|null,
     *     hasLabel: bool,
     *     hasError: bool,
     *     classInvalid: string,
     *     classFeedbackInvalid: string,
     * }
     */
    public static function resolveFormField(
        ComponentAttributeBag $attributes,
        mixed $label,
        bool $hasValidation,
        ViewErrorBag $errors,
        ?string $id = null,
    ): array {
        $wiremodel = (string) ($attributes->whereStartsWith('wire:model')->first() ?? '') ?: null;

        $hasLabel = $label instanceof \Illuminate\View\ComponentSlot
            ? $label->hasActualContent()
            : ($label !== null && $label !== '');

        return [
            'wiremodel' => $wiremodel,
            'resolvedId' => $id ?? ($wiremodel ? str_replace('.', '-', $wiremodel) : null),
            'hasLabel' => $hasLabel,
            'hasError' => $wiremodel !== null && $errors->has($wiremodel) && $hasValidation,
            'classInvalid' => (string) config('wirestrap.validation.invalid', 'ws-form-invalid'),
            'classFeedbackInvalid' => (string) config('wirestrap.validation.feedback_invalid', 'ws-form-feedback-invalid'),
        ];
    }

    /**
     * Resolve common form field state for check-type components (checkbox, radio, switch).
     *
     * @return array{
     *     wiremodel: string|null,
     *     resolvedId: string|null,
     *     hasLabel: bool,
     *     hasError: bool,
     *     classInvalid: string,
     *     classFeedbackInvalid: string,
     * }
     */
    public static function resolveCheckField(
        ComponentAttributeBag $attributes,
        mixed $label,
        ComponentSlot $slot,
        bool $hasValidation,
        ViewErrorBag $errors,
        ?string $id = null,
    ): array {
        $wiremodel = (string) ($attributes->whereStartsWith('wire:model')->first() ?? '') ?: null;
        $value = $attributes->get('value');

        $hasLabel = $slot->hasActualContent()
            || ($label instanceof \Illuminate\View\ComponentSlot
                ? $label->hasActualContent()
                : ($label !== null && $label !== ''));

        return [
            'wiremodel' => $wiremodel,
            'resolvedId' => $id ?? ($wiremodel
                ? str_replace('.', '-', $wiremodel) . ($value !== null ? '-' . $value : '')
                : null),
            'hasLabel' => $hasLabel,
            'hasError' => $wiremodel !== null && $errors->has($wiremodel) && $hasValidation,
            'classInvalid' => (string) config('wirestrap.validation.invalid', 'ws-form-invalid'),
            'classFeedbackInvalid' => (string) config('wirestrap.validation.feedback_invalid', 'ws-form-feedback-invalid'),
        ];
    }

    public static function iconComponent(): string
    {
        return config('wirestrap.icon.component_path') ?? 'wirestrap::ui.icon';
    }
}
