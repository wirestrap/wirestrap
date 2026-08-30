<?php

/**
 * Wait until the counter text content reaches the expected value.
 */
function js_wait_counter(string $selector, string $value, int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            const el = document.querySelector('{$selector} span');
            if (el && el.textContent === '{$value}') {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for counter to reach {$value}'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

// --- Static animation ---

test('counter animates to integer target', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-int', '500'));
});

test('counter animates to decimal target with correct precision', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-dec', '4.87'));
});

test('counter with zero stays at 0', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-zero', '0'));
});

test('counter with zero and decimals shows formatted zero', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-zero-dec', '0.00'));
});

// --- MutationObserver re-animation ---

test('counter re-animates when data-ws-count changes', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-int', '500'))
        ->assertScript("(() => {
            document.querySelector('#counter-int').setAttribute('data-ws-count', '1000');
            return true;
        })()")
        ->assertScript(js_wait_counter('#counter-int', '1000'));
});

test('counter animates down when value decreases', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-int', '500'))
        ->assertScript("(() => {
            document.querySelector('#counter-int').setAttribute('data-ws-count', '200');
            return true;
        })()")
        ->assertScript(js_wait_counter('#counter-int', '200'));
});

// --- Negative values ---

test('counter animates to negative value', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-int', '500'))
        ->assertScript("(() => {
            document.querySelector('#counter-int').setAttribute('data-ws-count', '-100');
            return true;
        })()")
        ->assertScript(js_wait_counter('#counter-int', '-100'));
});

// --- NaN guard ---

test('non-finite value is ignored', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-int', '500'))
        ->assertScript("(() => {
            document.querySelector('#counter-int').setAttribute('data-ws-count', 'abc');
            return true;
        })()")
        ->assertScript(js_after_tick("document.querySelector('#counter-int span').textContent === '500'"));
});

// --- Reactive mode ---

test('reactive counter re-animates when Livewire property changes', function () {
    $this->visit('/_ws/test/ui/counter')
        ->assertScript(js_wait_counter('#counter-reactive', '100'))
        ->click('#btn-increment')
        ->assertScript(js_wait_counter('#counter-reactive', '600'));
});
