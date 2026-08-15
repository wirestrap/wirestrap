<?php

// --- Element ---

test('renders a textarea element', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertSee('<textarea', false);
});

test('textarea has ws-form-textarea class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertSee('ws-form-textarea', false);
});

// --- Label ---

test('renders label from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea label="Description" />')
        ->assertSee('ws-form-label', false)
        ->assertSee('Description');
});

test('renders label from slot', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea><x-slot:label>Rich <strong>label</strong></x-slot:label></x-wirestrap::textarea>')
        ->assertSee('<strong>label</strong>', false);
});

test('no label element when label is absent', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertDontSee('ws-form-label', false);
});

test('label for attribute matches resolved id', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea id="desc" label="Description" />')
        ->assertSee('for="desc"', false);
});

test('non-floating label renders before textarea', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea id="x" label="Name" />')
        ->__toString();

    $labelPos = strpos($html, 'ws-form-label');
    $textareaPos = strpos($html, '<textarea');

    expect($labelPos)->toBeLessThan($textareaPos);
});

// --- Floating ---

test('floating adds ws-form-floating class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating label="Bio" />')
        ->assertSee('ws-form-floating', false);
});

test('no floating class by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertDontSee('ws-form-floating', false);
});

test('floating label renders after textarea', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating id="x" label="Bio" />')
        ->__toString();

    $textareaPos = strpos($html, '<textarea');
    $labelPos = strpos($html, 'ws-form-label');

    expect($textareaPos)->toBeLessThan($labelPos);
});

test('floating sets placeholder to space when none given', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating label="Bio" />')
        ->assertSee('placeholder=" "', false);
});

test('floating preserves explicit placeholder', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating label="Bio" placeholder="Enter bio" />')
        ->assertSee('placeholder="Enter bio"', false);
});

// --- Placeholder ---

test('placeholder is rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea placeholder="Type here" />')
        ->assertSee('placeholder="Type here"', false);
});

test('no placeholder attribute by default', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->__toString();

    expect($html)->not->toContain('placeholder=');
});

// --- Icon ---

test('icon renders icon element', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" />')
        ->assertSee('ws-form-input-icon', false);
});

test('icon start placement adds class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" />')
        ->assertSee('ws-form-has-icon-start', false)
        ->assertSee('ws-form-input-icon-start', false);
});

test('icon end placement adds class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" icon-placement="end" />')
        ->assertSee('ws-form-has-icon-end', false)
        ->assertSee('ws-form-input-icon-end', false);
});

test('icon has aria-hidden', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" />')
        ->assertSee('aria-hidden="true"', false);
});

test('non-floating icon wraps in textarea-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" />')
        ->assertSee('ws-form-textarea-wrapper', false);
});

test('floating icon does not use textarea-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating icon="bi-chat" label="Bio" />')
        ->assertDontSee('ws-form-textarea-wrapper', false);
});

test('floating with icon adds icon class to wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating icon="bi-chat" label="Bio" />')
        ->assertSee('ws-form-floating ws-form-has-icon-start', false);
});

// --- Rows ---

test('rows attribute is rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea :rows="5" />')
        ->assertSee('rows="5"', false);
});

test('rows defaults to config value', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertSee('rows="3"', false);
});

// --- Autosize ---

test('autosize adds x-data and x-bind', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertSee('x-data="wsTextarea"', false)
        ->assertSee('x-bind="textarea"', false);
});

test('autosize adds data-ws-wiremodel when wire:model present', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea wire:model="field" />')
        ->assertSee('data-ws-wiremodel="field"', false);
});

test('autosize false removes x-data and x-bind', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea :autosize="false" />')
        ->__toString();

    expect($html)->not->toContain('x-data="wsTextarea"');
    expect($html)->not->toContain('x-bind="textarea"');
});

test('autosize false does not add data-ws-wiremodel', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea :autosize="false" wire:model="field" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-wiremodel');
});

// --- wire:ignore.self placement ---

test('autosize places wire:ignore.self on outer wrapper without icon', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea />')
        ->assertSee('wire:ignore.self', false);
});

test('autosize with non-floating icon places wire:ignore.self on inner wrapper', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea icon="bi-chat" />')
        ->__toString();

    $innerWrapperPos = strpos($html, 'ws-form-textarea-wrapper');
    $wireIgnorePos = strpos($html, 'wire:ignore.self');

    // wire:ignore.self should be on the inner wrapper (textarea-wrapper), not the outer div
    expect($wireIgnorePos)->toBeGreaterThan($innerWrapperPos);
});

test('autosize with floating icon places wire:ignore.self on outer wrapper', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea floating icon="bi-chat" label="Bio" />')
        ->__toString();

    $outerDivPos = strpos($html, '<div');
    $wireIgnorePos = strpos($html, 'wire:ignore.self');

    // wire:ignore.self should be near the outer div
    expect($wireIgnorePos)->toBeGreaterThan($outerDivPos);
    expect($html)->not->toContain('ws-form-textarea-wrapper');
});

test('no wire:ignore.self when autosize is false', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea :autosize="false" />')
        ->__toString();

    expect($html)->not->toContain('wire:ignore.self');
});

// --- Disabled ---

test('disabled attribute is rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea disabled />')
        ->assertSee('disabled', false);
});

// --- Validation ---

test('invalid class applied when wire:model has error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" />')
        ->assertSee('ws-form-invalid', false);
});

test('no invalid class when no error', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea wire:model="field" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('validation message rendered when has error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" />')
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
});

test('no validation message when has-validation-message is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" :has-validation-message="false" />')
        ->assertDontSee('ws-form-feedback-invalid', false);
});

test('no invalid class when has-validation is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" :has-validation="false" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('floating error message renders outside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" floating label="Bio" />')
        ->__toString();

    $closingDivPos = strrpos($html, '</div>', strrpos($html, 'ws-form-floating') - strlen($html));
    $feedbackPos = strrpos($html, 'ws-form-feedback-invalid');

    expect($feedbackPos)->toBeGreaterThan($closingDivPos);
});

test('non-floating error message renders inside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" />')->__toString();

    $feedbackPos = strpos($html, 'ws-form-feedback-invalid');
    $lastClosingDivPos = strrpos($html, '</div>');

    expect($feedbackPos)->toBeLessThan($lastClosingDivPos);
});

test('non-floating icon with error adds invalid class to inner wrapper', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::textarea wire:model="field" icon="bi-chat" />')
        ->assertSee('ws-form-textarea-wrapper ws-form-invalid', false);
});

// --- Default attributes ---

test('default-attributes are merged onto textarea', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::textarea :default-attributes="[\'data-custom\' => \'val\']" />')
        ->assertSee('data-custom="val"', false);
});
