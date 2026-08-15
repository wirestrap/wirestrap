<?php

use Illuminate\View\ViewException;

$withoutId = [
    'accordion' => '<x-wirestrap::accordion><x-slot:item label="A">content</x-slot:item></x-wirestrap::accordion>',
    'tabs' => '<x-wirestrap::tabs><x-slot:tab label="A">content</x-slot:tab></x-wirestrap::tabs>',
    'modal' => '<x-wirestrap::modal>content</x-wirestrap::modal>',
    'slider' => '<x-wirestrap::slider><x-slot:item>content</x-slot:item></x-wirestrap::slider>',
    'menu' => '<x-wirestrap::menu><x-slot:item label="A" href="#">link</x-slot:item></x-wirestrap::menu>',
    'select' => '<x-wirestrap::select :options="[\'a\' => \'A\']" />',
];

$withId = [
    'accordion' => '<x-wirestrap::accordion id="test"><x-slot:item label="A">content</x-slot:item></x-wirestrap::accordion>',
    'tabs' => '<x-wirestrap::tabs id="test"><x-slot:tab label="A">content</x-slot:tab></x-wirestrap::tabs>',
    'modal' => '<x-wirestrap::modal id="test">content</x-wirestrap::modal>',
    'slider' => '<x-wirestrap::slider id="test"><x-slot:item>content</x-slot:item></x-wirestrap::slider>',
    'menu' => '<x-wirestrap::menu id="test"><x-slot:item label="A" href="#">link</x-slot:item></x-wirestrap::menu>',
    'select' => '<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />',
];

dataset('without id', $withoutId);
dataset('with id', $withId);

test('strict mode throws when id is missing', function (string $blade) {
    config()->set('wirestrap.strict_mode', true);

    $this->expectException(ViewException::class);
    $this->expectExceptionMessage('Missing ID');

    $this->withViewErrors([])->blade($blade);
})->with('without id');

test('strict mode passes when id is provided', function (string $blade) {
    config()->set('wirestrap.strict_mode', true);

    $this->expectNotToPerformAssertions();
    $this->withViewErrors([])->blade($blade);
})->with('with id');

test('no exception when strict mode is disabled', function (string $blade) {
    config()->set('wirestrap.strict_mode', false);

    $this->expectNotToPerformAssertions();
    $this->withViewErrors([])->blade($blade);
})->with('without id');
