<?php

// --- Livewire attributes ---

test('renders unique wire:key on columns and bulk actions', function () {
    $rendered = $this->blade('
        <x-wirestrap::table
            id="t"
            :columns="[\'Name\', \'Email\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\'], [\'label\' => \'Export\']]"
        >
            <tr><td>A</td><td>B</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->toHaveUniqueWireKeys();
});

// --- Root element ---

test('renders x-data wsTable on root', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')->assertSee('x-data="wsTable"', false);
});

test('renders tooltip config data attributes on root', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-tip="ws-tooltip"', false)
    ->assertSee('data-ws-tip-arrow="ws-tooltip-arrow"', false)
    ->assertSee('data-ws-tip-inner="ws-tooltip-content"', false);
});

// --- Data attributes ---

test('data-ws-animate defaults to false', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-animate="false"', false);
});

test('data-ws-animate absent when animate enabled', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" animate>
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('data-ws-animate="false"', false);
});

test('data-ws-model set when wire-model-bulk provided', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" wire-model-bulk="selectedIds">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-model="selectedIds"', false);
});

test('data-ws-model absent without wire-model-bulk', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('data-ws-model', false);
});

// --- Inner wrapper ---

test('forwards id to inner div', function () {
    $this->blade('
        <x-wirestrap::table id="my-table" :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('id="my-table"', false);
});

test('responsive wrapper applied by default', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('ws-table-responsive', false);
});

test('responsive can be disabled', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" :responsive="false">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('ws-table-responsive', false);
});

test('forwards custom class to inner div', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" class="custom-table">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('custom-table', false);
});

test('forwards extra attributes to inner div', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" data-custom="value">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-custom="value"', false);
});

// --- Table ---

test('renders ws-table class on table element', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->toContain('<table class="ws-table">');
});

// --- Columns ---

test('string columns render as th with scope col', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\', \'Email\']">
            <tr><td>A</td><td>B</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('scope="col"', false)
    ->assertSee('Name')
    ->assertSee('Email');
});

test('truncate enabled by default adds ws-th-truncate', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('ws-th-truncate', false);
});

test('truncate false removes ws-th-truncate', function () {
    $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'truncate\' => false]]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('ws-th-truncate', false);
});

test('truncated column has data-ws-label with label text', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-label="Name"', false);
});

test('no data-ws-label when truncate false', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'truncate\' => false]]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->not->toContain('data-ws-label');
});

test('custom tooltip sets data-ws-tip-always and data-ws-label', function () {
    $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Col\', \'tooltip\' => \'Custom tip\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-tip-always', false)
    ->assertSee('data-ws-label="Custom tip"', false);
});

test('column class applied to span', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'class\' => \'text-end\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->toContain('class="text-end"');
});

test('column html renders raw content', function () {
    $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'html\' => \'<em class=extra>*</em>\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('<em class=extra>*</em>', false);
});

test('column icon renders icon component', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'icon\' => \'person\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->toContain('<i');
});

test('column icon-placement end renders icon after label', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Status\', \'icon\' => \'check\', \'icon-placement\' => \'end\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    // Within the th>span, label text should come before the <i> icon
    expect($rendered)->toMatch('/<span[^>]*>.*Status.*<i[^>]*>.*<\/i>.*<\/span>/s');
});

test('column icon-placement start renders icon before label', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'icon\' => \'person\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    // Within the th>span, the <i> icon should come before the label text
    expect($rendered)->toMatch('/<span[^>]*>.*<i[^>]*>.*<\/i>.*Name.*<\/span>/s');
});

test('extra column attributes spread on th', function () {
    $this->blade('
        <x-wirestrap::table :columns="[[\'label\' => \'Name\', \'data-sort\' => \'asc\']]">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-sort="asc"', false);
});

// --- Select-all ---

test('select-all checkbox rendered when wire-model-bulk with columns', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" wire-model-bulk="ids">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('x-bind="wsBulkSelectAll"', false)
    ->assertSee('aria-label="Select all"', false);
});

test('no select-all checkbox without wire-model-bulk', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('wsBulkSelectAll', false);
});

// --- Slots ---

test('head slot renders when no columns', function () {
    $this->blade('
        <x-wirestrap::table>
            <x-slot:head><tr><th>Custom Head</th></tr></x-slot:head>
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('thead', false)
    ->assertSee('Custom Head');
});

test('head slot not used when columns provided', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <x-slot:head><tr><th>Custom Head</th></tr></x-slot:head>
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('Custom Head')
    ->assertSee('Name');
});

test('foot slot renders in tfoot', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
            <x-slot:foot><tr><td>Footer</td></tr></x-slot:foot>
        </x-wirestrap::table>
    ')
    ->assertSee('tfoot', false)
    ->assertSee('Footer');
});

test('caption renders as caption element', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" caption="Table Title">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('<caption>', false)
    ->assertSee('Table Title');
});

// --- Empty state ---

test('data-ws-empty row always rendered', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-empty', false);
});

test('default empty label from config', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
        </x-wirestrap::table>
    ')
    ->assertSee('No results');
});

test('custom empty label', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" empty-label="Nothing here">
        </x-wirestrap::table>
    ')
    ->assertSee('Nothing here');
});

test('empty label disabled with empty string', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" :empty-label="\'\'">
        </x-wirestrap::table>
    ')
    ->assertDontSee('No results')
    ->assertSee('data-ws-empty', false);
});

test('empty row has ws-table-empty class', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']">
        </x-wirestrap::table>
    ')
    ->assertSee('ws-table-empty', false);
});

test('empty colspan matches column count', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[\'Name\', \'Email\', \'Role\']">
        </x-wirestrap::table>
    ');

    expect($rendered)->toContain('colspan="3"');
});

test('empty colspan includes bulk checkbox column', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[\'Name\', \'Email\']" wire-model-bulk="ids">
        </x-wirestrap::table>
    ');

    expect($rendered)->toContain('colspan="3"');
});

test('empty row gets data-ws-animate when animate enabled', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" animate>
        </x-wirestrap::table>
    ');

    // The empty row: <tr data-ws-empty data-ws-animate>
    expect($rendered)->toMatch('/data-ws-empty\s+data-ws-animate/');
});

// --- Bulk actions ---

test('bulk container rendered when wire-model-bulk and bulk-actions', function () {
    $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('data-ws-bulk-container', false);
});

test('bulk container has wire:ignore.self', function () {
    $rendered = $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    expect($rendered)->toUseWireIgnoreSelf();
});

test('bulk container hidden by default via inline style', function () {
    $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('style="display: none"', false);
});

test('bulk action buttons rendered from array', function () {
    $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\', \'class\' => \'btn-danger\', \'wire:click\' => \'deleteSelected\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('Delete')
    ->assertSee('btn-danger', false)
    ->assertSee('wire:click="deleteSelected"', false);
});

test('bulk actions bar has ws-table-bulk-actions class', function () {
    $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Delete\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('ws-table-bulk-actions', false);
});

test('bulk action icon renders with placement', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            wire-model-bulk="ids"
            :bulk-actions="[[\'label\' => \'Remove\', \'icon\' => \'trash\', \'icon-placement\' => \'end\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ');

    $labelPos = strpos($rendered, 'Remove');
    $iconPos = strpos($rendered, '<i', $labelPos - 50);
    // Icon should be after label for end placement
    expect($iconPos)->toBeGreaterThan($labelPos);
});

test('bulk-actions slot renders custom content', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" wire-model-bulk="ids">
            <x-slot:bulk-actions>
                <button id="custom-bulk-btn">Custom Action</button>
            </x-slot:bulk-actions>
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertSee('Custom Action')
    ->assertSee('ws-table-bulk-actions', false);
});

test('no bulk container without wire-model-bulk', function () {
    $this->blade('
        <x-wirestrap::table
            :columns="[\'Name\']"
            :bulk-actions="[[\'label\' => \'Delete\']]"
        >
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('data-ws-bulk-container', false);
});

test('no bulk container without bulk-actions', function () {
    $this->blade('
        <x-wirestrap::table :columns="[\'Name\']" wire-model-bulk="ids">
            <tr><td>A</td></tr>
        </x-wirestrap::table>
    ')
    ->assertDontSee('data-ws-bulk-container', false);
});

// --- table.check ---

test('table.check renders checkbox in td', function () {
    $rendered = (string) $this->blade('
        <x-wirestrap::table.check value="1" />
    ');

    expect($rendered)
        ->toContain('<td')
        ->toContain('type="checkbox"');
});

test('table.check has data-ws-bulk attribute', function () {
    $this->blade('
        <x-wirestrap::table.check value="1" />
    ')
    ->assertSee('data-ws-bulk', false);
});

test('table.check has wsBulkCheck binding', function () {
    $this->blade('
        <x-wirestrap::table.check value="1" />
    ')
    ->assertSee('x-bind="wsBulkCheck"', false);
});

test('table.check has ws-form-check-input class', function () {
    $this->blade('
        <x-wirestrap::table.check value="1" />
    ')
    ->assertSee('ws-form-check-input', false);
});

test('table.check class-td applies to td', function () {
    $this->blade('
        <x-wirestrap::table.check value="1" class-td="text-center" />
    ')
    ->assertSee('class="text-center"', false);
});

test('table.check forwards extra attributes to input', function () {
    $this->blade('
        <x-wirestrap::table.check value="42" data-row="test" />
    ')
    ->assertSee('value="42"', false)
    ->assertSee('data-row="test"', false);
});

test('table.check disabled prop', function () {
    $this->blade('
        <x-wirestrap::table.check value="1" disabled />
    ')
    ->assertSee('disabled', false);
});
