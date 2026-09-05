<?php

// --- CSS classes ---

test('renders ws-tooltip class on floatable', function () {
    $html = $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')->__toString();

    // Token match: a substring assertion would also match ws-tooltip-content
    // and ws-tooltip-arrow.
    expect(htmlHasClass($html, 'ws-tooltip'))->toBeTrue();
});

test('renders ws-tooltip-content class', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('ws-tooltip-content', false);
});

test('renders ws-tooltip-arrow class when arrow enabled', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('ws-tooltip-arrow', false);
});

// --- Floating attributes ---

test('renders data-ws-floatable on floatable', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-floatable', false);
});

test('renders data-ws-float-trigger on trigger wrapper', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-float-trigger', false);
});

test('renders data-ws-float-id with id value', function () {
    $this->blade('
        <x-wirestrap::tooltip id="my-tip" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-float-id="my-tip"', false);
});

// --- Default prop values ---

test('default placement is top', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-placement="top"', false);
});

test('default trigger is hover', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-trigger="hover"', false);
});

test('default offset-distance is 8', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-offset-distance="8"', false);
});

// --- Trigger slot ---

test('trigger slot content rendered inside trigger wrapper', function () {
    $html = $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">My Trigger</button>
        </x-wirestrap::tooltip>
    ')->__toString();

    expect(htmlContainsInside($html, null, 'data-ws-float-trigger'))->toBeTrue();
    expect(htmlContainsInside($html, null, 'My Trigger'))->toBeTrue();
});

// --- Content slot attributes ---

test('content slot class is merged on floatable', function () {
    $html = $this->blade('
        <x-wirestrap::tooltip id="test">
            <x-slot:content class="extra-class">Some tip</x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')->__toString();

    expect(htmlGetAttribute($html, 'ws-tooltip', 'class'))->toContain('extra-class');
});

// --- Livewire attributes ---

test('uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Data attributes ---

test('renders data-ws-placement from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" placement="right">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-placement="right"', false);
});

test('renders data-ws-trigger from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" trigger="click">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-trigger="click"', false);
});

test('renders data-ws-interactive when interactive is true', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" :interactive="true">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-interactive', false);
});

test('does not render data-ws-interactive when interactive is false', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertDontSee('data-ws-interactive', false);
});

// --- Structure ---

test('renders id on wrapper and floatable', function () {
    $this->blade('
        <x-wirestrap::tooltip id="my-tip" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('id="my-tip"', false)
    ->assertSee('id="my-tip-tip"', false);
});

test('renders role=tooltip on floatable', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('role="tooltip"', false);
});

test('renders aria-describedby on trigger', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('aria-describedby="test-tip"', false);
});

test('renders arrow by default', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-arrow', false);
});

test('hides arrow when arrow is false', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" :arrow="false">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertDontSee('data-ws-arrow', false);
});

test('floatable is hidden by default', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('style="display: none;', false);
});

// --- Content rendering ---

test('renders content from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="My tooltip text">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('My tooltip text');
});

test('renders content from slot', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test">
            <x-slot:content>Rich <strong>content</strong></x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('<strong>content</strong>', false);
});

// --- Offset and position ---

test('renders data-ws-offset-distance from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" :offset-distance="12">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-offset-distance="12"', false);
});

test('renders data-ws-offset-skidding from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" :offset-skidding="5">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-offset-skidding="5"', false);
});

test('renders data-ws-position from prop', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" position="fixed">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-position="fixed"', false);
});

// --- Teleport ---

test('teleport renders and marks the panel as teleported', function () {
    $this->blade('
        <x-wirestrap::tooltip content="Hello" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-teleported', false);
});

test('no data-ws-teleported without teleport', function () {
    $this->blade('
        <x-wirestrap::tooltip content="Hello">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertDontSee('data-ws-teleported', false);
});
