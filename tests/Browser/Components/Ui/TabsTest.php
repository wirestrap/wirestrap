<?php

// --- Initial state ---

test('first tab panel is visible by default', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertVisible('#tabs-basic [data-ws-tab="first"].ws-tabs-panel')
        ->assertMissing('#tabs-basic [data-ws-tab="second"].ws-tabs-panel');
});

test('default prop selects the initial active tab', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertMissing('#tabs-default [data-ws-tab="alpha"].ws-tabs-panel')
        ->assertVisible('#tabs-default [data-ws-tab="beta"].ws-tabs-panel');
});

test('aria-selected reflects active tab', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertAttribute('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button', 'aria-selected', 'true')
        ->assertAttribute('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button', 'aria-selected', 'false');
});

// --- Click interactions ---

test('clicking a tab switches the active panel', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertVisible('#tabs-basic [data-ws-tab="first"].ws-tabs-panel')
        ->click('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button')
        ->assertVisible('#tabs-basic [data-ws-tab="second"].ws-tabs-panel')
        ->assertScript(js_wait_for('#tabs-basic [data-ws-tab="first"].ws-tabs-panel--hidden'));
});

test('active tab button gets active class after click', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertPresent('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button.active')
        ->click('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button')
        ->assertPresent('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button.active')
        ->assertNotPresent('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button.active');
});

test('aria-selected updates after click', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->click('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button')
        ->assertAttribute('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button', 'aria-selected', 'true')
        ->assertAttribute('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button', 'aria-selected', 'false');
});

// --- Programmatic control ($wirestrap magic) ---

test('$wirestrap.tabs.show switches to the given tab', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertVisible('#tabs-events [data-ws-tab="one"].ws-tabs-panel')
        ->assertScript(js_wirestrap("tabs.show('tabs-events', 'two')"))
        ->assertVisible('#tabs-events [data-ws-tab="two"].ws-tabs-panel')
        ->assertScript(js_wait_for('#tabs-events [data-ws-tab="one"].ws-tabs-panel--hidden'));
});

// --- Invalid feedback ---

test('invalid-feedback highlights tab button when panel contains validation error', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertNotPresent('#tabs-valid [data-ws-tab="profile"].ws-tabs-nav-link-invalid')
        ->click('#btn-tabs-save')
        ->assertScript(js_wait_for('#tabs-valid .ws-form-feedback-invalid'))
        ->assertPresent('#tabs-valid [data-ws-tab="profile"].ws-tabs-nav-link-invalid')
        ->assertNotPresent('#tabs-valid [data-ws-tab="other"].ws-tabs-nav-link-invalid');
});
