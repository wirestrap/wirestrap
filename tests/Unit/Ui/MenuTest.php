<?php

// --- Livewire attributes ---

test('renders unique wire:key on each element', function () {
    $rendered = $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:home><a href="#">Home</a></x-slot:home>
            <x-slot:settings_group>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:general><a href="#">General</a></x-slot:general>
                    <x-slot:security><a href="#">Security</a></x-slot:security>
                </x-wirestrap::menu.accordion>
            </x-slot:settings_group>
        </x-wirestrap::menu>
    ');

    expect($rendered)->toHaveUniqueWireKeys();
});

test('accordion uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="grp" label="Group">
                    <x-slot:item><a href="#">Item</a></x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Menu: Data attributes ---

test('data-ws-single is true when single prop is set', function () {
    $this->blade('
        <x-wirestrap::menu id="nav" :single="true">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-single="true"', false);
});

test('data-ws-single defaults to false', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-single="false"', false);
});

test('data-ws-events is true when events prop is set', function () {
    $this->blade('
        <x-wirestrap::menu id="nav" :events="true">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-events="true"', false);
});

test('data-ws-events defaults to false', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-events="false"', false);
});

// --- Menu: Root element ---

test('renders nav element with x-data wsMenu', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('x-data="wsMenu"', false);
});

test('renders id on root element', function () {
    $this->blade('
        <x-wirestrap::menu id="my-nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('id="my-nav"', false);
});

test('renders ws-menu class on root', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('ws-menu', false);
});

test('forwards custom class to root element', function () {
    $this->blade('
        <x-wirestrap::menu id="nav" class="sidebar-nav">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('sidebar-nav', false);
});

test('forwards extra attributes to root element', function () {
    $this->blade('
        <x-wirestrap::menu id="nav" data-custom="value">
            <x-slot:item>content</x-slot:item>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-custom="value"', false);
});

// --- Menu.accordion: Structure ---

test('trigger has data-ws-menu-accordion attribute', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-menu-accordion="settings"', false);
});

test('panel has data-ws-menu-accordion-panel attribute', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-menu-accordion-panel="settings"', false);
});

test('panel has id for ARIA linking', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('id="ws-menu-accordion-panel-settings"', false);
});

test('aria-controls links trigger to panel', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('aria-controls="ws-menu-accordion-panel-settings"', false);
});

// --- Menu.accordion: Label ---

test('trigger renders label text', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('Settings');
});

test('trigger falls back to id when label is omitted', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="my-group">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('my-group');
});

test('trigger slot overrides label', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="custom" label="Fallback">
                    <x-slot:trigger><i class="bi bi-gear"></i> Custom</x-slot:trigger>
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('bi-gear', false)
    ->assertDontSee('Fallback');
});

// --- Menu.accordion: Initial state ---

test('open group panel has data-ws-open', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings" open>
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('data-ws-open="true"', false);
});

test('closed group panel is hidden', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('style="display: none"', false);
});

test('open group trigger has open class', function () {
    $this->blade('
        <x-wirestrap::menu id="nav">
            <x-slot:grp>
                <x-wirestrap::menu.accordion id="settings" label="Settings" open>
                    <x-slot:item>content</x-slot:item>
                </x-wirestrap::menu.accordion>
            </x-slot:grp>
        </x-wirestrap::menu>
    ')
    ->assertSee('ws-menu-accordion-trigger open', false);
});
