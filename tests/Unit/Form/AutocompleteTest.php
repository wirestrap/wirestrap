<?php

// --- Element ---

test('renders ws-autocomplete class', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    // Token match: a substring assertion would also match ws-autocomplete-input-wrapper,
    // ws-autocomplete-field or ws-autocomplete-dropdown.
    expect(htmlHasClass($html, 'ws-autocomplete'))->toBeTrue();
});

test('renders x-data wsAutocomplete', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('x-data="wsAutocomplete"', false);
});

test('renders x-bind autocompleteRoot', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('x-bind="autocompleteRoot"', false);
});

test('renders input with ws-form-input and ws-autocomplete-input classes', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('ws-form-input ws-autocomplete-input', false);
});

test('input has autocomplete off', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('autocomplete="off"', false);
});

test('input has type text', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('type="text"', false);
});

// --- Ghost text ---

test('ghost text elements rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('ws-autocomplete-ghost', false)
        ->assertSee('ws-autocomplete-ghost-match', false)
        ->assertSee('ws-autocomplete-ghost-suffix', false);
});

test('ghost text has aria-hidden', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    expect($html)->toHaveAttributeOn('ws-autocomplete-ghost', 'aria-hidden');
});

// --- wire:ignore ---

test('tag list has wire:ignore', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);
    $xpath = new DOMXPath($doc);
    $el = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' ws-d-contents ')]")->item(0);

    expect($el)->not->toBeNull()
        ->and($el->hasAttribute('wire:ignore'))->toBeTrue();
});

test('multiple wire:ignore elements present', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    // tagList and dropdown wrapper both have wire:ignore
    preg_match_all('/wire:ignore(?!\.self)/', $html, $matches);
    expect(count($matches[0]))->toBeGreaterThanOrEqual(2);
});

// --- Label ---

test('renders label from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" />')
        ->assertSee('ws-form-label', false)
        ->assertSee('City');
});

test('renders label from slot', function () {
    $this->withViewErrors([])
        ->blade('
            <x-wirestrap::autocomplete id="test" wire-options="getItems">
                <x-slot:label>Rich <strong>label</strong></x-slot:label>
            </x-wirestrap::autocomplete>
        ')
        ->assertSee('<strong>label</strong>', false);
});

test('no label element when label is absent', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertDontSee('ws-form-label', false);
});

test('label has for attribute matching id', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" />')
        ->assertSee('for="test"', false);
});

test('non-floating label renders before autocomplete', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" />')
        ->__toString();

    $labelPos = strpos($html, 'ws-form-label');
    $autoPos = strpos($html, 'ws-autocomplete');

    expect($labelPos)->toBeLessThan($autoPos);
});

test('floating label renders after autocomplete', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" floating />')
        ->__toString();

    $autoPos = strpos($html, 'ws-autocomplete');
    $labelPos = strpos($html, 'ws-form-label');

    expect($autoPos)->toBeLessThan($labelPos);
});

// --- Floating ---

test('floating adds ws-form-floating class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" floating />')
        ->assertSee('ws-form-floating', false);
});

test('no floating class by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertDontSee('ws-form-floating', false);
});

test('floating forces placeholder to space', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" label="City" floating placeholder="Type..." />')
        ->__toString();

    expect($html)->toContain('placeholder=" "');
});

// --- Placeholder ---

test('placeholder from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" placeholder="Type a city..." />')
        ->assertSee('placeholder="Type a city..."', false);
});

test('no placeholder by default', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    expect($html)->not->toContain('placeholder=');
});

// --- Icon ---

test('single mode icon renders inside autocomplete-field', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" icon="bi-search" />')
        ->assertSee('ws-form-input-icon', false)
        ->assertDontSee('ws-autocomplete-is-multiple', false);
});

test('icon start placement class on autocomplete root', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" icon="bi-search" />')
        ->assertSee('ws-autocomplete-has-icon-start', false);
});

test('icon end placement class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" icon="bi-search" icon-placement="end" />')
        ->assertSee('ws-autocomplete-has-icon-end', false);
});

test('multiple mode icon renders inside input-wrapper', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" icon="bi-search" multiple />')
        ->__toString();

    // Icon should be inside the input-wrapper, before tagList
    $iconPos = strpos($html, 'ws-form-input-icon');
    $wrapperPos = strpos($html, 'ws-autocomplete-input-wrapper');

    expect($iconPos)->toBeGreaterThan($wrapperPos);
});

test('floating with icon adds icon class to wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" icon="bi-search" label="City" floating />')
        ->assertSee('ws-form-has-icon-start', false);
});

// --- Dropdown ---

test('dropdown has ws-autocomplete-dropdown class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('ws-autocomplete-dropdown', false);
});

test('dropdown has display none by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('style="display: none"', false);
});

test('dropdown has id {id}-listbox', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('id="test-listbox"', false);
});

test('dropdown has data-ws-floatable', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-floatable', false);
});

// --- Data attributes ---

test('data-ws-wiremodel from wire:model', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" wire:model="city" />')
        ->assertSee('data-ws-wiremodel="city"', false);
});

test('data-ws-multiple true when multiple', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" multiple />')
        ->assertSee('data-ws-multiple="true"', false);
});

test('data-ws-multiple false by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-multiple="false"', false);
});

test('data-ws-live true when live', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" live />')
        ->assertSee('data-ws-live="true"', false);
});

test('data-ws-live false by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-live="false"', false);
});

test('data-ws-min-chars rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" :min-chars="3" />')
        ->assertSee('data-ws-min-chars="3"', false);
});

test('data-ws-min-chars defaults to zero', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-min-chars="0"', false);
});

test('data-ws-no-dropdown rendered when set', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" no-dropdown />')
        ->assertSee('data-ws-no-dropdown="true"', false);
});

test('no data-ws-no-dropdown by default', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-no-dropdown');
});

test('data-ws-dropdown-offset rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" :dropdown-offset="5" />')
        ->assertSee('data-ws-dropdown-offset="5"', false);
});

test('data-ws-dropdown-offset defaults to zero', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-dropdown-offset="0"', false);
});

test('data-ws-position rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" position="fixed" />')
        ->assertSee('data-ws-position="fixed"', false);
});

test('data-ws-position defaults to absolute', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-position="absolute"', false);
});

test('data-ws-label-remove rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('data-ws-label-remove=', false);
});

// --- Multiple mode ---

test('multiple mode adds ws-autocomplete-is-multiple class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" multiple />')
        ->assertSee('ws-autocomplete-is-multiple', false);
});

test('multiple mode strips wire:model from input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" wire:model="tags" multiple />')
        ->__toString();

    // wire:model should NOT appear on the input element (managed by Alpine)
    // But data-ws-wiremodel should exist on the root
    expect($html)->toContain('data-ws-wiremodel="tags"');

    // Count occurrences of wire:model — should appear only in the non-input context
    preg_match_all('/wire:model(?!=)/', $html, $matches);
    // The wire:model on the input should have been stripped
    // There should be zero wire:model attributes on <input> elements
    preg_match('/<input[^>]*wire:model/', $html, $inputMatch);
    expect($inputMatch)->toBeEmpty();
});

// --- wire-options ---

test('data-ws-wire-options rendered with method name', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getCities" />')
        ->assertSee('data-ws-wire-options="getCities"', false);
});

test('data-ws-wire-options-params with array form', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" :wire-options="$wireOptions" />', [
            'wireOptions' => ['getCities', ['ws-wire' => 'country']],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options="getCities"');
    expect(htmlGetJsonAttribute($html, 'data-ws-wire-options-params'))->toBe([['ws-wire' => 'country']]);
});

test('no wire-options attributes when null', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-wire-options');
});

// --- wire-options-watch ---

test('data-ws-wire-options-watch rendered with string', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" wire-options-watch="country" />')
        ->assertSee('data-ws-wire-options-watch="country"', false);
});

test('data-ws-wire-options-reset rendered with array form', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" :wire-options-watch="$watch" />', [
            'watch' => ['country', null],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options-watch="country"');
    expect($html)->toContain('data-ws-wire-options-reset="null"');
});

test('data-ws-wire-options-reset-live rendered', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" :wire-options-watch="$watch" />', [
            'watch' => ['country', null, true],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options-reset-live="true"');
});

test('no wire-options-watch attributes when null', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-wire-options-watch');
    expect($html)->not->toContain('data-ws-wire-options-reset');
});

// --- ARIA ---

test('input has aria-controls {id}-listbox', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->assertSee('aria-controls="test-listbox"', false);
});

test('aria-owns rendered when teleport is set', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" teleport="body" />')
        ->assertSee('aria-owns="test-listbox"', false);
});

test('no aria-owns without teleport', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" />')
        ->__toString();

    expect($html)->not->toContain('aria-owns');
});

// --- Auto-resolved ID ---

test('id auto-resolved from wire:model', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete wire-options="getItems" wire:model="user.email" />')
        ->assertSee('id="user-email"', false);
});

// --- Disabled ---

test('disabled attribute rendered on root and input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" disabled />')
        ->__toString();

    expect(htmlCountAttribute($html, 'disabled'))->toBeGreaterThanOrEqual(2);
});

// --- Validation ---

test('invalid class on input when error in single mode', function () {
    $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" />')
        ->assertSee('ws-autocomplete-input ws-form-invalid', false);
});

test('invalid class on wrapper when error in multiple mode', function () {
    $this->withViewErrors(['tags' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="tags" wire-options="getItems" multiple />')
        ->assertSee('ws-autocomplete-input-wrapper ws-form-invalid', false);
});

test('invalid class on autocomplete root when non-floating error', function () {
    $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" />')
        ->assertSee('ws-autocomplete ws-form-invalid', false);
});

test('no invalid class when no error', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('validation message rendered when has error', function () {
    $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" />')
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
});

test('no validation message when has-validation-message is false', function () {
    $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" :has-validation-message="false" />')
        ->assertDontSee('ws-form-feedback-invalid', false);
});

test('no invalid class when has-validation is false', function () {
    $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" :has-validation="false" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('floating error message renders outside wrapper', function () {
    $html = $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" floating label="City" />')
        ->__toString();

    // Feedback must NOT be inside the floating wrapper
    expect(htmlContainsInside($html, 'ws-form-floating', 'ws-form-feedback-invalid'))->toBeFalse();
});

test('non-floating error message renders inside wrapper', function () {
    $html = $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" />')
        ->__toString();

    // Feedback must be inside the root wrapper
    expect(htmlContainsInside($html, null, 'ws-form-feedback-invalid'))->toBeTrue();
});

test('floating with error: invalid class on floating wrapper', function () {
    $html = $this->withViewErrors(['city' => 'Required'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="city" wire-options="getItems" floating label="City" />')
        ->__toString();

    expect(htmlGetAttribute($html, 'ws-form-floating', 'class'))->toContain('ws-form-invalid');
});

// --- Invalid indices (multiple mode) ---

test('data-ws-invalid-indices rendered for per-item errors', function () {
    $html = $this->withViewErrors(['tags.1' => 'Invalid tag'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="tags" wire-options="getItems" multiple />')
        ->__toString();

    expect($html)->toContain('data-ws-invalid-indices="[1]"');
});

test('no data-ws-invalid-indices when no per-item errors', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="tags" wire-options="getItems" multiple />')
        ->__toString();

    expect($html)->not->toContain('data-ws-invalid-indices');
});

test('per-item error triggers invalid state even without root error', function () {
    $this->withViewErrors(['tags.0' => 'Bad tag'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="tags" wire-options="getItems" multiple />')
        ->assertSee('ws-form-invalid', false);
});

test('per-item error message is shown as feedback', function () {
    $this->withViewErrors(['tags.0' => 'Bad tag'])
        ->blade('<x-wirestrap::autocomplete id="test" wire:model="tags" wire-options="getItems" multiple />')
        ->assertSee('Bad tag');
});

// --- Default attributes ---

test('default-attributes are merged onto input', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::autocomplete id="test" wire-options="getItems" :default-attributes="[\'data-custom\' => \'val\']" />')
        ->assertSee('data-custom="val"', false);
});
