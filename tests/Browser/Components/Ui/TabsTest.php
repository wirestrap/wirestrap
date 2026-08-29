<?php

// --- Initial state ---

test('first tab panel is visible by default', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertVisible('#tabs-basic [data-ws-tab="first"].ws-tabs-panel')
        ->assertPresent('#tabs-basic [data-ws-tab="second"].ws-tabs-panel.ws-tabs-panel--hidden');
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
        ->assertScript(js_wait_for('#tabs-basic [data-ws-tab="first"].ws-tabs-panel--hidden'))
        ->assertVisible('#tabs-basic [data-ws-tab="second"].ws-tabs-panel');
});

test('clicking the already-active tab is a no-op', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertVisible('#tabs-basic [data-ws-tab="first"].ws-tabs-panel')
        ->click('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button')
        ->assertVisible('#tabs-basic [data-ws-tab="first"].ws-tabs-panel')
        ->assertPresent('#tabs-basic [data-ws-tab="first"].ws-tabs-nav-button.active');
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
        ->assertScript(js_wait_for('#tabs-events [data-ws-tab="one"].ws-tabs-panel--hidden'))
        ->assertVisible('#tabs-events [data-ws-tab="two"].ws-tabs-panel');
});

// --- ws-show event ---

test('ws-show event is dispatched on panel activation', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->assertScript("(() => {
            window.__wsShowFired = false;
            document.querySelector('#tabs-basic [data-ws-tab=\"second\"].ws-tabs-panel')
                .addEventListener('ws-show', () => { window.__wsShowFired = true; });
            return true;
        })()")
        ->click('#tabs-basic [data-ws-tab="second"].ws-tabs-nav-button')
        ->assertScript(js_wait_for('#tabs-basic [data-ws-tab="first"].ws-tabs-panel--hidden'))
        ->assertScript('window.__wsShowFired === true');
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

test('invalid-feedback highlights non-active tab containing validation error', function () {
    $this->visit('/_ws/test/ui/tabs')
        ->click('#tabs-valid [data-ws-tab="other"].ws-tabs-nav-button')
        ->assertScript(js_wait_for('#tabs-valid [data-ws-tab="profile"].ws-tabs-panel--hidden'))
        ->click('#btn-tabs-save')
        ->assertScript(js_wait_for('#tabs-valid .ws-form-feedback-invalid'))
        ->assertPresent('#tabs-valid [data-ws-tab="profile"].ws-tabs-nav-link-invalid')
        ->assertNotPresent('#tabs-valid [data-ws-tab="other"].ws-tabs-nav-link-invalid');
});
