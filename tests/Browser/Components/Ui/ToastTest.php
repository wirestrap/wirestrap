<?php

/**
 * Add a toast via Wirestrap.toast.add() inside assertScript.
 */
function js_toast(string $options = "'Hello'"): string
{
    return "(() => { Wirestrap.toast.add({$options}); return true; })()";
}

/**
 * Wait until no toast elements remain in the DOM.
 */
function js_wait_no_toasts(int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            if (!document.querySelector('.ws-toast')) {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for all toasts to be removed'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

// --- Rendering ---

test('string shorthand creates toast with message', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast())
        ->assertVisible('.ws-toast .ws-toast-body')
        ->assertScript("document.querySelector('.ws-toast .ws-toast-body').textContent.trim() === 'Hello'");
});

test('default type is primary', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast())
        ->assertPresent('.ws-toast.ws-toast-primary');
});

test('type variant applies correct class', function (string $type) {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', type: '{$type}', duration: 0 }"))
        ->assertPresent(".ws-toast.ws-toast-{$type}");
})->with(['success', 'info', 'warning', 'danger']);

test('toast has role alert', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertAttribute('.ws-toast', 'role', 'alert');
});

test('title renders header with icon', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', title: 'Done', duration: 0 }"))
        ->assertPresent('.ws-toast .ws-toast-header .ws-toast-icon')
        ->assertPresent('.ws-toast .ws-toast-header .ws-toast-title')
        ->assertScript("document.querySelector('.ws-toast .ws-toast-title').textContent.trim() === 'Done'");
});

test('no title means no header', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertNotPresent('.ws-toast .ws-toast-header');
});

test('progress bar rendered when duration > 0', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 10000 }"))
        ->assertPresent('.ws-toast .ws-toast-progress .ws-toast-progress-bar');
});

test('no progress bar when duration is 0', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertNotPresent('.ws-toast .ws-toast-progress');
});

// --- Behavior ---

test('toast becomes visible with transition class', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertScript(js_wait_for('.ws-toast.ws-toast-visible'));
});

test('click dismisses toast', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertVisible('.ws-toast')
        ->click('.ws-toast')
        ->assertScript(js_wait_no_toasts());
});

test('click dismisses timed toast', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'timed', duration: 10000 }"))
        ->assertVisible('.ws-toast')
        ->assertPresent('.ws-toast .ws-toast-progress-bar')
        ->click('.ws-toast')
        ->assertScript(js_wait_no_toasts());
});

test('html in message is escaped', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: '<b>bold</b>', duration: 0 }"))
        ->assertScript("document.querySelector('.ws-toast .ws-toast-body').textContent.trim() === '<b>bold</b>'")
        ->assertNotPresent('.ws-toast .ws-toast-body b');
});

test('auto-dismiss removes toast after duration', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 300 }"))
        ->assertVisible('.ws-toast')
        ->assertScript(js_wait_no_toasts());
});

test('hover pauses auto-dismiss', function () {
    // Use a short duration and verify the toast survives well beyond it while hovered
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'hover me', duration: 400 }"))
        ->assertVisible('.ws-toast')
        ->hover('.ws-toast')
        // Wait 600ms (well past the 400ms duration) and verify it's still present
        ->assertScript("new Promise(resolve => setTimeout(() => resolve(document.querySelector('.ws-toast') !== null), 600))")
        ->hover('#elsewhere')
        ->assertScript(js_wait_no_toasts());
});

test('multiple toasts stack', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'one', duration: 0 }"))
        ->assertScript(js_toast("{ message: 'two', duration: 0 }"))
        ->assertScript(js_toast("{ message: 'three', duration: 0 }"))
        ->assertScript("document.querySelectorAll('.ws-toast').length === 3");
});

test('max limit removes oldest toast', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript('(() => { Wirestrap.toast.configure({ max: 2 }); return true; })()')
        ->assertScript(js_toast("{ message: 'one', duration: 0 }"))
        ->assertScript(js_toast("{ message: 'two', duration: 0 }"))
        ->assertScript(js_toast("{ message: 'three', duration: 0 }"))
        ->assertScript(js_wait_for('.ws-toast.ws-toast-leaving'))
        ->assertScript("document.querySelectorAll('.ws-toast:not(.ws-toast-leaving)').length === 2");
});

// --- Configure ---

test('configure sets default duration', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript('(() => { Wirestrap.toast.configure({ duration: 300 }); return true; })()')
        ->assertScript(js_toast("{ message: 'short' }"))
        ->assertVisible('.ws-toast')
        ->assertScript(js_wait_no_toasts(2000));
});

// --- Container ---

test('container has default placement class', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertPresent('.ws-toast-container.ws-toast-bottom-end');
});

test('configure placement changes container class', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript("(() => { Wirestrap.toast.configure({ placement: 'top-start' }); return true; })()")
        ->assertScript('(() => { Wirestrap.toast.destroy(); return true; })()')
        ->assertScript(js_toast("{ message: 'ok', duration: 0 }"))
        ->assertPresent('.ws-toast-container.ws-toast-top-start');
});

// --- Alpine magic ---

test('$wirestrap.toast() creates a toast', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_wirestrap("toast({ message: 'alpine', duration: 0 })"))
        ->assertVisible('.ws-toast');
});

test('$wirestrap.toast() string shorthand', function () {
    $this->visit('/_ws/test/ui/toast')
        ->assertScript(js_wirestrap("toast('alpine string')"))
        ->assertVisible('.ws-toast');
});

// --- PHP (Livewire) ---

test('php toast() creates a toast', function () {
    $this->visit('/_ws/test/ui/toast')
        ->click('#btn-php-basic')
        ->assertScript(js_wait_for('.ws-toast.ws-toast-success'))
        ->assertVisible('.ws-toast.ws-toast-success')
        ->assertScript("document.querySelector('.ws-toast .ws-toast-body').textContent.trim() === 'PHP toast message'");
});

test('php toast() with title renders header', function () {
    $this->visit('/_ws/test/ui/toast')
        ->click('#btn-php-titled')
        ->assertScript(js_wait_for('.ws-toast.ws-toast-info'))
        ->assertPresent('.ws-toast .ws-toast-header')
        ->assertScript("document.querySelector('.ws-toast .ws-toast-title').textContent.trim() === 'Info'")
        ->assertScript("document.querySelector('.ws-toast .ws-toast-body').textContent.trim() === 'PHP titled toast'");
});

test('php toast() with duration 0 has no progress bar', function () {
    $this->visit('/_ws/test/ui/toast')
        ->click('#btn-php-persistent')
        ->assertScript(js_wait_for('.ws-toast.ws-toast-danger'))
        ->assertNotPresent('.ws-toast .ws-toast-progress');
});
