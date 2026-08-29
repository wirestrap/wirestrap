<?php

// --- resolveFormField ---

$formComponents = [
    'input' => '<x-wirestrap::input wire:model="user.email" />',
    'textarea' => '<x-wirestrap::textarea wire:model="user.email" />',
    'autocomplete' => '<x-wirestrap::autocomplete wire:model="user.email" />',
];

dataset('form components', $formComponents);

test('auto id is resolved from wire:model', function (string $blade) {
    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="user-email"', false);
})->with('form components');

test('explicit id takes precedence over wire:model', function (string $blade) {
    $blade = str_replace('wire:model="user.email"', 'id="custom" wire:model="user.email"', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="custom"', false)
        ->assertDontSee('id="user-email"', false);
})->with('form components');

$formComponentsNoModel = [
    'input' => '<x-wirestrap::input />',
    'textarea' => '<x-wirestrap::textarea />',
    'autocomplete' => '<x-wirestrap::autocomplete />',
];

dataset('form components without model', $formComponentsNoModel);

test('no id rendered when no id and no wire:model', function (string $blade) {
    $rendered = (string) $this->withViewErrors([])
        ->blade($blade);

    expect($rendered)->not->toContain(' id="');
})->with('form components without model');

// --- resolveCheckField ---

$checkComponents = [
    'checkbox' => '<x-wirestrap::checkbox wire:model="terms" />',
    'radio' => '<x-wirestrap::radio wire:model="terms" />',
    'switch' => '<x-wirestrap::switch wire:model="terms" />',
];

dataset('check components', $checkComponents);

test('check auto id is resolved from wire:model', function (string $blade) {
    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="terms"', false);
})->with('check components');

test('check auto id appends value when present', function (string $blade) {
    $blade = str_replace('/>', 'value="yes" />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="terms-yes"', false);
})->with('check components');

test('check explicit id takes precedence over wire:model', function (string $blade) {
    $blade = str_replace('wire:model="terms"', 'id="custom" wire:model="terms"', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="custom"', false)
        ->assertDontSee('id="terms"', false);
})->with('check components');

// --- wire:model modifiers ---

test('wire:model.live resolves id correctly', function (string $blade) {
    $blade = str_replace('wire:model="user.email"', 'wire:model.live="user.email"', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="user-email"', false);
})->with('form components');

test('wire:model.blur resolves id correctly', function (string $blade) {
    $blade = str_replace('wire:model="user.email"', 'wire:model.blur="user.email"', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="user-email"', false);
})->with('form components');

// --- Deeply nested dots ---

test('deeply nested wire:model dots are replaced with dashes', function (string $blade) {
    $blade = str_replace('wire:model="user.email"', 'wire:model="form.address.city"', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('id="form-address-city"', false);
})->with('form components');

// --- Label for attribute ---

test('label for attribute matches resolved auto id', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::input wire:model="user.name" label="Name" />')
        ->assertSee('for="user-name"', false)
        ->assertSee('id="user-name"', false);
});
