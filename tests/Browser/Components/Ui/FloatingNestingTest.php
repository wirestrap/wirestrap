<?php

dataset('nested-floatables', [
    'flyout > tooltip' => ['#parent-ft-trigger', '#parent-ft > [data-ws-floatable]', '#child-ft-trigger', '#child-ft-tip'],
    'flyout > flyout' => ['#parent-ff-trigger', '#parent-ff > [data-ws-floatable]', '#child-ff-trigger', '#child-ff > [data-ws-floatable]'],
    'flyout > popover' => ['#parent-fp-trigger', '#parent-fp > [data-ws-floatable]', '#child-fp-trigger', '#child-fp > [data-ws-floatable]'],
    'popover > tooltip' => ['#parent-pt-trigger', '#parent-pt > [data-ws-floatable]', '#child-pt-trigger', '#child-pt-tip'],
]);

dataset('nested-interactive-children', [
    'flyout > flyout' => ['#parent-ff-trigger', '#parent-ff > [data-ws-floatable]', '#child-ff-trigger', '#child-ff > [data-ws-floatable]', '#child-ff-panel'],
    'flyout > popover' => ['#parent-fp-trigger', '#parent-fp > [data-ws-floatable]', '#child-fp-trigger', '#child-fp > [data-ws-floatable]', '#child-fp-panel'],
]);

dataset('nested-teleported-parent', [
    'teleported flyout > tooltip' => ['#parent-tp-trigger', '.panel-tp', '#child-tp-trigger', '#child-tp-tip'],
    'teleported flyout > flyout' => ['#parent-tpf-trigger', '.panel-tpf', '#child-tpf-trigger', '#child-tpf > [data-ws-floatable]'],
]);

test('parent stays visible when showing child', function (
    string $parentTrigger,
    string $parentFloatable,
    string $childTrigger,
    string $childFloatable,
) {
    $this->visit('/_ws/test/ui/nesting')
        ->hover($parentTrigger)
        ->assertVisible($parentFloatable)
        ->hover($childTrigger)
        ->assertVisible($childFloatable)
        ->assertVisible($parentFloatable);
})->with('nested-floatables');

test('parent stays visible when hovering child interactive panel', function (
    string $parentTrigger,
    string $parentFloatable,
    string $childTrigger,
    string $childFloatable,
    string $childPanel,
) {
    $this->visit('/_ws/test/ui/nesting')
        ->hover($parentTrigger)
        ->assertVisible($parentFloatable)
        ->hover($childTrigger)
        ->assertVisible($childFloatable)
        ->hover($childPanel)
        ->assertVisible($childFloatable)
        ->assertVisible($parentFloatable);
})->with('nested-interactive-children');

test('teleported parent stays visible when showing child', function (
    string $parentTrigger,
    string $parentFloatable,
    string $childTrigger,
    string $childFloatable,
) {
    $this->visit('/_ws/test/ui/nesting')
        ->hover($parentTrigger)
        ->assertVisible($parentFloatable)
        ->hover($childTrigger)
        ->assertVisible($childFloatable)
        ->assertVisible($parentFloatable);
})->with('nested-teleported-parent');

test('teleported parent stays visible when hovering child interactive panel', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->hover('#parent-tpf-trigger')
        ->assertVisible('.panel-tpf')
        ->hover('#child-tpf-trigger')
        ->assertVisible('#child-tpf > [data-ws-floatable]')
        ->hover('#child-tpf-panel')
        ->assertVisible('#child-tpf > [data-ws-floatable]')
        ->assertVisible('.panel-tpf');
});

// --- Teleported child inside a parent ---

test('hovering a teleported child panel keeps the parent open', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->hover('#nest-hover-parent-trigger')
        ->assertVisible('.panel-hover-parent')
        ->hover('#nest-hover-child-trigger')
        ->assertVisible('.panel-hover-child')
        ->hover('#nest-hover-inner')
        // The hover-out delay is 50ms and the leave transition lasts longer: assert on the show
        // class, which hide() drops synchronously, rather than on visibility.
        ->wait(0.5)
        ->assertScript("document.querySelector('.panel-hover-parent').classList.contains('show')");
});

test('clicking inside a teleported child panel keeps the parent open', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#nest-click-parent-trigger')
        ->assertVisible('.panel-click-parent')
        ->click('#nest-click-child-trigger')
        ->assertVisible('.panel-click-child')
        ->click('#nest-click-inner')
        ->wait(0.5)
        ->assertScript("document.querySelector('.panel-click-parent').classList.contains('show')")
        ->assertScript("document.querySelector('.panel-click-child').classList.contains('show')");
});

// --- Dismiss in nested contexts ---

test('dismiss in a nested child closes the child, not the parent', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#parent-dismiss-trigger')
        ->assertVisible('#parent-dismiss > [data-ws-floatable]')
        ->click('#child-dismiss-trigger')
        ->assertVisible('#child-dismiss > [data-ws-floatable]')
        ->click('#child-dismiss-btn')
        ->assertScript(js_wait_hidden('#child-dismiss > [data-ws-floatable]'))
        // hide() drops the show class synchronously, while display:none only lands after the
        // transition: asserting on the class catches a parent that was closed along with the child.
        ->assertScript("document.querySelector('#parent-dismiss > [data-ws-floatable]').classList.contains('show')");
});

test('dismiss in the parent content closes the parent', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#parent-dismiss-trigger')
        ->assertVisible('#parent-dismiss > [data-ws-floatable]')
        ->click('#parent-dismiss-btn')
        ->assertScript(js_wait_hidden('#parent-dismiss > [data-ws-floatable]'));
});

test('floating-stack in a nested child closes the whole chain', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#parent-dismiss-trigger')
        ->assertVisible('#parent-dismiss > [data-ws-floatable]')
        ->click('#child-dismiss-trigger')
        ->assertVisible('#child-dismiss > [data-ws-floatable]')
        ->click('#child-stack-btn')
        ->assertScript(js_wait_hidden('#child-dismiss > [data-ws-floatable]'))
        ->assertScript(js_wait_hidden('#parent-dismiss > [data-ws-floatable]'));
});

test('floating-stack walks past a teleported parent', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#parent-tstack-trigger')
        ->assertVisible('.panel-tstack')
        ->click('#child-tstack-trigger')
        ->assertVisible('#child-tstack > [data-ws-floatable]')
        ->click('#child-tstack-btn')
        ->assertScript(js_wait_hidden('#child-tstack > [data-ws-floatable]'))
        ->assertScript(js_wait_hidden('.panel-tstack'));
});

test('dismiss in a teleported child closes that child', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#parent-tdismiss-trigger')
        ->assertVisible('#parent-tdismiss > [data-ws-floatable]')
        ->click('#child-tdismiss-trigger')
        ->assertVisible('.panel-tdismiss')
        ->click('#child-tdismiss-btn')
        ->assertScript(js_wait_hidden('.panel-tdismiss'));
});

test('dismiss on a flyout inside a modal leaves the modal open', function () {
    $this->visit('/_ws/test/ui/nesting')
        ->click('#modal-nest-trigger')
        ->assertVisible('#modal-nest')
        ->click('#flyout-in-modal-trigger')
        ->assertVisible('#flyout-in-modal > [data-ws-floatable]')
        ->click('#flyout-in-modal-dismiss')
        ->assertScript(js_wait_hidden('#flyout-in-modal > [data-ws-floatable]'))
        ->assertScript("document.getElementById('modal-nest').style.display !== 'none'")
        ->assertVisible('#modal-nest');
});
