<?php

// --- Livewire attributes ---

test('modal uses wire:ignore.self not wire:ignore', function () {
    $rendered = $this->blade('
        <x-wirestrap::modal id="my-modal" title="Test">
            <p>Content</p>
        </x-wirestrap::modal>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

// --- Root element ---

test('renders x-data wsModal on root', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('x-data="wsModal"', false);
});

// --- Data attributes (always present) ---

test('boolean data attribute defaults to false', function (string $attr) {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee("data-ws-{$attr}=\"false\"", false);
})->with(['draggable', 'static', 'resizable', 'backdrop']);

test('boolean data attribute is true when prop is set', function (string $attr) {
    $this->blade("
        <x-wirestrap::modal id=\"m\" {$attr}>
            <p>Content</p>
        </x-wirestrap::modal>
    ")->assertSee("data-ws-{$attr}=\"true\"", false);
})->with(['draggable', 'static', 'resizable', 'backdrop']);

// --- Conditional data attributes ---

test('data-ws-destroy-on-dismiss absent by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('data-ws-destroy-on-dismiss', false);
});

test('data-ws-destroy-on-dismiss present when set', function () {
    $this->blade('
        <x-wirestrap::modal id="m" :destroy-on-dismiss="true">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('data-ws-destroy-on-dismiss="true"', false);
});

test('data-ws-auto-show absent by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('data-ws-auto-show', false);
});

test('data-ws-auto-show present when set', function () {
    $this->blade('
        <x-wirestrap::modal id="m" :auto-show="true">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('data-ws-auto-show="true"', false);
});

// --- Minimizable data attributes ---

test('data-ws-label uses title when minimizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" title="My Title" minimizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('data-ws-label="My Title"', false);
});

test('data-ws-label falls back to id when no title', function () {
    $this->blade('
        <x-wirestrap::modal id="my-modal" minimizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('data-ws-label="my-modal"', false);
});

test('data-ws-btn-class set when minimizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" minimizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('data-ws-btn-class="ws-modal-minimized-btn"', false);
});

test('data-ws-label absent when not minimizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('data-ws-label', false);
});

// --- Modal element ---

test('renders id on modal element', function () {
    $this->blade('
        <x-wirestrap::modal id="my-modal">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('id="my-modal"', false);
});

test('renders role dialog', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('role="dialog"', false);
});

test('renders tabindex -1', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('tabindex="-1"', false);
});

test('renders aria-hidden true', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-hidden="true"', false);
});

test('renders ws-modal class', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal', false);
});

test('forwards custom class to modal element', function () {
    $this->blade('
        <x-wirestrap::modal id="m" class="custom-modal">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('custom-modal', false);
});

// --- ARIA ---

test('aria-labelledby links to title when title is set', function () {
    $this->blade('
        <x-wirestrap::modal id="my-modal" title="Test Title">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-labelledby="my-modal-title"', false);
});

test('aria-labelledby absent when no title', function () {
    $this->blade('
        <x-wirestrap::modal id="my-modal">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('aria-labelledby', false);
});

// --- Title ---

test('title renders h5 with id and class', function () {
    $this->blade('
        <x-wirestrap::modal id="my-modal" title="Settings">
            <p>Content</p>
        </x-wirestrap::modal>
    ')
        ->assertSee('Settings')
        ->assertSee('id="my-modal-title"', false)
        ->assertSee('ws-modal-title', false);
});

test('no title element when title omitted', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-title', false);
});

// --- Footer ---

test('footer renders when slot provided', function () {
    $this->blade('
        <x-wirestrap::modal id="m" title="Test">
            <p>Content</p>
            <x-slot:footer>
                <button>Action</button>
            </x-slot:footer>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-footer', false);
});

test('footer slot class forwarded', function () {
    $this->blade('
        <x-wirestrap::modal id="m" title="Test">
            <p>Content</p>
            <x-slot:footer class="custom-footer">
                <button>Action</button>
            </x-slot:footer>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-footer custom-footer', false);
});

test('no footer when not provided', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-footer', false);
});

// --- Dialog sizing ---

test('size class applied to dialog', function () {
    $this->blade('
        <x-wirestrap::modal id="m" size="800">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-800', false);
});

test('no size class by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-dialog ws-modal-', false);
});

// --- Close button ---

test('close button present when dismissible', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-close', false);
});

test('close button absent when dismissible false', function () {
    $this->blade('
        <x-wirestrap::modal id="m" :dismissible="false">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-close', false);
});

test('close button has aria-label', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-label="Close"', false);
});

// --- Expand button ---

test('expand button present when expandable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" expandable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-expand-btn', false);
});

test('expand button absent by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-expand-btn', false);
});

test('expand button has aria-pressed false', function () {
    $this->blade('
        <x-wirestrap::modal id="m" expandable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-pressed="false"', false);
});

test('expand button has aria-label', function () {
    $this->blade('
        <x-wirestrap::modal id="m" expandable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-label="Toggle fullscreen"', false);
});

// --- Minimize button ---

test('minimize button present when minimizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" minimizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-minimize-btn', false);
});

test('minimize button absent by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-minimize-btn', false);
});

test('minimize button has aria-label', function () {
    $this->blade('
        <x-wirestrap::modal id="m" minimizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('aria-label="Minimize"', false);
});

// --- Resize handles ---

test('resize handles rendered when resizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" resizable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')
        ->assertSee('data-ws-resize="e"', false)
        ->assertSee('data-ws-resize="w"', false)
        ->assertSee('data-ws-resize="s"', false)
        ->assertSee('data-ws-resize="se"', false)
        ->assertSee('data-ws-resize="sw"', false);
});

test('no resize handles when not resizable', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-modal-resize-handle', false);
});

// --- Drag handle ---

test('header has drag handle class when draggable', function () {
    $this->blade('
        <x-wirestrap::modal id="m" draggable>
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertSee('ws-modal-header ws-drag-handle', false);
});

test('header has no drag handle class by default', function () {
    $this->blade('
        <x-wirestrap::modal id="m">
            <p>Content</p>
        </x-wirestrap::modal>
    ')->assertDontSee('ws-drag-handle', false);
});
