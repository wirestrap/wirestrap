<?php

use Tests\BrowserTestCase;

uses(BrowserTestCase::class)->in('Browser');
uses(Tests\UnitTestCase::class)->in('Unit');

require __DIR__ . '/Browser/helpers.php';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Check whether $needle appears as a descendant of the element matching $parentClass
 * in the rendered HTML. Uses DOMDocument for reliable parsing regardless of tag types.
 *
 * When $parentClass is null, targets the root (first child) element.
 */
function htmlContainsInside(string $html, ?string $parentClass, string $needle): bool
{
    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);
    $xpath = new DOMXPath($doc);

    if ($parentClass === null) {
        // First element child of body (the component root)
        $parents = $xpath->query('//body/*[1]');
    } else {
        $parents = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' {$parentClass} ')]");
    }

    if ($parents === false || $parents->length === 0) {
        return false;
    }

    $parentHtml = $doc->saveHTML($parents->item(0));

    return str_contains($parentHtml, $needle);
}

/**
 * Get an attribute value from the first element matching the given selector.
 *
 * @param string $selector CSS class name (e.g. 'ws-form-floating') or data-attribute selector (e.g. 'data-ws-tab=alpha')
 */
function htmlGetAttribute(string $html, string $selector, string $attribute): ?string
{
    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);
    $xpath = new DOMXPath($doc);

    if (str_contains($selector, '=')) {
        [$attr, $value] = explode('=', $selector, 2);
        $query = "//*[@{$attr}=\"{$value}\"]";
    } else {
        $query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$selector} ')]";
    }

    $elements = $xpath->query($query);

    if ($elements === false || $elements->length === 0) {
        return null;
    }

    $element = $elements->item(0);

    return $element->hasAttribute($attribute) ? $element->getAttribute($attribute) : null;
}

/**
 * Get the decoded JSON value of a data-attribute from the first element that has it.
 */
function htmlGetJsonAttribute(string $html, string $attribute): mixed
{
    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);
    $xpath = new DOMXPath($doc);

    $elements = $xpath->query("//*[@{$attribute}]");

    if ($elements === false || $elements->length === 0) {
        return null;
    }

    return json_decode($elements->item(0)->getAttribute($attribute), true);
}

/**
 * Count the number of elements that have the given attribute.
 */
function htmlCountAttribute(string $html, string $attribute): int
{
    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);

    $count = 0;
    foreach ($doc->getElementsByTagName('*') as $element) {
        if ($element->hasAttribute($attribute)) {
            $count++;
        }
    }

    return $count;
}

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

expect()->extend('toHaveAttributeOn', function (string $elementClass, string $attribute): Pest\Expectation {
    $html = (string) $this->value;

    $doc = new DOMDocument();
    @$doc->loadHTML('<meta charset="utf-8">' . $html);
    $xpath = new DOMXPath($doc);

    $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' {$elementClass} ')]");

    expect($elements->length)->toBeGreaterThan(0, "No element with class '{$elementClass}' found");
    expect($elements->item(0)->hasAttribute($attribute))->toBeTrue(
        "Element with class '{$elementClass}' does not have attribute '{$attribute}'"
    );

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
