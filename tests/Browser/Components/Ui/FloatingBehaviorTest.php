<?php

dataset('hover-floatables', [
    'tooltip' => ['ui/tooltip', '#trigger-basic', '#tip-basic-tip'],
    'flyout' => ['ui/flyout', '#trigger-basic', '#flyout-basic [data-ws-floatable]'],
    'popover' => ['ui/popover', '#trigger-full', '#pop-full [data-ws-floatable]'],
]);

dataset('click-floatables', [
    'tooltip' => ['ui/tooltip', '#trigger-click', '#tip-click-tip'],
    'flyout' => ['ui/flyout', '#trigger-click', '#flyout-click [data-ws-floatable]'],
    'popover' => ['ui/popover', '#trigger-click', '#pop-click [data-ws-floatable]'],
]);

dataset('interactive-floatables', [
    'tooltip' => ['ui/tooltip', '#trigger-interactive', '#tip-interactive-tip', '#tip-interactive-tip'],
    'flyout' => ['ui/flyout', '#trigger-interactive', '#flyout-interactive [data-ws-floatable]', '#flyout-panel'],
    'popover' => ['ui/popover', '#trigger-interactive', '#pop-interactive [data-ws-floatable]', '#popover-panel'],
]);

// --- Hidden by default ---

test('floatable is hidden by default', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertMissing($floatable);
})->with('hover-floatables');

// --- Hover trigger ---

test('floatable appears on hover', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertMissing($floatable)
        ->hover($trigger)
        ->assertVisible($floatable);
})->with('hover-floatables');

test('floatable hides on unhover', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->hover($trigger)
        ->assertVisible($floatable)
        ->hover('#elsewhere')
        ->assertScript(js_wait_hidden($floatable));
})->with('hover-floatables');

// --- Click trigger ---

test('floatable shows on click', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertMissing($floatable)
        ->click($trigger)
        ->assertVisible($floatable);
})->with('click-floatables');

test('floatable hides on second click', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->click($trigger)
        ->assertVisible($floatable)
        ->click($trigger)
        ->assertScript(js_wait_hidden($floatable));
})->with('click-floatables');

test('floatable closes on click outside', function (
    string $page,
    string $trigger,
    string $floatable,
) {
    $this->visit("/_ws/test/{$page}")
        ->click($trigger)
        ->assertVisible($floatable)
        ->click('#elsewhere')
        ->assertScript(js_wait_hidden($floatable));
})->with('click-floatables');

// --- Interactive behavior ---

test('interactive floatable stays open when hovering the panel', function (
    string $page,
    string $trigger,
    string $floatable,
    string $panel,
) {
    $this->visit("/_ws/test/{$page}")
        ->hover($trigger)
        ->assertVisible($floatable)
        ->hover($panel)
        ->assertVisible($floatable);
})->with('interactive-floatables');

// --- Non-interactive behavior (tooltip-specific) ---

test('non-interactive tooltip closes when hovering the floatable', function () {
    $this->visit('/_ws/test/ui/tooltip')
        ->hover('#trigger-noninteractive')
        ->assertVisible('#tip-noninteractive-tip')
        ->hover('#tip-noninteractive-tip')
        ->assertScript(js_wait_hidden('#tip-noninteractive-tip'));
});

// --- Focus behavior ---

test('focusing input inside trigger keeps tooltip open', function () {
    $this->visit('/_ws/test/ui/tooltip')
        ->assertMissing('#tip-focus-tip')
        ->click('#trigger-focus')
        ->assertVisible('#tip-focus-tip')
        ->click('#elsewhere')
        ->assertScript(js_wait_hidden('#tip-focus-tip'));
});

// --- Programmatic control ($wirestrap magic) ---

dataset('programmatic-floatables', [
    'tooltip' => ['ui/tooltip', 'tip-basic', '#tip-basic-tip', 'tooltip'],
    'flyout' => ['ui/flyout', 'flyout-basic', '#flyout-basic > [data-ws-floatable]', 'flyout'],
    'popover' => ['ui/popover', 'pop-full', '#pop-full > [data-ws-floatable]', 'popover'],
]);

test('$wirestrap magic show opens the floatable', function (
    string $page,
    string $id,
    string $floatable,
    string $type,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertMissing($floatable)
        ->assertScript(js_wirestrap("{$type}.show('{$id}')"))
        ->assertScript(js_wait_for($floatable . '.show'))
        ->assertVisible($floatable);
})->with('programmatic-floatables');

test('$wirestrap magic hide closes the floatable', function (
    string $page,
    string $id,
    string $floatable,
    string $type,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertScript(js_wirestrap("{$type}.show('{$id}')"))
        ->assertVisible($floatable)
        ->assertScript(js_wirestrap("{$type}.hide('{$id}')"))
        ->assertScript(js_wait_hidden($floatable));
})->with('programmatic-floatables');

test('$wirestrap magic toggle switches the floatable', function (
    string $page,
    string $id,
    string $floatable,
    string $type,
) {
    $this->visit("/_ws/test/{$page}")
        ->assertMissing($floatable)
        ->assertScript(js_wirestrap("{$type}.toggle('{$id}')"))
        ->assertScript(js_wait_for($floatable . '.show'))
        ->assertVisible($floatable)
        ->assertScript(js_wirestrap("{$type}.toggle('{$id}')"))
        ->assertScript(js_wait_hidden($floatable));
})->with('programmatic-floatables');
