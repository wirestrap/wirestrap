<?php

// --- Show / Hide ---

test('magic show opens modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->assertMissing('#modal-basic')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertPresent('#modal-basic.ws-modal-visible');
});

test('aria attributes update on show', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertAttribute('#modal-basic', 'aria-modal', 'true')
        ->assertScript("!document.getElementById('modal-basic').hasAttribute('aria-hidden')");
});

test('close button dismisses modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->click('#modal-basic .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic');
});

test('clicking outside dialog dismisses modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertScript("(() => {
            document.getElementById('modal-basic').dispatchEvent(new MouseEvent('click', { bubbles: false }));
            return true;
        })()")
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic');
});

test('escape key dismisses modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertScript("(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
            return true;
        })()")
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic');
});

test('data-ws-dismiss="modal" dismisses modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-ws-dismiss')
        ->assertVisible('#modal-ws-dismiss')
        ->click('#dismiss-via-data')
        ->assertScript(js_wait_hidden('#modal-ws-dismiss'))
        ->assertMissing('#modal-ws-dismiss');
});

test('aria attributes update on hide', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->click('#modal-basic .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic')
        ->assertAttribute('#modal-basic', 'aria-hidden', 'true')
        ->assertScript("!document.getElementById('modal-basic').hasAttribute('aria-modal')");
});

test('dismissible false still allows data-ws-dismiss', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-no-dismiss')
        ->assertVisible('#modal-no-dismiss')
        ->click('#btn-close-no-dismiss')
        ->assertScript(js_wait_hidden('#modal-no-dismiss'))
        ->assertMissing('#modal-no-dismiss');
});

// --- Static mode ---

test('static mode shakes on outside click instead of closing', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-static')
        ->assertVisible('#modal-static')
        ->assertScript("(() => {
            document.getElementById('modal-static').dispatchEvent(new MouseEvent('click', { bubbles: false }));
            return document.getElementById('modal-static').classList.contains('ws-modal-static');
        })()")
        ->assertPresent('#modal-static.ws-modal-visible');
});

test('static mode shakes on escape instead of closing', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-static')
        ->assertVisible('#modal-static')
        ->assertScript("(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
            return document.getElementById('modal-static').classList.contains('ws-modal-static');
        })()")
        ->assertPresent('#modal-static.ws-modal-visible');
});

test('static mode close button still works', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-static')
        ->assertVisible('#modal-static')
        ->click('#modal-static .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-static'))
        ->assertMissing('#modal-static');
});

// --- Backdrop ---

test('backdrop shown when backdrop prop is true', function () {
    $this->visit('/_ws/test/ui/modal')
        ->assertNotPresent('.ws-modal-backdrop')
        ->click('#btn-backdrop')
        ->assertVisible('#modal-backdrop')
        ->assertPresent('.ws-modal-backdrop.ws-modal-visible');
});

test('backdrop removed when modal is closed', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-backdrop')
        ->assertVisible('#modal-backdrop')
        ->assertPresent('.ws-modal-backdrop')
        ->click('#modal-backdrop .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-backdrop'))
        ->assertMissing('#modal-backdrop')
        ->assertNotPresent('.ws-modal-backdrop');
});

test('no backdrop when backdrop prop is false', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertNotPresent('.ws-modal-backdrop');
});

// --- Backdrop reference counting ---

test('backdrop persists when one of two backdrop modals is closed', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-backdrop')
        ->assertVisible('#modal-backdrop')
        ->assertPresent('.ws-modal-backdrop')
        ->assertScript(js_wirestrap("modal.show('modal-backdrop2')"))
        ->assertVisible('#modal-backdrop2')
        ->click('#modal-backdrop2 .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-backdrop2'))
        ->assertMissing('#modal-backdrop2')
        ->assertPresent('.ws-modal-backdrop')
        ->click('#modal-backdrop .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-backdrop'))
        ->assertMissing('#modal-backdrop')
        ->assertNotPresent('.ws-modal-backdrop');
});

// --- Scroll lock ---

test('body scroll locked when non-draggable modal opens', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertScript("document.body.style.overflow === 'hidden'");
});

test('body scroll restored when modal closes', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->click('#modal-basic .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertScript("document.body.style.overflow === ''");
});

test('no scroll lock for draggable modals', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("document.body.style.overflow !== 'hidden'");
});

// --- Scroll lock reference counting ---

test('scroll lock persists when one of two modals is closed', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-scroll-a')
        ->assertVisible('#modal-scroll-a')
        ->assertScript("document.body.style.overflow === 'hidden'")
        ->assertScript(js_wirestrap("modal.show('modal-scroll-b')"))
        ->assertVisible('#modal-scroll-b')
        ->click('#modal-scroll-b .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-scroll-b'))
        ->assertScript("document.body.style.overflow === 'hidden'")
        ->click('#modal-scroll-a .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-scroll-a'))
        ->assertScript("document.body.style.overflow === ''");
});

// --- Stacking ---

test('multiple modals can be open simultaneously', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-stack-a')
        ->assertVisible('#modal-stack-a')
        ->click('#btn-stack-b')
        ->assertVisible('#modal-stack-b')
        ->assertVisible('#modal-stack-a');
});

test('last opened modal has highest z-index', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-stack-a')
        ->assertVisible('#modal-stack-a')
        ->click('#btn-stack-b')
        ->assertVisible('#modal-stack-b')
        ->assertScript("
            parseInt(document.getElementById('modal-stack-b').style.zIndex)
            > parseInt(document.getElementById('modal-stack-a').style.zIndex)
        ");
});

test('escape only closes topmost modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-stack-a')
        ->assertVisible('#modal-stack-a')
        ->click('#btn-stack-b')
        ->assertVisible('#modal-stack-b')
        ->assertScript("(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
            return true;
        })()")
        ->assertScript(js_wait_hidden('#modal-stack-b'))
        ->assertMissing('#modal-stack-b')
        ->assertVisible('#modal-stack-a');
});

// --- Expandable ---

test('expand button toggles fullscreen class', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-expandable')
        ->assertVisible('#modal-expandable')
        ->assertNotPresent('#modal-expandable .ws-modal-dialog.ws-modal-fullscreen')
        ->click('#modal-expandable .ws-modal-expand-btn')
        ->assertScript(js_wait_for('#modal-expandable .ws-modal-dialog.ws-modal-fullscreen'));
});

test('expand button aria-pressed reflects state', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-expandable')
        ->assertVisible('#modal-expandable')
        ->assertAttribute('#modal-expandable .ws-modal-expand-btn', 'aria-pressed', 'false')
        ->click('#modal-expandable .ws-modal-expand-btn')
        ->assertAttribute('#modal-expandable .ws-modal-expand-btn', 'aria-pressed', 'true');
});

test('expand button restores on second click', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-expandable')
        ->assertVisible('#modal-expandable')
        ->click('#modal-expandable .ws-modal-expand-btn')
        ->assertScript(js_wait_for('#modal-expandable .ws-modal-dialog.ws-modal-fullscreen'))
        ->click('#modal-expandable .ws-modal-expand-btn')
        ->assertScript(js_wait_for('#modal-expandable .ws-modal-dialog:not(.ws-modal-fullscreen):not(.ws-expanding)'));
});

// --- Minimizable ---

test('minimize creates taskbar button and hides modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-minimizable')
        ->assertVisible('#modal-minimizable')
        ->click('#modal-minimizable .ws-modal-minimize-btn')
        ->assertScript(js_wait_hidden('#modal-minimizable'))
        ->assertMissing('#modal-minimizable')
        ->assertPresent('#minimized_modals .ws-modal-minimized-btn');
});

test('taskbar button label comes from title', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-minimizable')
        ->assertVisible('#modal-minimizable')
        ->click('#modal-minimizable .ws-modal-minimize-btn')
        ->assertScript(js_wait_hidden('#modal-minimizable'))
        ->assertSeeIn('#minimized_modals .ws-modal-minimized-btn', 'Minimizable');
});

test('clicking taskbar button restores modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-minimizable')
        ->assertVisible('#modal-minimizable')
        ->click('#modal-minimizable .ws-modal-minimize-btn')
        ->assertScript(js_wait_hidden('#modal-minimizable'))
        ->assertMissing('#modal-minimizable')
        ->click('#minimized_modals .ws-modal-minimized-btn')
        ->assertVisible('#modal-minimizable')
        ->assertNotPresent('#minimized_modals .ws-modal-minimized-btn');
});

// --- Focus management ---

test('focus moves to first focusable element on show', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-focus')
        ->assertVisible('#modal-focus')
        ->assertScript("new Promise((resolve) => {
            requestAnimationFrame(() => requestAnimationFrame(() => {
                resolve(document.getElementById('modal-focus').contains(document.activeElement));
            }));
        })");
});

test('focus moves to modal itself when no focusable element', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-focus-no-focusable')
        ->assertVisible('#modal-focus-no-focusable')
        ->assertScript("new Promise((resolve) => {
            requestAnimationFrame(() => requestAnimationFrame(() => {
                resolve(document.activeElement === document.getElementById('modal-focus-no-focusable'));
            }));
        })");
});

// --- Programmatic control ($wirestrap magic) ---

test('$wirestrap.modal.show opens modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->assertMissing('#modal-basic')
        ->assertScript(js_wirestrap("modal.show('modal-basic')"))
        ->assertVisible('#modal-basic');
});

test('$wirestrap.modal.hide closes modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-basic')
        ->assertVisible('#modal-basic')
        ->assertScript(js_wirestrap("modal.hide('modal-basic')"))
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic');
});

// --- PHP (Livewire) control ---

test('PHP modalShow opens modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->assertMissing('#modal-basic')
        ->click('#btn-php-show')
        ->assertVisible('#modal-basic');
});

test('PHP modalHide closes modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-php-show')
        ->assertVisible('#modal-basic')
        ->assertScript("(() => {
            document.getElementById('btn-php-hide').click();
            return true;
        })()")
        ->assertScript(js_wait_hidden('#modal-basic'))
        ->assertMissing('#modal-basic');
});

// --- ModalManager ---

test('modalShowManaged creates a modal with child component', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->assertSeeIn('#managed-1', 'managed-content')
        ->assertPresent('#managed-1 .ws-modal-dialog.ws-modal-600');
});

test('managed modal has title from modalProps', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->assertSeeIn('#managed-1 .ws-modal-title', 'Managed Modal');
});

test('duplicate modalShowManaged re-shows instead of creating new', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#managed-1 .ws-modal-close')
        ->assertScript(js_wait_hidden('#managed-1'))
        ->assertMissing('#managed-1')
        ->click('#btn-managed-dup')
        ->assertVisible('#managed-1')
        ->assertScript("document.querySelectorAll('#managed-1').length === 1");
});

test('child component can self-close via modalHide', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#btn-self-close')
        ->assertScript(js_wait_hidden('#managed-1'))
        ->assertMissing('#managed-1');
});

test('child component can self-destroy via modalDestroyManaged', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#btn-self-destroy')
        ->assertScript(js_wait_removed('managed-1'))
        ->assertNotPresent('#managed-1');
});

test('modalDestroyManaged by key removes the modal', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#managed-1 .ws-modal-close')
        ->assertScript(js_wait_hidden('#managed-1'))
        ->assertMissing('#managed-1')
        ->click('#btn-destroy-key')
        ->assertScript(js_wait_removed('managed-1'))
        ->assertNotPresent('#managed-1');
});

test('modalDestroyManaged by component removes all modals for that component', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#managed-1 .ws-modal-close')
        ->assertScript(js_wait_hidden('#managed-1'))
        ->click('#btn-destroy-component')
        ->assertScript(js_wait_removed('managed-1'))
        ->assertNotPresent('#managed-1');
});

test('modalDestroyManaged with no args removes all managed modals', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed')
        ->assertVisible('#managed-1')
        ->click('#managed-1 .ws-modal-close')
        ->assertScript(js_wait_hidden('#managed-1'))
        ->click('#btn-destroy-all')
        ->assertScript(js_wait_removed('managed-1'))
        ->assertNotPresent('#managed-1');
});

test('destroyOnDismiss auto-destroys modal on close', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-managed-destroy-dismiss')
        ->assertVisible('#managed-destroy')
        ->click('#managed-destroy .ws-modal-close')
        ->assertScript(js_wait_removed('managed-destroy'))
        ->assertNotPresent('#managed-destroy');
});

// --- Expand + drag interaction ---

test('expand after drag saves position, collapse restores it', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-combo')
        ->assertVisible('#modal-combo')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-combo .ws-modal-header');
            const rect = handle.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: x, clientY: y, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: x + 200, clientY: y + 100, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
            return true;
        })()")
        ->assertScript("(() => {
            const d = document.querySelector('#modal-combo .ws-modal-dialog');
            window._savedLeft = d.style.left;
            window._savedTop = d.style.top;
            return d.style.position === 'absolute';
        })()")
        ->click('#modal-combo .ws-modal-expand-btn')
        ->assertScript(js_wait_for('#modal-combo .ws-modal-dialog.ws-modal-fullscreen:not(.ws-expanding)'))
        ->click('#modal-combo .ws-modal-expand-btn')
        ->assertScript(js_wait_for('#modal-combo .ws-modal-dialog:not(.ws-modal-fullscreen):not(.ws-expanding)'))
        ->assertScript("(() => {
            const d = document.querySelector('#modal-combo .ws-modal-dialog');
            return d.style.left === window._savedLeft
                && d.style.top === window._savedTop
                && d.style.position === 'absolute';
        })()");
});

// --- Drag behavior ---

test('dragging the header moves the dialog', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-draggable .ws-modal-header');
            const dialog = document.querySelector('#modal-draggable .ws-modal-dialog');
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX + 150, clientY: startY + 80, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return dialog.style.position === 'absolute'
                && parseFloat(dialog.style.left) > 0
                && parseFloat(dialog.style.top) > 0;
        })()");
});

test('body has ws-dragging class during drag', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-draggable .ws-modal-header');
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX + 10, clientY: startY, bubbles: true }));

            const hasDragging = document.body.classList.contains('ws-dragging');
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return hasDragging && !document.body.classList.contains('ws-dragging');
        })()");
});

test('starting drag brings modal to front via z-index', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-stack-a')
        ->assertVisible('#modal-stack-a')
        ->click('#btn-stack-b')
        ->assertVisible('#modal-stack-b')
        ->assertScript("
            parseInt(document.getElementById('modal-stack-b').style.zIndex)
            > parseInt(document.getElementById('modal-stack-a').style.zIndex)
        ")
        ->assertScript("(() => {
            const handleA = document.querySelector('#modal-stack-a .ws-modal-header');
            const rect = handleA.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;

            handleA.dispatchEvent(new MouseEvent('mousedown', { clientX: x, clientY: y, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: x + 5, clientY: y, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return parseInt(document.getElementById('modal-stack-a').style.zIndex)
                > parseInt(document.getElementById('modal-stack-b').style.zIndex);
        })()");
});

test('drag position resets after close and reopen', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-draggable .ws-modal-header');
            const dialog = document.querySelector('#modal-draggable .ws-modal-dialog');
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX + 200, clientY: startY + 100, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return dialog.style.position === 'absolute';
        })()")
        ->click('#modal-draggable .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-draggable'))
        ->assertMissing('#modal-draggable')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("document.querySelector('#modal-draggable .ws-modal-dialog').style.position !== 'absolute'");
});

test('clicking button inside header does not start drag', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("(() => {
            const closeBtn = document.querySelector('#modal-draggable .ws-modal-close');
            const dialog = document.querySelector('#modal-draggable .ws-modal-dialog');
            closeBtn.dispatchEvent(new MouseEvent('mousedown', { clientX: 100, clientY: 100, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: 300, clientY: 200, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
            return dialog.style.position !== 'absolute';
        })()");
});

test('drag is clamped within container bounds', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-draggable')
        ->assertVisible('#modal-draggable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-draggable .ws-modal-header');
            const dialog = document.querySelector('#modal-draggable .ws-modal-dialog');
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: -9999, clientY: -9999, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return parseFloat(dialog.style.left) >= 0 && parseFloat(dialog.style.top) >= 0;
        })()");
});

// --- Resize behavior ---

test('resizing east handle changes dialog width', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-e');
            const dialog = document.querySelector('#modal-resizable .ws-modal-dialog');
            const initialWidth = dialog.offsetWidth;
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX + 200, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return dialog.offsetWidth > initialWidth;
        })()");
});

test('resizing south handle changes content height', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-s');
            const content = document.querySelector('#modal-resizable .ws-modal-content');
            const initialHeight = content.offsetHeight;
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX, clientY: startY + 150, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return content.offsetHeight > initialHeight;
        })()");
});

test('resize switches dialog to absolute positioning', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("document.querySelector('#modal-resizable .ws-modal-dialog').style.position !== 'absolute'")
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-e');
            const rect = handle.getBoundingClientRect();
            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: rect.left, clientY: rect.top, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: rect.left + 10, clientY: rect.top, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
            return document.querySelector('#modal-resizable .ws-modal-dialog').style.position === 'absolute';
        })()");
});

test('resizing west handle moves left edge while right edge stays anchored', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-w');
            const dialog = document.querySelector('#modal-resizable .ws-modal-dialog');
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX - 150, clientY: startY, bubbles: true }));

            const widthAfter = dialog.offsetWidth;
            const leftAfter = parseFloat(dialog.style.left);

            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return leftAfter < startX && widthAfter > 300;
        })()");
});

test('resizing SE corner changes both width and height', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-se');
            const dialog = document.querySelector('#modal-resizable .ws-modal-dialog');
            const content = document.querySelector('#modal-resizable .ws-modal-content');
            const initialWidth = dialog.offsetWidth;
            const initialHeight = content.offsetHeight;
            const rect = handle.getBoundingClientRect();
            const startX = rect.left + rect.width / 2;
            const startY = rect.top + rect.height / 2;

            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: startX, clientY: startY, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: startX + 200, clientY: startY + 100, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));

            return dialog.offsetWidth > initialWidth && content.offsetHeight > initialHeight;
        })()");
});

test('resize resets after close and reopen', function () {
    $this->visit('/_ws/test/ui/modal')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("(() => {
            const handle = document.querySelector('#modal-resizable .ws-modal-resize-e');
            const rect = handle.getBoundingClientRect();
            handle.dispatchEvent(new MouseEvent('mousedown', { clientX: rect.left, clientY: rect.top, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mousemove', { clientX: rect.left + 200, clientY: rect.top, bubbles: true }));
            document.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
            return true;
        })()")
        ->click('#modal-resizable .ws-modal-close')
        ->assertScript(js_wait_hidden('#modal-resizable'))
        ->assertMissing('#modal-resizable')
        ->click('#btn-resizable')
        ->assertVisible('#modal-resizable')
        ->assertScript("!document.querySelector('#modal-resizable .ws-modal-dialog').style.width");
});
