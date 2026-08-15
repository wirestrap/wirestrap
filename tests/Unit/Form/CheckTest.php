<?php

$components = [
    'checkbox' => '<x-wirestrap::checkbox />',
    'radio' => '<x-wirestrap::radio />',
    'switch' => '<x-wirestrap::switch />',
];

dataset('check components', $components);

// --- Input type ---

test('checkbox renders type checkbox', function () {
    $this->withViewErrors([])->blade('<x-wirestrap::checkbox />')
        ->assertSee('type="checkbox"', false);
});

test('radio renders type radio', function () {
    $this->withViewErrors([])->blade('<x-wirestrap::radio />')
        ->assertSee('type="radio"', false);
});

test('switch renders type checkbox with role switch', function () {
    $this->withViewErrors([])->blade('<x-wirestrap::switch />')
        ->assertSee('type="checkbox"', false)
        ->assertSee('role="switch"', false);
});

// --- Wrapper class ---

test('renders ws-form-check wrapper', function (string $blade) {
    $this->withViewErrors([])->blade($blade)
        ->assertSee('ws-form-check', false);
})->with('check components');

test('switch has ws-form-switch class', function () {
    $this->withViewErrors([])->blade('<x-wirestrap::switch />')
        ->assertSee('ws-form-switch', false);
});

// --- Label ---

test('renders label from prop', function (string $blade) {
    $blade = str_replace('/>', 'label="Accept" />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('ws-form-check-label', false)
        ->assertSee('Accept');
})->with('check components');

test('renders label from default slot', function (string $blade) {
    $blade = str_replace(
        '/>',
        '>Rich <strong>label</strong></x-wirestrap::' . explode('::', explode(' ', $blade)[0])[1] . '>',
        $blade
    );

    $this->withViewErrors([])->blade($blade)
        ->assertSee('<strong>label</strong>', false);
})->with('check components');

test('no label element when label is absent', function (string $blade) {
    $this->withViewErrors([])->blade($blade)
        ->assertDontSee('ws-form-check-label', false);
})->with('check components');

test('label for attribute matches resolved id', function (string $blade) {
    $blade = str_replace('/>', 'id="my-check" label="Test" />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('for="my-check"', false);
})->with('check components');

// --- Layout variants ---

test('inline adds inline class', function (string $blade) {
    $blade = str_replace('/>', 'inline />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('ws-form-check-inline', false);
})->with('check components');

test('no inline class by default', function (string $blade) {
    $this->withViewErrors([])->blade($blade)
        ->assertDontSee('ws-form-check-inline', false);
})->with('check components');

test('reverse adds reverse class', function (string $blade) {
    $blade = str_replace('/>', 'reverse />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('ws-form-check-reverse', false);
})->with('check components');

test('no reverse class by default', function (string $blade) {
    $this->withViewErrors([])->blade($blade)
        ->assertDontSee('ws-form-check-reverse', false);
})->with('check components');

// --- Disabled ---

test('disabled attribute is rendered', function (string $blade) {
    $blade = str_replace('/>', 'disabled />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('disabled', false);
})->with('check components');

// --- Validation ---

test('invalid class applied when wire:model has error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors(['field' => 'Required'])->blade($blade)
        ->assertSee('ws-form-invalid', false);
})->with('check components');

test('no invalid class when no error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertDontSee('ws-form-invalid', false);
})->with('check components');

test('validation message rendered when has error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors(['field' => 'Required'])->blade($blade)
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
})->with('check components');

test('no validation message when has-validation-message is false', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" :has-validation-message="false" />', $blade);

    $this->withViewErrors(['field' => 'Required'])->blade($blade)
        ->assertDontSee('ws-form-feedback-invalid', false);
})->with('check components');

test('no invalid class when has-validation is false', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" :has-validation="false" />', $blade);

    $this->withViewErrors(['field' => 'Required'])->blade($blade)
        ->assertDontSee('ws-form-invalid', false);
})->with('check components');

// --- Default attributes ---

test('default-attributes are merged onto input', function (string $blade) {
    $blade = str_replace('/>', ':default-attributes="[\'data-custom\' => \'val\']" />', $blade);

    $this->withViewErrors([])->blade($blade)
        ->assertSee('data-custom="val"', false);
})->with('check components');
