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
 * Returns a JS expression (Promise) that resolves to true once an element
 * matching the selector is hidden (display: none or removed from DOM).
 *
 * Use with assertScript(): ->assertScript(js_wait_hidden('.my-element'))
 */
function js_wait_hidden(string $selector, int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            const el = document.querySelector('{$selector}');
            if (!el || getComputedStyle(el).display === 'none') {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for hidden: {$selector}'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

/**
 * Returns a JS expression (Promise) that resolves to true once an element
 * is completely removed from the DOM. Unlike js_wait_hidden which also
 * resolves for display:none, this only resolves when the element no longer exists.
 * Useful for Livewire re-renders that destroy a component.
 *
 * Use with assertScript(): ->assertScript(js_wait_removed('my-element-id'))
 */
function js_wait_removed(string $id, int $timeout = 10000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        (function check() {
            if (!document.getElementById('{$id}')) return resolve(true);
            if (Date.now() > deadline) return reject(new Error('Timed out waiting for #{$id} removal'));
            requestAnimationFrame(check);
        })();
    })";
}

/**
 * Wait one animation frame for Alpine's microtask-based DOM updates to flush.
 * Returns a Promise that resolves with the result of the check expression.
 *
 * Use with assertScript(): ->assertScript(js_after_tick("el.disabled === true"))
 */
function js_after_tick(string $check): string
{
    return "new Promise(resolve => requestAnimationFrame(() => resolve({$check})))";
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
