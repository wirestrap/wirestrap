<?php

// Helper: wait until both Livewire components are mounted and their eager loads are done
function waitForRefreshKeysPage(mixed $page): mixed
{
    return $page->visit('/_ws/test/form/refresh-keys')
        ->assertScript(js_wait_for('#select-with-value-b'))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectWithValue') . ' === 1 && ' . refreshKeysLoadCount('b', 'selectWithValue') . ' === 1'));
}

// Helper: JS expression reading how many times a field of a component loaded its options
function refreshKeysLoadCount(string $suffix, string $field): string
{
    return '(' . js_wire_get("#select-with-value-{$suffix}", 'calls') . ".{$field} ?? 0)";
}

test('select refresh by key reloads subscribers across Livewire components', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wirestrap("select.refresh('colors')"))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectWithValue') . ' === 2 && ' . refreshKeysLoadCount('b', 'selectWithValue') . ' === 2'))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'selectUnsubscribed') . ' === 1 && ' . refreshKeysLoadCount('b', 'selectUnsubscribed') . ' === 1'));
});

test('select refresh by key defers subscribers without a value until opened', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wirestrap("select.refresh('colors')"))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'selectEmpty') . ' === 0'))
        ->click('#select-empty-a')
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectEmpty') . ' === 1'));
});

test('select refresh by key does not reach autocompletes', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wirestrap("select.refresh('colors')"))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectWithValue') . ' === 2'))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'autocompleteWithTags') . ' === 1 && ' . refreshKeysLoadCount('b', 'autocompleteWithTags') . ' === 1'));
});

test('autocomplete refresh by key reloads subscribers and not selects', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'autocompleteWithTags') . ' === 1 && ' . refreshKeysLoadCount('b', 'autocompleteWithTags') . ' === 1'))
        ->assertScript(js_wirestrap("autocomplete.refresh('colors')"))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'autocompleteWithTags') . ' === 2 && ' . refreshKeysLoadCount('b', 'autocompleteWithTags') . ' === 2'))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'selectWithValue') . ' === 1 && ' . refreshKeysLoadCount('b', 'selectWithValue') . ' === 1'));
});

test('select refresh by id still targets a single select', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wirestrap("select.refresh('select-unsubscribed-a')"))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectUnsubscribed') . ' === 2'))
        ->assertScript(js_still_true(refreshKeysLoadCount('b', 'selectUnsubscribed') . ' === 1 && ' . refreshKeysLoadCount('a', 'selectWithValue') . ' === 1'));
});

test('a select subscribed to its own id is refreshed once', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectOwnIdKey') . ' === 1'))
        ->assertScript(js_wirestrap("select.refresh('select-own-id-key-a')"))
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectOwnIdKey') . ' === 2'))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'selectOwnIdKey') . ' === 2'));
});

test('select refresh by key from a Livewire action reloads subscribers', function () {
    waitForRefreshKeysPage($this)
        ->click('#btn-create-a')
        ->assertScript(js_wait_until(refreshKeysLoadCount('a', 'selectWithValue') . ' === 2 && ' . refreshKeysLoadCount('b', 'selectWithValue') . ' === 2'));
});

test('an unknown key refreshes nothing', function () {
    waitForRefreshKeysPage($this)
        ->assertScript(js_wirestrap("select.refresh('unknown')"))
        ->assertScript(js_still_true(refreshKeysLoadCount('a', 'selectWithValue') . ' === 1 && ' . refreshKeysLoadCount('b', 'selectWithValue') . ' === 1'));
});
