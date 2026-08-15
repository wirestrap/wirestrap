<?php

// --- Initial state ---

test('closed panel is hidden by default', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertMissing('#acc-multi [data-ws-accordion-panel="first"]');
});

test('panel with open attribute is visible on mount', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertVisible('#acc-multi [data-ws-accordion-panel="third"]');
});

test('aria-expanded reflects initial panel state', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertAttribute('#acc-multi [data-ws-accordion-item="first"]', 'aria-expanded', 'false')
        ->assertAttribute('#acc-multi [data-ws-accordion-item="third"]', 'aria-expanded', 'true');
});

// --- Click interactions ---

test('clicking a closed panel opens it', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertMissing('#acc-multi [data-ws-accordion-panel="first"]')
        ->click('#acc-multi [data-ws-accordion-item="first"]')
        ->assertVisible('#acc-multi [data-ws-accordion-panel="first"]');
});

test('clicking an open panel closes it', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->click('#acc-multi [data-ws-accordion-item="third"]')
        ->assertAttribute('#acc-multi [data-ws-accordion-item="third"]', 'aria-expanded', 'false')
        ->assertScript(js_wait_hidden('#acc-multi [data-ws-accordion-panel="third"]'));
});

test('multiple panels can be open simultaneously', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->click('#acc-multi [data-ws-accordion-item="first"]')
        ->assertVisible('#acc-multi [data-ws-accordion-panel="first"]')
        ->assertVisible('#acc-multi [data-ws-accordion-panel="third"]');
});

// --- Single mode ---

test('single mode opens clicked panel', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->click('#acc-single [data-ws-accordion-item="alpha"]')
        ->assertVisible('#acc-single [data-ws-accordion-panel="alpha"]');
});

test('single mode closes other panels when opening one', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->click('#acc-single [data-ws-accordion-item="alpha"]')
        ->assertVisible('#acc-single [data-ws-accordion-panel="alpha"]')
        ->assertAttribute('#acc-single [data-ws-accordion-item="beta"]', 'aria-expanded', 'false')
        ->assertScript(js_wait_hidden('#acc-single [data-ws-accordion-panel="beta"]'));
});

// --- Programmatic control ($wirestrap magic) ---

test('$wirestrap.accordion.show opens a panel', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertMissing('#acc-events [data-ws-accordion-panel="one"]')
        ->assertScript(js_wirestrap("accordion.show('acc-events', 'one')"))
        ->assertVisible('#acc-events [data-ws-accordion-panel="one"]');
});

test('$wirestrap.accordion.hide closes a panel', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->click('#acc-events [data-ws-accordion-item="one"]')
        ->assertVisible('#acc-events [data-ws-accordion-panel="one"]')
        ->assertScript(js_wirestrap("accordion.hide('acc-events', 'one')"))
        ->assertAttribute('#acc-events [data-ws-accordion-item="one"]', 'aria-expanded', 'false')
        ->assertScript(js_wait_hidden('#acc-events [data-ws-accordion-panel="one"]'));
});

test('$wirestrap.accordion.toggle switches a panel', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertMissing('#acc-events [data-ws-accordion-panel="two"]')
        ->assertScript(js_wirestrap("accordion.toggle('acc-events', 'two')"))
        ->assertVisible('#acc-events [data-ws-accordion-panel="two"]')
        ->assertScript(js_wirestrap("accordion.toggle('acc-events', 'two')"))
        ->assertAttribute('#acc-events [data-ws-accordion-item="two"]', 'aria-expanded', 'false')
        ->assertScript(js_wait_hidden('#acc-events [data-ws-accordion-panel="two"]'));
});

// --- Invalid feedback ---

test('invalid-feedback highlights button when panel contains validation error', function () {
    $this->visit('/_ws/test/ui/accordion')
        ->assertNotPresent('#acc-valid [data-ws-accordion-item="profile"].ws-accordion-button-invalid')
        ->click('#acc-valid [data-ws-accordion-item="profile"]')
        ->assertVisible('#acc-valid [data-ws-accordion-panel="profile"]')
        ->click('#btn-save')
        ->assertScript(js_wait_for('#acc-valid .ws-form-feedback-invalid'))
        ->assertPresent('#acc-valid [data-ws-accordion-item="profile"].ws-accordion-button-invalid')
        ->assertNotPresent('#acc-valid [data-ws-accordion-item="settings"].ws-accordion-button-invalid');
});
