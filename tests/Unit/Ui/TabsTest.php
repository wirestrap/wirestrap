<?php

// --- Livewire attributes ---

test('renders unique wire:key on each nav item and panel', function () {
    $rendered = $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::tabs>
    ');

    expect($rendered)->toHaveUniqueWireKeys();
});

test('uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">content</x-slot:one>
        </x-wirestrap::tabs>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Alpine data attributes ---

test('data-ws-default is first tab key when default prop is omitted', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-default="alpha"', false);
});

test('data-ws-default reflects default prop', function () {
    $this->blade('
        <x-wirestrap::tabs id="test" default="beta">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-default="beta"', false);
});

test('invalid default prop falls back to first tab', function () {
    $this->blade('
        <x-wirestrap::tabs id="test" default="nonexistent">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-default="alpha"', false);
});

test('data-ws-events is true when events prop is set', function () {
    $this->blade('
        <x-wirestrap::tabs id="test" events>
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-events="true"', false);
});

test('data-ws-events defaults to false', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-events="false"', false);
});

test('data-ws-invalid-class is present when invalid-feedback is enabled', function () {
    $this->blade('
        <x-wirestrap::tabs id="test" invalid-feedback>
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('data-ws-invalid-class=', false);
});

test('data-ws-invalid-class is absent when invalid-feedback is disabled', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertDontSee('data-ws-invalid-class', false);
});

// --- Root element ---

test('renders id on root element', function () {
    $this->blade('
        <x-wirestrap::tabs id="my-tabs">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('id="my-tabs"', false);
});

// --- ARIA ---

test('tab buttons have role=tab', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('role="tab"', false);
});

test('tab panels have role=tabpanel', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('role="tabpanel"', false);
});

test('nav has role=tablist', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('role="tablist"', false);
});

test('aria-controls links button to panel', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('aria-controls="ws-tabs-test-panel-one"', false);
});

test('aria-labelledby links panel to button', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('aria-labelledby="ws-tabs-test-btn-one"', false);
});

// --- Slot rendering ---

test('renders label text in button', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="My Label">content</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('My Label');
});

test('falls back to slot key when label is omitted', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:shipping>content</x-slot:shipping>
        </x-wirestrap::tabs>
    ')
    ->assertSee('shipping');
});

test('label slot overrides label attribute', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:item label="Plain">content</x-slot:item>
            <x-slot:label_item><strong>Rich label</strong></x-slot:label_item>
        </x-wirestrap::tabs>
    ')
    ->assertSee('<strong>Rich label</strong>', false)
    ->assertDontSee('Plain');
});

test('renders icon with correct class', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One" icon="bi-star">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSee('bi-star', false);
});

test('icon-placement start renders icon before label', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="My Label" icon="bi-star">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSeeInOrder(['bi-star', 'My Label'], false);
});

test('icon-placement end renders icon after label', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="My Label" icon="bi-gear" icon-placement="end">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertSeeInOrder(['My Label', 'bi-gear'], false);
});

test('does not render icon element when icon is not set', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
        </x-wirestrap::tabs>
    ')
    ->assertDontSee('<i ', false);
});

test('actions slot renders in nav bar', function () {
    $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:one label="One">c</x-slot:one>
            <x-slot:actions><button id="my-action">Export</button></x-slot:actions>
        </x-wirestrap::tabs>
    ')
    ->assertSee('id="my-action"', false)
    ->assertSee('ws-tabs-nav-actions', false);
});

// --- Initial state ---

test('default tab panel is visible, others are hidden', function () {
    $rendered = $this->blade('
        <x-wirestrap::tabs id="test">
            <x-slot:alpha label="Alpha">a</x-slot:alpha>
            <x-slot:beta label="Beta">b</x-slot:beta>
        </x-wirestrap::tabs>
    ')->__toString();

    // Alpha (default/first) should not have the hidden class
    expect(htmlGetAttribute($rendered, 'id=ws-tabs-test-panel-alpha', 'class'))->not->toContain('ws-tabs-panel--hidden');

    // Beta should have the hidden class
    expect(htmlGetAttribute($rendered, 'id=ws-tabs-test-panel-beta', 'class'))->toContain('ws-tabs-panel--hidden');
});
