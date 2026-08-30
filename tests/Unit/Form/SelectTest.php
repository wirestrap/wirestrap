<?php

// --- Element ---

test('renders ws-select class', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    // Token match: a substring assertion would also match ws-select-dropdown.
    expect(htmlHasClass($html, 'ws-select'))->toBeTrue();
});

test('renders x-data wsSelect', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('x-data="wsSelect"', false);
});

test('renders x-bind select', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('x-bind="select"', false);
});

test('renders ws-form-select class on selection', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-form-select', false);
});

// --- wire:key ---

test('wire:key format is ws-select-{id}', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="my-sel" :options="[\'a\' => \'A\']" />')
        ->assertSee('wire:key="ws-select-my-sel"', false);
});

// --- wire:ignore ---

test('selection div has wire:ignore', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    // wire:ignore on the selection div (x-bind="selection")
    expect($html)->toContain('wire:ignore');
});

test('multiple wire:ignore elements present', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    // selection div, dropdown wrapper, and option list all have wire:ignore
    preg_match_all('/wire:ignore(?!\.self)/', $html, $matches);
    expect(count($matches[0]))->toBeGreaterThanOrEqual(3);
});

// --- Label ---

test('renders label from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick one" />')
        ->assertSee('ws-form-label', false)
        ->assertSee('Pick one');
});

test('renders label from slot', function () {
    $this->withViewErrors([])
        ->blade('
            <x-wirestrap::select id="test" :options="[\'a\' => \'A\']">
                <x-slot:label>Rich <strong>label</strong></x-slot:label>
            </x-wirestrap::select>
        ')
        ->assertSee('<strong>label</strong>', false);
});

test('no label element when label is absent', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertDontSee('ws-form-label', false);
});

test('label id matches {id}-label', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick" />')
        ->assertSee('id="test-label"', false);
});

test('non-floating label renders before select', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick" />')
        ->__toString();

    $labelPos = strpos($html, 'ws-form-label');
    $selectPos = strpos($html, 'ws-select');

    expect($labelPos)->toBeLessThan($selectPos);
});

test('floating label renders after select', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick" floating />')
        ->__toString();

    $selectPos = strpos($html, 'ws-select');
    $labelPos = strpos($html, 'ws-form-label');

    expect($selectPos)->toBeLessThan($labelPos);
});

// --- Floating ---

test('floating adds ws-form-floating class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick" floating />')
        ->assertSee('ws-form-floating', false);
});

test('no floating class by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertDontSee('ws-form-floating', false);
});

// --- Placeholder ---

test('placeholder span rendered with ws-selection-placeholder class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-selection-placeholder', false);
});

test('placeholder text from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" placeholder="Choose..." />')
        ->assertSee('Choose...');
});

test('placeholder defaults to config value', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('Select an option');
});

test('placeholder span carries the placeholder text', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" placeholder="Choose..." />')
        ->__toString();

    // Assert the span content: the text also appears in data-ws-placeholder, so a
    // page-wide assertSee() cannot tell whether the span itself renders it.
    expect(htmlText($html, 'ws-selection-placeholder'))->toBe('Choose...');
});

// --- Icon ---

test('icon renders icon element', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->assertSee('ws-form-input-icon', false);
});

test('icon is always start placement', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->assertSee('ws-form-input-icon-start', false);
});

test('icon adds ws-form-has-icon-start on selection', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->__toString();

    // ws-form-has-icon-start appears on the selection div
    expect($html)->toContain('ws-form-select ws-form-has-icon-start');
});

test('icon has aria-hidden', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->__toString();

    expect($html)->toHaveAttributeOn('ws-form-input-icon', 'aria-hidden');
});

test('non-floating icon wraps in select-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->assertSee('ws-form-select-wrapper', false);
});

test('floating icon does not use select-wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" label="Pick" floating />')
        ->assertDontSee('ws-form-select-wrapper', false);
});

test('floating with icon adds icon class to wrapper', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" icon="bi-search" label="Pick" floating />')
        ->assertSee('ws-form-floating ws-form-has-icon-start', false);
});

// --- Search ---

test('search renders search input', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" search />')
        ->assertSee('ws-search-container', false)
        ->assertSee('x-bind="searchInput"', false);
});

test('no search input by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertDontSee('ws-search-container', false);
});

test('search input has id {id}-search', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" search />')
        ->assertSee('id="test-search"', false);
});

test('search input has placeholder from prop', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" search search-placeholder="Find..." />')
        ->assertSee('placeholder="Find..."', false);
});

test('search input has default placeholder', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" search />')
        ->assertSee('placeholder="Search ..."', false);
});

test('search input has aria-label', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" search />')
        ->assertSee('aria-label="Search ..."', false);
});

// --- Dropdown ---

test('dropdown has ws-select-dropdown class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-select-dropdown', false);
});

test('dropdown has display none by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('style="display: none"', false);
});

test('dropdown has data-floating-placement bottom', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-floating-placement="bottom"', false);
});

test('dropdown has id {id}-listbox', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('id="test-listbox"', false);
});

test('dropdown has data-ws-floatable', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-floatable', false);
});

// --- Loader ---

test('loader div rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('x-bind="loader"', false);
});

// --- Data attributes ---

test('data-ws-wiremodel from wire:model', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" wire:model="field" />')
        ->assertSee('data-ws-wiremodel="field"', false);
});

test('data-ws-multiple true when multiple', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" multiple />')
        ->assertSee('data-ws-multiple="true"', false);
});

test('data-ws-multiple false by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-multiple="false"', false);
});

test('data-ws-placeholder rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" placeholder="Choose..." />')
        ->assertSee('data-ws-placeholder="Choose..."', false);
});

test('data-ws-empty-options-label rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-empty-options-label="No results"', false);
});

test('data-ws-live true when live', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" live />')
        ->assertSee('data-ws-live="true"', false);
});

test('data-ws-live false by default', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-live="false"', false);
});

test('data-ws-dropdown-offset rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" :dropdown-offset="5" />')
        ->assertSee('data-ws-dropdown-offset="5"', false);
});

test('data-ws-dropdown-offset defaults to zero', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-dropdown-offset="0"', false);
});

test('data-ws-position rendered', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" position="fixed" />')
        ->assertSee('data-ws-position="fixed"', false);
});

test('data-ws-position defaults to absolute', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('data-ws-position="absolute"', false);
});

test('data-ws-empty-value rendered when set', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" empty-value="0" />')
        ->assertSee('data-ws-empty-value="0"', false);
});

test('no data-ws-empty-value when not set', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-empty-value');
});

// --- Options (inline) ---

test('short form options normalized to JSON', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="$options" />', [
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
        ])
        ->assertSee('data-ws-options="', false);

    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="$options" />', [
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
        ])
        ->__toString();

    expect(htmlGetJsonAttribute($html, 'data-ws-options'))->toBe([
        ['value' => 'active', 'label' => 'Active'],
        ['value' => 'inactive', 'label' => 'Inactive'],
    ]);
});

test('full form options rendered as JSON', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="$options" />', [
            'options' => [
                ['value' => 'fr', 'label' => 'France', 'optgroup' => 'Europe'],
                ['value' => 'us', 'label' => 'United States'],
            ],
        ])
        ->__toString();

    expect(htmlGetJsonAttribute($html, 'data-ws-options'))->toBe([
        ['value' => 'fr', 'label' => 'France', 'optgroup' => 'Europe'],
        ['value' => 'us', 'label' => 'United States'],
    ]);
});

test('no data-ws-options when options is null', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-options');
});

// --- wire-options ---

test('data-ws-wire-options rendered with method name', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" wire-options="getCountries" />')
        ->assertSee('data-ws-wire-options="getCountries"', false);
});

test('data-ws-wire-options-params with array form', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :wire-options="$wireOptions" />', [
            'wireOptions' => ['getCities', ['ws-wire' => 'country'], 'active'],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options="getCities"');
    expect(htmlGetJsonAttribute($html, 'data-ws-wire-options-params'))->toBe([['ws-wire' => 'country'], 'active']);
});

test('no wire-options attributes when null', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-wire-options');
});

// --- wire-options-watch ---

test('data-ws-wire-options-watch rendered with string', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" wire-options="getItems" wire-options-watch="category" />')
        ->assertSee('data-ws-wire-options-watch="category"', false);
});

test('data-ws-wire-options-reset rendered with array form', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" wire-options="getItems" :wire-options-watch="$watch" />', [
            'watch' => ['category', null],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options-watch="category"');
    expect($html)->toContain('data-ws-wire-options-reset="null"');
});

test('data-ws-wire-options-reset-live rendered', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" wire-options="getItems" :wire-options-watch="$watch" />', [
            'watch' => ['category', null, true],
        ])
        ->__toString();

    expect($html)->toContain('data-ws-wire-options-reset-live="true"');
});

test('no wire-options-watch attributes when null', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('data-ws-wire-options-watch');
    expect($html)->not->toContain('data-ws-wire-options-reset');
});

// --- ARIA ---

test('selection has aria-labelledby {id}-label', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" label="Pick" />')
        ->assertSee('aria-labelledby="test-label"', false);
});

test('selection has aria-controls {id}-listbox', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->assertSee('aria-controls="test-listbox"', false);
});

test('aria-owns rendered when teleport is set', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" teleport="body" />')
        ->assertSee('aria-owns="test-listbox"', false);
});

test('no aria-owns without teleport', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('aria-owns');
});

test('teleport wraps the dropdown in an x-teleport template', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" teleport="body" />')
        ->__toString();

    expect($html)->toContain('<template x-teleport="body">');

    // The dropdown must sit inside the template, not next to it.
    $template = substr($html, strpos($html, '<template x-teleport="body">'));
    expect(strpos($template, 'ws-select-dropdown'))->not->toBeFalse()
        ->and(strpos($template, 'ws-select-dropdown'))->toBeLessThan(strpos($template, '</template>'));
});

test('no teleport template when teleport is not set', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('x-teleport');
});

test('no aria-labelledby without label', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" />')
        ->__toString();

    expect($html)->not->toContain('aria-labelledby');
});

// --- Disabled ---

test('disabled attribute is rendered on ws-select div', function () {
    $html = $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" disabled />')
        ->__toString();

    $doc = new DOMDocument();
    @$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $xpath = new DOMXPath($doc);
    $el = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' ws-select ')]")->item(0);

    expect($el->hasAttribute('disabled'))->toBeTrue();
});

// --- Validation ---

test('invalid class on selection when error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-form-invalid', false);
});

test('no invalid class when no error', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('validation message rendered when has error', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
});

test('no validation message when has-validation-message is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" :has-validation-message="false" />')
        ->assertDontSee('ws-form-feedback-invalid', false);
});

test('no invalid class when has-validation is false', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" :has-validation="false" />')
        ->assertDontSee('ws-form-invalid', false);
});

test('floating error message renders outside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" floating label="Pick" />')
        ->__toString();

    // Feedback must NOT be inside the floating wrapper
    expect(htmlContainsInside($html, 'ws-form-floating', 'ws-form-feedback-invalid'))->toBeFalse();
});

test('non-floating error message renders inside wrapper', function () {
    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" />')
        ->__toString();

    // Feedback must be inside the root wrapper
    expect(htmlContainsInside($html, null, 'ws-form-feedback-invalid'))->toBeTrue();
});

test('non-floating with icon: invalid class on inner wrapper', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" icon="bi-search" />')
        ->assertSee('ws-form-select-wrapper ws-form-invalid', false);
});

test('non-floating without icon: invalid class on ws-select', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" />')
        ->assertSee('ws-select ws-form-invalid', false);
});

test('floating with error: invalid class on floating wrapper', function () {
    $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" floating label="Pick" />')
        ->assertSee('ws-form-floating', false)
        ->assertSee('ws-form-invalid', false);

    $html = $this->withViewErrors(['field' => 'Required'])
        ->blade('<x-wirestrap::select id="test" wire:model="field" :options="[\'a\' => \'A\']" floating label="Pick" />')
        ->__toString();

    expect(htmlGetAttribute($html, 'ws-form-floating', 'class'))->toContain('ws-form-invalid');
});

// --- Default attributes ---

test('default-attributes are merged onto root', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::select id="test" :options="[\'a\' => \'A\']" :default-attributes="[\'data-custom\' => \'val\']" />')
        ->assertSee('data-custom="val"', false);
});
