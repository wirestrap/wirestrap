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
    'teleported flyout > tooltip' => ['#parent-tp-trigger', '[data-ws-float-for="parent-tp"]', '#child-tp-trigger', '#child-tp-tip'],
    'teleported flyout > flyout' => ['#parent-tpf-trigger', '[data-ws-float-for="parent-tpf"]', '#child-tpf-trigger', '#child-tpf > [data-ws-floatable]'],
]);

test('parent stays visible when showing child', function (string $parentTrigger, string $parentFloatable, string $childTrigger, string $childFloatable) {
    $this->visit('/_ws/test/ui/nesting')
        ->hover($parentTrigger)
        ->assertVisible($parentFloatable)
        ->hover($childTrigger)
        ->assertVisible($childFloatable)
        ->assertVisible($parentFloatable);
})->with('nested-floatables');

test('parent stays visible when hovering child interactive panel', function (string $parentTrigger, string $parentFloatable, string $childTrigger, string $childFloatable, string $childPanel) {
    $this->visit('/_ws/test/ui/nesting')
        ->hover($parentTrigger)
        ->assertVisible($parentFloatable)
        ->hover($childTrigger)
        ->assertVisible($childFloatable)
        ->hover($childPanel)
        ->assertVisible($childFloatable)
        ->assertVisible($parentFloatable);
})->with('nested-interactive-children');

test('teleported parent stays visible when showing child', function (string $parentTrigger, string $parentFloatable, string $childTrigger, string $childFloatable) {
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
        ->assertVisible('[data-ws-float-for="parent-tpf"]')
        ->hover('#child-tpf-trigger')
        ->assertVisible('#child-tpf > [data-ws-floatable]')
        ->hover('#child-tpf-panel')
        ->assertVisible('#child-tpf > [data-ws-floatable]')
        ->assertVisible('[data-ws-float-for="parent-tpf"]');
});
