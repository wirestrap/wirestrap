<?php

// --- Livewire attributes ---

test('renders unique wire:key on each item', function () {
    $rendered = $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::accordion>
    ');

    expect($rendered)->toHaveUniqueWireKeys();
});

test('uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">content</x-slot:one>
        </x-wirestrap::accordion>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Alpine data attributes ---

test('data-ws-single is true when single prop is set', function () {
    $this->blade('
        <x-wirestrap::accordion id="test" single>
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-single="true"', false);
});

test('data-ws-single defaults to false', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-single="false"', false);
});

test('data-ws-events is true when events prop is set', function () {
    $this->blade('
        <x-wirestrap::accordion id="test" events>
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-events="true"', false);
});

test('data-ws-events defaults to false', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-events="false"', false);
});

test('data-ws-invalid-class is present when invalid-feedback is enabled', function () {
    $this->blade('
        <x-wirestrap::accordion id="test" invalid-feedback>
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-invalid-class=', false);
});

test('data-ws-invalid-class is absent when invalid-feedback is disabled', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertDontSee('data-ws-invalid-class', false);
});

// --- Root element ---

test('renders id on root element', function () {
    $this->blade('
        <x-wirestrap::accordion id="my-acc">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('id="my-acc"', false);
});

test('forwards custom class to root element', function () {
    $this->blade('
        <x-wirestrap::accordion id="test" class="custom-class">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('custom-class', false);
});

test('forwards extra attributes to root element', function () {
    $this->blade('
        <x-wirestrap::accordion id="test" data-custom="value">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-custom="value"', false);
});

// --- Slot rendering ---

test('renders label text in button', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="My Label">content</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('My Label');
});

test('falls back to slot key when label is omitted', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:shipping>content</x-slot:shipping>
        </x-wirestrap::accordion>
    ')
    ->assertSee('shipping');
});

test('label slot overrides label attribute', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:item label="Plain">content</x-slot:item>
            <x-slot:label_item><strong>Rich label</strong></x-slot:label_item>
        </x-wirestrap::accordion>
    ')
    ->assertSee('<strong>Rich label</strong>', false)
    ->assertDontSee('Plain');
});

test('renders icon with correct class', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One" icon="bi-star">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('bi-star', false);
});

test('icon-placement end renders icon after label', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="My Label" icon="bi-gear" icon-placement="end">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSeeInOrder(['My Label', 'bi-gear'], false);
});

test('icon-placement start renders icon before label', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="My Label" icon="bi-star">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSeeInOrder(['bi-star', 'My Label'], false);
});

test('does not render icon element when icon is not set', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertDontSee('<i ', false);
});

// --- Initial state markers ---

test('open item has show class and data-ws-open', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One" open>content</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('data-ws-open="true"', false)
    ->assertSee('show', false);
});

test('closed item has display none and no data-ws-open', function () {
    $this->blade('
        <x-wirestrap::accordion id="test">
            <x-slot:one label="One">content</x-slot:one>
        </x-wirestrap::accordion>
    ')
    ->assertSee('style="display: none"', false)
    ->assertDontSee('data-ws-open', false);
});
