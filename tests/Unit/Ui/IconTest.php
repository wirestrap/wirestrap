<?php

test('renders i element', function () {
    $rendered = (string) $this->blade('<x-wirestrap::icon icon="person" />');

    expect($rendered)->toContain('<i ');
});

test('string icon is used as class', function () {
    $this->blade('<x-wirestrap::icon icon="person" />')
        ->assertSee('person', false);
});

test('merges config base class with icon string', function () {
    $rendered = (string) $this->blade('<x-wirestrap::icon icon="person" />');

    // Config default: 'bi'
    expect($rendered)->toContain('bi');
    expect($rendered)->toContain('person');
});

test('array icon spreads as attributes', function () {
    $this->blade('<x-wirestrap::icon :icon="[\'class\' => \'custom-icon\', \'data-name\' => \'star\']" />')
        ->assertSee('custom-icon', false)
        ->assertSee('data-name="star"', false);
});

test('array icon merges config base class', function () {
    $rendered = (string) $this->blade('<x-wirestrap::icon :icon="[\'class\' => \'my-icon\']" />');

    expect($rendered)->toContain('bi');
    expect($rendered)->toContain('my-icon');
});

test('empty string icon still renders with base class', function () {
    $rendered = (string) $this->blade('<x-wirestrap::icon icon="" />');

    expect($rendered)->toContain('bi');
});

// --- Config: iconComponent ---

test('iconComponent returns default component path', function () {
    expect(\Wirestrap\Wirestrap::iconComponent())->toBe('wirestrap::ui.icon');
});

test('iconComponent returns custom path from config', function () {
    config()->set('wirestrap.icon.component_path', 'my-app.custom-icon');

    expect(\Wirestrap\Wirestrap::iconComponent())->toBe('my-app.custom-icon');
});

$withIcon = [
    'input' => '<x-wirestrap::input icon="bi-star" />',
    'textarea' => '<x-wirestrap::textarea icon="bi-star" />',
    'select' => '<x-wirestrap::select icon="bi-star" id="test" :options="[\'a\' => \'A\']" />',
    'autocomplete' => '<x-wirestrap::autocomplete icon="bi-star" id="test" />',
    'table column' => '<x-wirestrap::table :columns="[[\'label\' => \'Name\', \'icon\' => \'bi-star\']]"><tr><td>Row</td></tr></x-wirestrap::table>',
    'table bulk action' => '<x-wirestrap::table :columns="[\'Name\']" wire-model-bulk="ids" :bulk-actions="[[\'label\' => \'Delete\', \'icon\' => \'bi-star\', \'wire:click\' => \'delete\']]"><tr><td>Row</td></tr></x-wirestrap::table>',
    'tabs' => '<x-wirestrap::tabs id="test"><x-slot:tab label="A" icon="bi-star">content</x-slot:tab></x-wirestrap::tabs>',
    'accordion' => '<x-wirestrap::accordion id="test"><x-slot:item label="A" icon="bi-star">content</x-slot:item></x-wirestrap::accordion>',
];

dataset('with icon', $withIcon);

test('component renders icon via iconComponent', function (string $blade) {
    $rendered = (string) $this->withViewErrors([])
        ->blade($blade);

    expect($rendered)->toContain('<i ');
    expect($rendered)->toContain('bi-star');
})->with('with icon');
