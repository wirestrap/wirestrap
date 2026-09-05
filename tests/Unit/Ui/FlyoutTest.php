<?php

// --- CSS classes ---

test('renders ws-flyout class on floatable', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('ws-flyout', false);
});

// --- Floating attributes ---

test('renders data-ws-floatable on floatable', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-floatable', false);
});

test('renders data-ws-float-trigger on trigger wrapper', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-float-trigger', false);
});

test('renders data-ws-float-id with id value', function () {
    $this->blade('
        <x-wirestrap::flyout id="my-fly" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-float-id="my-fly"', false);
});

// --- Default prop values ---

test('default placement is bottom-start', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-placement="bottom-start"', false);
});

test('default trigger is hover', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-trigger="hover"', false);
});

test('default offset-distance is 0', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-offset-distance="0"', false);
});

// --- Trigger slot ---

test('trigger slot content rendered inside trigger wrapper', function () {
    $html = $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">My Trigger</button>
        </x-wirestrap::flyout>
    ')->__toString();

    expect(htmlContainsInside($html, null, 'data-ws-float-trigger'))->toBeTrue();
    expect(htmlContainsInside($html, null, 'My Trigger'))->toBeTrue();
});

// --- Content slot attributes ---

test('content slot class is merged on floatable', function () {
    $html = $this->blade('
        <x-wirestrap::flyout id="test">
            <x-slot:content class="extra-class">Some content</x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')->__toString();

    expect(htmlGetAttribute($html, 'ws-flyout', 'class'))->toContain('extra-class');
});

// --- Livewire attributes ---

test('uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Data attributes ---

test('renders data-ws-placement from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" placement="top">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-placement="top"', false);
});

test('renders data-ws-trigger from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" trigger="click">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-trigger="click"', false);
});

test('renders data-ws-interactive by default', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-interactive', false);
});

test('does not render data-ws-interactive when interactive is false', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" :interactive="false">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertDontSee('data-ws-interactive', false);
});

// --- Structure ---

test('renders id on wrapper', function () {
    $this->blade('
        <x-wirestrap::flyout id="my-fly" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('id="my-fly"', false);
});

test('has no arrow element', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertDontSee('data-ws-arrow', false);
});

test('floatable is hidden by default', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('style="display: none;', false);
});

// --- Content rendering ---

test('renders content from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Flyout text">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('Flyout text');
});

test('renders content from slot', function () {
    $this->blade('
        <x-wirestrap::flyout id="test">
            <x-slot:content>Rich <strong>flyout</strong></x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('<strong>flyout</strong>', false);
});

// --- Offset and position ---

test('renders data-ws-offset-distance from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" :offset-distance="12">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-offset-distance="12"', false);
});

test('renders data-ws-offset-skidding from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" :offset-skidding="5">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-offset-skidding="5"', false);
});

test('renders data-ws-position from prop', function () {
    $this->blade('
        <x-wirestrap::flyout id="test" content="Hello" position="fixed">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-position="fixed"', false);
});

// --- Teleport ---

test('teleport renders and marks the panel as teleported', function () {
    $this->blade('
        <x-wirestrap::flyout content="Hello" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertSee('data-ws-teleported', false);
});

test('no data-ws-teleported without teleport', function () {
    $this->blade('
        <x-wirestrap::flyout content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::flyout>
    ')
    ->assertDontSee('data-ws-teleported', false);
});
