<?php

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

test('teleport without id throws MissingComponentIdException', function () {
    $this->blade('
        <x-wirestrap::tooltip content="Hello" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ');
})->throws(\Illuminate\View\ViewException::class, 'Missing ID');

test('renders data-ws-float-for when teleported', function () {
    $this->blade('
        <x-wirestrap::tooltip id="test" content="Hello" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::tooltip>
    ')
    ->assertSee('data-ws-float-for="test"', false);
});
