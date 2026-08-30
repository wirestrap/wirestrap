<?php

// --- Element ---

test('renders an input element', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertSee('<input', false);
});

test('input has ws-form-input class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertSee('ws-form-input', false);
});

// --- Type ---

test('type defaults to text', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertSee('type="text"', false);
});

test('type prop is rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input type="email" />')
        ->assertSee('type="email"', false);
});

test('password overrides type to password', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password type="email" />')
        ->assertSee('type="password"', false);
});

// --- Label ---

test('renders label from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input label="Email" />')
        ->assertSee('ws-form-label', false)
        ->assertSee('Email');
});

test('renders label from slot', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input><x-slot:label>Rich <strong>label</strong></x-slot:label></x-wirestrap::input>')
        ->assertSee('<strong>label</strong>', false);
});

test('no label element when label is absent', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertDontSee('ws-form-label', false);
});

test('label for attribute matches resolved id', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input id="email" label="Email" />')
        ->assertSee('for="email"', false);
});

test('non-floating label renders before input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::input id="x" label="Name" />')
        ->__toString();

    $labelPos = strpos($html, 'ws-form-label');
    $inputPos = strpos($html, '<input');

    expect($labelPos)->toBeLessThan($inputPos);
});

// --- Floating ---

test('floating adds ws-form-floating class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating label="Email" />')
        ->assertSee('ws-form-floating', false);
});

test('no floating class by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertDontSee('ws-form-floating', false);
});

test('floating label renders after input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating id="x" label="Email" />')
        ->__toString();

    $inputPos = strpos($html, '<input');
    $labelPos = strpos($html, 'ws-form-label');

    expect($inputPos)->toBeLessThan($labelPos);
});

test('floating sets placeholder to space when none given', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating label="Email" />')
        ->assertSee('placeholder=" "', false);
});

test('floating preserves explicit placeholder', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating label="Email" placeholder="you@example.com" />')
        ->assertSee('placeholder="you@example.com"', false);
});

// --- Placeholder ---

test('placeholder is rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input placeholder="Type here" />')
        ->assertSee('placeholder="Type here"', false);
});

test('no placeholder attribute by default', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->__toString();

    expect($html)->not->toContain('placeholder=');
});

// --- Icon ---

test('icon renders icon element', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input icon="bi-search" />')
        ->assertSee('ws-form-input-icon', false);
});

test('icon start placement adds class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input icon="bi-search" />')
        ->assertSee('ws-form-has-icon-start', false)
        ->assertSee('ws-form-input-icon-start', false);
});

test('icon end placement adds class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input icon="bi-search" icon-placement="end" />')
        ->assertSee('ws-form-has-icon-end', false)
        ->assertSee('ws-form-input-icon-end', false);
});

test('icon has aria-hidden', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input icon="bi-search" />')
        ->assertSee('aria-hidden="true"', false);
});

test('non-floating icon wraps in input-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input icon="bi-search" />')
        ->assertSee('ws-form-input-wrapper', false);
});

test('floating icon does not use input-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating icon="bi-search" label="Search" />')
        ->assertDontSee('ws-form-input-wrapper', false);
});

test('floating with icon adds icon class to wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input floating icon="bi-search" label="Search" />')
        ->assertSee('ws-form-floating ws-form-has-icon-start', false);
});

// --- Password toggle (HTML structure) ---

test('password adds x-data wsInput', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('x-data="wsInput"', false);
});

test('password renders toggle button', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('ws-form-input-password-toggle', false);
});

test('password toggle button has x-bind', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('x-bind="passwordToggle"', false);
});

test('password input has x-bind input', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('x-bind="input"', false);
});

test('password renders show and hide icons', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('ws-password-show-icon', false)
        ->assertSee('ws-password-hide-icon', false);
});

test('password renders data-ws-label attributes', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('data-ws-label-show="Show password"', false)
        ->assertSee('data-ws-label-hide="Hide password"', false);
});

test('password adds ws-form-has-icon-end class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('ws-form-has-icon-end', false);
});

test('password wraps in input-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input password />')
        ->assertSee('ws-form-input-wrapper', false);
});

test('no password toggle by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input />')
        ->assertDontSee('ws-form-input-password-toggle', false);
});

// --- Disabled ---

test('disabled attribute is rendered on input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::input disabled />')
        ->__toString();

    expect($html)->toHaveAttributeOn('ws-form-input', 'disabled');
});

// --- Validation ---

test('invalid class applied when wire:model has error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" />')
        ->assertSee('ws-form-invalid', false);
});

test('no invalid class when no error', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input wire:model="field" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('validation message rendered when has error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" />')
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
});

test('no validation message when has-validation-message is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" :has-validation-message="false" />')
        ->assertDontSee('ws-form-feedback-invalid', false);
});

test('no invalid class when has-validation is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" :has-validation="false" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('floating error message renders outside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" floating label="Email" />')
        ->__toString();

    // Feedback must NOT be inside the floating wrapper
    expect(htmlContainsInside($html, 'ws-form-floating', 'ws-form-feedback-invalid'))->toBeFalse();
});

test('non-floating error message renders inside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" />')
        ->__toString();

    // Feedback must be inside the root wrapper
    expect(htmlContainsInside($html, null, 'ws-form-feedback-invalid'))->toBeTrue();
});

test('non-floating icon with error adds invalid class to inner wrapper', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::input wire:model="field" icon="bi-search" />')
        ->assertSee('ws-form-input-wrapper ws-form-invalid', false);
});

// --- Default attributes ---

test('default-attributes are merged onto input', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input :default-attributes="[\'data-custom\' => \'val\']" />')
        ->assertSee('data-custom="val"', false);
});

test('custom class goes to the wrapper, not to the input', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::input class="my-wrapper" />')
        ->__toString();

    expect(htmlGetAttribute($html, 'my-wrapper', 'class'))->toBe('my-wrapper');

    // The input must carry a single class attribute: its own.
    preg_match('/<input\b[^>]*>/', $html, $matches);
    expect(substr_count($matches[0], 'class='))->toBe(1)
        ->and($matches[0])->not->toContain('my-wrapper');
});
