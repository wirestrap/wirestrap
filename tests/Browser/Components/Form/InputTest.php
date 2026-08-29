<?php

// --- Password toggle ---

test('password input starts as type password', function () {
    $this->visit('/_ws/test/form/input')
        ->assertAttribute('#pwd', 'type', 'password');
});

test('clicking toggle changes type to text', function () {
    $this->visit('/_ws/test/form/input')
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('#pwd').type === 'text'"));
});

test('clicking toggle twice reverts to password', function () {
    $this->visit('/_ws/test/form/input')
        ->click('.ws-form-input-password-toggle')
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('#pwd').type === 'password'"));
});

test('toggle button gets ws-password-shown class when visible', function () {
    $this->visit('/_ws/test/form/input')
        ->assertScript(js_after_tick("!document.querySelector('.ws-form-input-password-toggle').classList.contains('ws-password-shown')"))
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('.ws-form-input-password-toggle').classList.contains('ws-password-shown')"));
});

test('toggle button removes ws-password-shown class on second click', function () {
    $this->visit('/_ws/test/form/input')
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('.ws-form-input-password-toggle').classList.contains('ws-password-shown')"))
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("!document.querySelector('.ws-form-input-password-toggle').classList.contains('ws-password-shown')"));
});

test('toggle button aria-label cycles through Show and Hide', function () {
    $this->visit('/_ws/test/form/input')
        ->assertAttribute('.ws-form-input-password-toggle', 'aria-label', 'Show password')
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('.ws-form-input-password-toggle').getAttribute('aria-label') === 'Hide password'"))
        ->click('.ws-form-input-password-toggle')
        ->assertScript(js_after_tick("document.querySelector('.ws-form-input-password-toggle').getAttribute('aria-label') === 'Show password'"));
});
