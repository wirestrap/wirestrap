<?php

/**
 * Show an alert via Wirestrap.alert.show() inside assertScript.
 */
function js_alert(string $options = "'Hello'"): string
{
    return "(() => { Wirestrap.alert.show({$options}); return true; })()";
}

/**
 * Wait until no alert elements remain in the DOM.
 */
function js_wait_no_alerts(int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            if (!document.querySelector('.ws-alert')) {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for all alerts to be removed'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

// --- Rendering ---

test('string shorthand creates alert with message', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert())
        ->assertVisible('.ws-alert .ws-alert-body')
        ->assertScript("document.querySelector('.ws-alert .ws-alert-body').textContent.trim() === 'Hello'");
});

test('default type is primary', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert())
        ->assertPresent('.ws-alert.ws-alert-primary');
});

test('type variant applies correct class', function (string $type) {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', type: '{$type}' }"))
        ->assertPresent(".ws-alert.ws-alert-{$type}");
})->with(['success', 'info', 'warning', 'danger']);

test('alert has role alertdialog and aria-modal', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertAttribute('.ws-alert', 'role', 'alertdialog')
        ->assertAttribute('.ws-alert', 'aria-modal', 'true');
});

test('title renders header with icon', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', title: 'Done' }"))
        ->assertPresent('.ws-alert .ws-alert-header .ws-alert-icon')
        ->assertPresent('.ws-alert .ws-alert-header .ws-alert-title');
});

test('no title means no header', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertNotPresent('.ws-alert .ws-alert-header');
});

test('dismiss button shown by default', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertPresent('.ws-alert .ws-alert-footer .ws-alert-dismiss');
});

test('custom dismissText label', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', dismissText: 'Got it' }"))
        ->assertSeeIn('.ws-alert-dismiss', 'Got it');
});

test('showDismiss false hides dismiss button', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', showDismiss: false }"))
        ->assertNotPresent('.ws-alert .ws-alert-footer');
});

test('progress bar rendered when duration > 0', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', duration: 10000 }"))
        ->assertPresent('.ws-alert .ws-alert-progress .ws-alert-progress-bar');
});

test('no progress bar when duration is 0', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertNotPresent('.ws-alert .ws-alert-progress');
});

// --- Behavior ---

test('alert becomes visible with backdrop', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertPresent('.ws-alert-backdrop.ws-alert-visible')
        ->assertPresent('.ws-alert.ws-alert-visible');
});

test('dismiss button closes alert', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertVisible('.ws-alert')
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_no_alerts());
});

test('backdrop click dismisses alert', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertVisible('.ws-alert')
        ->assertScript("(() => {
            document.querySelector('.ws-alert-backdrop').dispatchEvent(new MouseEvent('click', { bubbles: true }));
            return true;
        })()")
        ->assertScript(js_wait_no_alerts());
});

test('escape key dismisses alert', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok' }"))
        ->assertVisible('.ws-alert')
        ->assertScript("(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
            return true;
        })()")
        ->assertScript(js_wait_no_alerts());
});

test('backdrop shake when backdropDismiss is false', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', backdropDismiss: false }"))
        ->assertVisible('.ws-alert')
        ->assertScript("(() => {
            document.querySelector('.ws-alert-backdrop').dispatchEvent(new MouseEvent('click', { bubbles: true }));
            return !!document.querySelector('.ws-alert.ws-alert-shaking');
        })()")
        ->assertPresent('.ws-alert.ws-alert-visible')
        ->assertNotPresent('.ws-alert.ws-alert-leaving')
        ->assertScript(js_wait_for('.ws-alert:not(.ws-alert-shaking)'));
});

test('escape shake when escapeDismiss is false', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', escapeDismiss: false }"))
        ->assertVisible('.ws-alert')
        ->assertScript("(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
            return !!document.querySelector('.ws-alert.ws-alert-shaking');
        })()")
        ->assertPresent('.ws-alert.ws-alert-visible')
        ->assertNotPresent('.ws-alert.ws-alert-leaving')
        ->assertScript(js_wait_for('.ws-alert:not(.ws-alert-shaking)'));
});

test('focus moves to alert on show', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'focus test' }"))
        ->assertScript(js_wait_focus('.ws-alert'));
});

test('auto-dismiss removes alert after duration', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'ok', duration: 300 }"))
        ->assertVisible('.ws-alert')
        ->assertScript(js_wait_no_alerts());
});

test('alerts are queued one at a time', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_alert("{ message: 'first' }"))
        ->assertScript(js_alert("{ message: 'second' }"))
        ->assertScript("document.querySelectorAll('.ws-alert').length === 1")
        ->assertScript("document.querySelector('.ws-alert .ws-alert-body').textContent.trim() === 'first'")
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_for('.ws-alert.ws-alert-visible'))
        ->assertScript("document.querySelector('.ws-alert .ws-alert-body').textContent.trim() === 'second'");
});

// --- Confirm ---

test('confirm shows cancel and confirm buttons with labels', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm')
        ->assertVisible('.ws-alert')
        ->assertPresent('.ws-alert .ws-alert-cancel')
        ->assertPresent('.ws-alert .ws-alert-dismiss')
        ->assertSeeIn('.ws-alert-dismiss', 'Yes')
        ->assertSeeIn('.ws-alert-cancel', 'No');
});

test('cancel button dismisses confirm without calling method', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm')
        ->assertVisible('.ws-alert')
        ->click('.ws-alert-cancel')
        ->assertScript(js_wait_no_alerts())
        ->assertNotPresent('#deleted-flag');
});

test('confirm calls Livewire method', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm')
        ->assertVisible('.ws-alert')
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_for('#deleted-flag'))
        ->assertVisible('#deleted-flag');
});

test('confirm button disables on click', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm')
        ->assertVisible('.ws-alert')
        ->assertScript("(() => {
            const btn = document.querySelector('.ws-alert-dismiss');
            btn.click();
            return btn.disabled;
        })()");
});

test('confirm shorthand shows confirm and calls method', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm-shorthand')
        ->assertVisible('.ws-alert')
        ->assertPresent('.ws-alert .ws-alert-cancel')
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_for('#deleted-flag'))
        ->assertVisible('#deleted-flag');
});

test('confirm with params forwards arguments to method', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-confirm-params')
        ->assertVisible('.ws-alert')
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_for('#moved-flag'))
        ->assertSeeIn('#moved-flag', '42->99');
});

test('confirm from a teleported trigger calls Livewire method', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_wait_for('#btn-confirm-teleported'))
        ->click('#btn-confirm-teleported')
        ->assertVisible('.ws-alert')
        ->click('.ws-alert-dismiss')
        ->assertScript(js_wait_for('#deleted-flag'))
        ->assertVisible('#deleted-flag');
});

// --- Configure ---

test('configure sets global defaults', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript("(() => { Wirestrap.alert.configure({ dismissText: 'Close', backdropDismiss: false }); return true; })()")
        ->assertScript(js_alert("{ message: 'configured' }"))
        ->assertSeeIn('.ws-alert-dismiss', 'Close')
        // Backdrop click should shake, not dismiss
        ->assertScript("(() => {
            document.querySelector('.ws-alert-backdrop').dispatchEvent(new MouseEvent('click', { bubbles: true }));
            return !!document.querySelector('.ws-alert.ws-alert-shaking');
        })()")
        ->assertPresent('.ws-alert');
});

test('configure sets confirm defaults', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript("(() => { Wirestrap.alert.confirm.configure({ confirmText: 'Do it', cancelText: 'Nope' }); return true; })()")
        ->assertScript("(() => { Wirestrap.alert.confirm.show({ message: 'test' }); return true; })()")
        ->assertVisible('.ws-alert')
        ->assertSeeIn('.ws-alert-dismiss', 'Do it')
        ->assertSeeIn('.ws-alert-cancel', 'Nope');
});

// --- Alpine magic ---

test('$wirestrap.alert.show() creates an alert', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript(js_wirestrap("alert.show({ message: 'alpine' })"))
        ->assertVisible('.ws-alert');
});

// --- PHP (Livewire) ---

test('php alert() creates an alert', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-php-basic')
        ->assertScript(js_wait_for('.ws-alert.ws-alert-success'))
        ->assertVisible('.ws-alert.ws-alert-success')
        ->assertScript("document.querySelector('.ws-alert .ws-alert-body').textContent.trim() === 'PHP alert message'");
});

test('php alert() with title renders header', function () {
    $this->visit('/_ws/test/ui/alert')
        ->click('#btn-php-titled')
        ->assertScript(js_wait_for('.ws-alert.ws-alert-info'))
        ->assertPresent('.ws-alert .ws-alert-header')
        ->assertScript("document.querySelector('.ws-alert .ws-alert-title').textContent.trim() === 'Info'")
        ->assertScript("document.querySelector('.ws-alert .ws-alert-body').textContent.trim() === 'PHP titled alert'");
});

// --- Confirm layout ---

test('confirm footer is inserted before the progress bar', function () {
    $this->visit('/_ws/test/ui/alert')
        ->assertScript("(() => { Wirestrap.alert.confirm.show({ message: 'ok', duration: 10000 }); return true; })()")
        ->assertVisible('.ws-alert')
        ->assertPresent('.ws-alert .ws-alert-progress')
        ->assertScript("(() => {
            const kids = [...document.querySelector('.ws-alert').children].map(e => e.className);
            return kids.indexOf('ws-alert-footer') > -1
                && kids.indexOf('ws-alert-footer') < kids.indexOf('ws-alert-progress');
        })()");
});
