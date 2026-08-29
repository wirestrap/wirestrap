<?php

// --- CSS classes ---

test('renders ws-popover class on floatable', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('ws-popover', false);
});

test('renders ws-popover-header class', function () {
    $this->blade('
        <x-wirestrap::popover id="test" header="Title" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('ws-popover-header', false);
});

test('renders ws-popover-body class', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('ws-popover-body', false);
});

test('renders ws-popover-arrow class when arrow enabled', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('ws-popover-arrow', false);
});

// --- Floating attributes ---

test('renders data-ws-floatable on floatable', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-floatable', false);
});

test('renders data-ws-float-trigger on trigger wrapper', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-float-trigger', false);
});

test('renders data-ws-float-id with id value', function () {
    $this->blade('
        <x-wirestrap::popover id="my-pop" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-float-id="my-pop"', false);
});

// --- Default prop values ---

test('default placement is top', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-placement="top"', false);
});

test('default trigger is hover', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-trigger="hover"', false);
});

test('default offset-distance is 8', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-offset-distance="8"', false);
});

// --- Trigger slot ---

test('trigger slot content rendered inside trigger wrapper', function () {
    $html = $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">My Trigger</button>
        </x-wirestrap::popover>
    ')->__toString();

    expect(htmlContainsInside($html, null, 'data-ws-float-trigger'))->toBeTrue();
    expect(htmlContainsInside($html, null, 'My Trigger'))->toBeTrue();
});

// --- Slot attributes ---

test('header slot class is merged on header div', function () {
    $html = $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <x-slot:header class="custom-header">Title</x-slot:header>
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')->__toString();

    expect(htmlGetAttribute($html, 'ws-popover-header', 'class'))->toContain('custom-header');
});

test('content slot class is merged on body div', function () {
    $html = $this->blade('
        <x-wirestrap::popover id="test">
            <x-slot:content class="custom-body">Rich body</x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')->__toString();

    expect(htmlGetAttribute($html, 'ws-popover-body', 'class'))->toContain('custom-body');
});

// --- Livewire attributes ---

test('uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::popover id="test" header="Title" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Data attributes ---

test('renders data-ws-placement from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" placement="bottom">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-placement="bottom"', false);
});

test('renders data-ws-trigger from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" trigger="click">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-trigger="click"', false);
});

test('renders data-ws-interactive by default', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-interactive', false);
});

test('does not render data-ws-interactive when interactive is false', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" :interactive="false">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertDontSee('data-ws-interactive', false);
});

// --- Structure ---

test('renders id on wrapper', function () {
    $this->blade('
        <x-wirestrap::popover id="my-pop" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('id="my-pop"', false);
});

test('renders arrow by default', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-arrow', false);
});

test('hides arrow when arrow is false', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" :arrow="false">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertDontSee('data-ws-arrow', false);
});

test('floatable is hidden by default', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('style="display: none"', false);
});

// --- Content rendering ---

test('renders header from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" header="My Header" content="Body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('My Header');
});

test('renders header from slot', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body">
            <x-slot:header><em>Rich header</em></x-slot:header>
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('<em>Rich header</em>', false);
});

test('renders content from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Popover body text">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('Popover body text');
});

test('renders content from slot', function () {
    $this->blade('
        <x-wirestrap::popover id="test">
            <x-slot:content>Rich <strong>body</strong></x-slot:content>
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('<strong>body</strong>', false);
});

// --- Offset and position ---

test('renders data-ws-offset-distance from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" :offset-distance="12">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-offset-distance="12"', false);
});

test('renders data-ws-offset-skidding from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" :offset-skidding="5">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-offset-skidding="5"', false);
});

test('renders data-ws-position from prop', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" position="fixed">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-position="fixed"', false);
});

// --- Teleport ---

test('teleport without id throws MissingComponentIdException', function () {
    $this->blade('
        <x-wirestrap::popover content="Body" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ');
})->throws(\Illuminate\View\ViewException::class, 'Missing ID');

test('renders data-ws-float-for when teleported', function () {
    $this->blade('
        <x-wirestrap::popover id="test" content="Body" teleport="body">
            <button type="button">Trigger</button>
        </x-wirestrap::popover>
    ')
    ->assertSee('data-ws-float-for="test"', false);
});
