<?php

// --- Livewire attributes ---

test('slider uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
            <x-slot:b>B</x-slot:b>
        </x-wirestrap::slider>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

test('renders unique wire:key on each element', function () {
    $rendered = $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
            <x-slot:b>B</x-slot:b>
        </x-wirestrap::slider>
    ');

    expect($rendered)->toHaveUniqueWireKeys();
});

// --- Root element ---

test('renders x-data wsSlider on root', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('x-data="wsSlider"', false);
});

test('renders id on root element', function () {
    $this->blade('
        <x-wirestrap::slider id="my-slider">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('id="my-slider"', false);
});

test('renders ws-slider class on root', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('ws-slider', false);
});

test('forwards custom class to root element', function () {
    $this->blade('
        <x-wirestrap::slider id="s" class="custom-carousel">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('custom-carousel', false);
});

test('forwards extra attributes to root element', function () {
    $this->blade('
        <x-wirestrap::slider id="s" data-custom="value">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-custom="value"', false);
});

// --- Data attributes ---

test('data-ws-scroll-by defaults to 1', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-scroll-by="1"', false);
});

test('data-ws-scroll-by reflects prop value', function () {
    $this->blade('
        <x-wirestrap::slider id="s" :scroll-by="3">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-scroll-by="3"', false);
});

test('data-ws-scroll-page defaults to false', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-scroll-page="false"', false);
});

test('data-ws-scroll-page is true when set', function () {
    $this->blade('
        <x-wirestrap::slider id="s" scroll-page>
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-scroll-page="true"', false);
});

test('data-ws-events defaults to false', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-events="false"', false);
});

test('data-ws-events is true when set', function () {
    $this->blade('
        <x-wirestrap::slider id="s" events>
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('data-ws-events="true"', false);
});

// --- Structure ---

test('renders track and inner containers', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')
        ->assertSee('ws-slider-track', false)
        ->assertSee('ws-slider-inner', false);
});

test('named slots render as slide divs', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:item_a>Alpha</x-slot:item_a>
            <x-slot:item_b>Beta</x-slot:item_b>
        </x-wirestrap::slider>
    ')
        ->assertSee('ws-slider-slide', false)
        ->assertSee('Alpha')
        ->assertSee('Beta');
});

// --- Arrows ---

test('arrows present by default', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')
        ->assertSee('ws-slider-arrow-prev', false)
        ->assertSee('ws-slider-arrow-next', false);
});

test('arrows absent when false', function () {
    $this->blade('
        <x-wirestrap::slider id="s" :arrows="false">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')
        ->assertDontSee('ws-slider-arrow-prev', false)
        ->assertDontSee('ws-slider-arrow-next', false);
});

test('prev arrow has aria-label', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('aria-label="Previous"', false);
});

test('next arrow has aria-label', function () {
    $this->blade('
        <x-wirestrap::slider id="s">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('aria-label="Next"', false);
});

// --- Columns CSS ---

test('columns CSS custom property set in style tag', function () {
    $this->blade('
        <x-wirestrap::slider id="s" :columns="4">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')->assertSee('--ws-slider-columns: 4', false);
});

test('responsive columns emit media queries', function () {
    $this->blade('
        <x-wirestrap::slider id="s" :columns="1" :columns-md="2" :columns-lg="3">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')
        ->assertSee('min-width: 768px', false)
        ->assertSee('min-width: 992px', false);
});

test('no media queries when responsive columns not set', function () {
    $this->blade('
        <x-wirestrap::slider id="s" :columns="3">
            <x-slot:a>A</x-slot:a>
        </x-wirestrap::slider>
    ')
        ->assertDontSee('min-width: 576px', false)
        ->assertDontSee('min-width: 768px', false);
});
