<?php

use Tests\BrowserTestCase;

uses(BrowserTestCase::class)->in('Browser');
uses(Tests\UnitTestCase::class)->in('Unit');

require __DIR__ . '/Browser/helpers.php';

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toUseWireIgnoreSelf', function (): Pest\Expectation {
    $html = (string) $this->value;

    expect($html)->toContain('wire:ignore.self');

    $withoutSelf = str_replace('wire:ignore.self', '', $html);
    expect($withoutSelf)->not->toContain('wire:ignore');

    return $this;
});

expect()->extend('toHaveUniqueWireKeys', function (): Pest\Expectation {
    $html = (string) $this->value;

    preg_match_all('/wire:key="([^"]+)"/', $html, $matches);

    expect($matches[1])->not->toBeEmpty('No wire:key attributes found');

    $duplicates = array_diff_assoc($matches[1], array_unique($matches[1]));
    expect($duplicates)->toBeEmpty('Duplicate wire:key values found: ' . implode(', ', $duplicates));

    return $this;
});
