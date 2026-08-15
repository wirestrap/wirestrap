<?php

/**
 * Returns a JS expression (Promise) that resolves to true once an element
 * matching the selector exists in the DOM. Useful for waiting after
 * Livewire round-trips or deferred DOM mutations.
 *
 * Use with assertScript(): ->assertScript(js_wait_for('.my-element'))
 */
function js_wait_for(string $selector, int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            if (document.querySelector('{$selector}')) {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for: {$selector}'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

/**
 * Returns a JS expression that calls a $wirestrap Alpine magic method.
 *
 * Use with assertScript(): ->assertScript(js_wirestrap("accordion.show('my-id', 'key')"))
 */
function js_wirestrap(string $expression): string
{
    return '(() => { Alpine.evaluate(document.body, "$wirestrap.' . $expression . '"); return true; })()';
}
