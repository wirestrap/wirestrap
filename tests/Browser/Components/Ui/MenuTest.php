<?php

// --- Initial state ---

test('closed group panel is hidden by default', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertMissing('#menu-basic [data-ws-menu-accordion-panel="settings"]');
});

test('open group is visible on mount', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="reports"]');
});

// --- Behavior ---

test('clicking a closed group opens it', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertMissing('#menu-basic [data-ws-menu-accordion-panel="settings"]')
        ->click('#menu-basic [data-ws-menu-accordion="settings"]')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="settings"]');
});

test('clicking an open group closes it', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="reports"]')
        ->click('#menu-basic [data-ws-menu-accordion="reports"]')
        ->assertScript(js_wait_hidden('#menu-basic [data-ws-menu-accordion-panel="reports"]'));
});

test('clicking updates aria-expanded', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertAttribute('#menu-basic [data-ws-menu-accordion="settings"]', 'aria-expanded', 'false')
        ->click('#menu-basic [data-ws-menu-accordion="settings"]')
        ->assertAttribute('#menu-basic [data-ws-menu-accordion="settings"]', 'aria-expanded', 'true');
});

test('multiple groups can be open simultaneously', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="reports"]')
        ->click('#menu-basic [data-ws-menu-accordion="settings"]')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="settings"]')
        ->assertVisible('#menu-basic [data-ws-menu-accordion-panel="reports"]');
});

// --- Single mode ---

test('single mode closes other group when opening one', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-single [data-ws-menu-accordion-panel="grp-a"]')
        ->click('#menu-single [data-ws-menu-accordion="grp-b"]')
        ->assertVisible('#menu-single [data-ws-menu-accordion-panel="grp-b"]')
        ->assertScript(js_wait_hidden('#menu-single [data-ws-menu-accordion-panel="grp-a"]'));
});

// --- Nesting ---

test('nested accordion opens inside parent', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-nested [data-ws-menu-accordion-panel="nest-settings"]')
        ->assertMissing('#menu-nested [data-ws-menu-accordion-panel="nest-advanced"]')
        ->click('#menu-nested [data-ws-menu-accordion="nest-advanced"]')
        ->assertVisible('#menu-nested [data-ws-menu-accordion-panel="nest-advanced"]')
        ->assertVisible('#menu-nested [data-ws-menu-accordion-panel="nest-settings"]');
});

test('single mode nested: opening child does not close parent', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertVisible('#menu-nested-single [data-ws-menu-accordion-panel="ns-settings"]')
        ->click('#menu-nested-single [data-ws-menu-accordion="ns-advanced"]')
        ->assertVisible('#menu-nested-single [data-ws-menu-accordion-panel="ns-advanced"]')
        ->assertVisible('#menu-nested-single [data-ws-menu-accordion-panel="ns-settings"]');
});

test('single mode nested: opening sibling closes the other', function () {
    $this->visit('/_ws/test/ui/menu')
        ->click('#menu-nested-single [data-ws-menu-accordion="ns-advanced"]')
        ->assertVisible('#menu-nested-single [data-ws-menu-accordion-panel="ns-advanced"]')
        ->click('#menu-nested-single [data-ws-menu-accordion="ns-security"]')
        ->assertVisible('#menu-nested-single [data-ws-menu-accordion-panel="ns-security"]')
        ->assertScript(js_wait_hidden('#menu-nested-single [data-ws-menu-accordion-panel="ns-advanced"]'));
});

// --- Programmatic control ($wirestrap magic) ---

test('$wirestrap.menu.show opens a group', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertMissing('#menu-events [data-ws-menu-accordion-panel="ev-grp"]')
        ->assertScript(js_wirestrap("menu.show('menu-events', 'ev-grp')"))
        ->assertVisible('#menu-events [data-ws-menu-accordion-panel="ev-grp"]');
});

test('$wirestrap.menu.hide closes a group', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertScript(js_wirestrap("menu.show('menu-events', 'ev-grp')"))
        ->assertVisible('#menu-events [data-ws-menu-accordion-panel="ev-grp"]')
        ->assertScript(js_wirestrap("menu.hide('menu-events', 'ev-grp')"))
        ->assertScript(js_wait_hidden('#menu-events [data-ws-menu-accordion-panel="ev-grp"]'));
});

test('$wirestrap.menu.toggle switches a group', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertMissing('#menu-events [data-ws-menu-accordion-panel="ev-grp"]')
        ->assertScript(js_wirestrap("menu.toggle('menu-events', 'ev-grp')"))
        ->assertVisible('#menu-events [data-ws-menu-accordion-panel="ev-grp"]')
        ->assertScript(js_wirestrap("menu.toggle('menu-events', 'ev-grp')"))
        ->assertScript(js_wait_hidden('#menu-events [data-ws-menu-accordion-panel="ev-grp"]'));
});

test('magic show on nested group opens ancestors', function () {
    $this->visit('/_ws/test/ui/menu')
        ->assertMissing('#menu-nested-events [data-ws-menu-accordion-panel="ne-settings"]')
        ->assertMissing('#menu-nested-events [data-ws-menu-accordion-panel="ne-advanced"]')
        ->assertScript(js_wirestrap("menu.show('menu-nested-events', 'ne-advanced')"))
        ->assertVisible('#menu-nested-events [data-ws-menu-accordion-panel="ne-advanced"]')
        ->assertVisible('#menu-nested-events [data-ws-menu-accordion-panel="ne-settings"]');
});
