<?php

// --- Autosize ---

test('autosize sets height variable on init', function () {
    $this->visit('/_ws/test/form/textarea')
        ->assertScript(js_after_tick("
            /^\\d+px$/.test(
                getComputedStyle(document.querySelector('#autosize').parentElement)
                    .getPropertyValue('--ws-textarea-height').trim()
            )
        "));
});

test('autosize grows textarea on multiline input', function () {
    $this->visit('/_ws/test/form/textarea')
        ->assertScript(js_after_tick("
            (() => {
                const el = document.querySelector('#autosize');
                window.__wsInitialHeight = parseInt(el.parentElement.style.getPropertyValue('--ws-textarea-height'));
                return window.__wsInitialHeight > 0;
            })()
        "))
        ->fill('#autosize', "line1\nline2\nline3\nline4\nline5\nline6\nline7\nline8\nline9\nline10")
        ->assertScript("
            new Promise(resolve => setTimeout(() => {
                const el = document.querySelector('#autosize');
                const height = parseInt(el.parentElement.style.getPropertyValue('--ws-textarea-height'));
                resolve(height > window.__wsInitialHeight);
            }, 100))
        ");
});

test('no autosize does not set height variable', function () {
    $this->visit('/_ws/test/form/textarea')
        ->assertScript(js_after_tick("
            document.querySelector('#no-autosize').parentElement.style
                .getPropertyValue('--ws-textarea-height') === ''
        "));
});
