<?php

// --- Data attributes ---

test('static mode renders data-ws-count', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="500" />')
        ->assertSee('data-ws-count="500"', false)
        ->assertDontSee('x-effect', false);
});

test('reactive mode renders x-effect without data-ws-count', function () {
    $this->blade('<x-wirestrap::counter id="c" count="$wire.total" />')
        ->assertSee('x-effect="animate($wire.total)"', false)
        ->assertDontSee('data-ws-count', false);
});

test('renders data-ws-decimals from prop', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" :decimals="2" />')
        ->assertSee('data-ws-decimals="2"', false);
});

test('data-ws-decimals defaults to config value', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" />')
        ->assertSee('data-ws-decimals="0"', false);
});

test('renders data-ws-duration from prop', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" :duration="500" />')
        ->assertSee('data-ws-duration="500"', false);
});

test('data-ws-duration defaults to config value', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" />')
        ->assertSee('data-ws-duration="1000"', false);
});

// --- Alpine integration ---

test('renders x-data wsCounter on root', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" />')
        ->assertSee('x-data="wsCounter"', false);
});

test('renders x-cloak on root', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" />')
        ->assertSee('x-cloak', false);
});

// --- Livewire integration ---

test('inner span has wire:ignore', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" />')
        ->assertSee('wire:ignore', false);
});

// --- Root element ---

test('renders id on root element', function () {
    $this->blade('<x-wirestrap::counter id="my-counter" :count="5" />')
        ->assertSee('id="my-counter"', false);
});

test('forwards custom class to root element', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" class="text-lg" />')
        ->assertSee('text-lg', false);
});

test('forwards extra attributes to root element', function () {
    $this->blade('<x-wirestrap::counter id="c" :count="5" data-custom="value" />')
        ->assertSee('data-custom="value"', false);
});
