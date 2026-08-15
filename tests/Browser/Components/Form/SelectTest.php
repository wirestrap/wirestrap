<?php

// Helper: wait for Livewire lazy component to load
function waitForSelectPage(mixed $page): mixed
{
    return $page->visit('/_ws/test/form/select')
        ->assertScript(js_wait_for('#sel-single'));
}

// Helper: open a select and wait for options to appear
function openSelect(mixed $page, string $id): mixed
{
    return $page->click("#$id")
        ->assertScript(js_wait_for("#$id-listbox .ws-select-option"));
}

// --- Open / Close ---

test('clicking selection opens dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single-listbox').style.display !== 'none'
        "));
});

test('clicking selection again closes dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single')
        ->assertScript(js_wait_hidden('#sel-single-listbox'));
});

test('escape closes dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->keys('#sel-single', 'Escape')
        ->assertScript(js_wait_hidden('#sel-single-listbox'));
});

test('clicking outside closes dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->assertScript('
            new Promise(resolve => {
                document.body.click();
                resolve(true);
            })
        ')
        ->assertScript(js_wait_hidden('#sel-single-listbox'));
});

test('arrow down opens dropdown when closed', function () {
    $page = waitForSelectPage($this);
    $page->keys('#sel-keyboard', 'ArrowDown')
        ->assertScript(js_wait_for('#sel-keyboard-listbox .ws-select-option'));
});

test('arrow up opens dropdown when closed', function () {
    $page = waitForSelectPage($this);
    $page->keys('#sel-keyboard', 'ArrowUp')
        ->assertScript(js_wait_for('#sel-keyboard-listbox .ws-select-option'));
});

test('space opens dropdown when closed', function () {
    $page = waitForSelectPage($this);
    $page->keys('#sel-keyboard', ' ')
        ->assertScript(js_wait_for('#sel-keyboard-listbox .ws-select-option'));
});

// --- Single selection ---

test('clicking option selects it and closes dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single .ws-selection-simple span').textContent === 'Red'
        "));
});

test('selected option has aria-selected true', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="green"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'));

    // Re-open to check aria-selected
    openSelect($page, 'sel-single')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single-listbox [data-ws-value=\"green\"]').getAttribute('aria-selected') === 'true'
        "));
});

test('selecting different option replaces previous selection', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'));

    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="blue"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single .ws-selection-simple span').textContent === 'Blue'
        "));
});

test('single selection syncs to wire:model', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="green"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id')).get('single') === 'green'
        "));
});

// --- Multiple selection ---

test('multiple selection keeps dropdown open', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-multiple')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-multiple-listbox').style.display !== 'none'
        "));
});

test('multiple selection shows badges', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-multiple')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->click('#sel-multiple-listbox [data-ws-value="blue"]')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-multiple .ws-selection-badge').length === 2
        "));
});

test('clicking selected option in multiple deselects it', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-multiple')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->click('#sel-multiple-listbox [data-ws-value="blue"]')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-multiple .ws-selection-badge').length === 1
        "));
});

test('badge remove button deselects option', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-multiple')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->click('#sel-multiple-listbox [data-ws-value="blue"]');

    // Close dropdown first, then click remove button
    $page->keys('#sel-multiple', 'Escape')
        ->assertScript(js_wait_hidden('#sel-multiple-listbox'))
        ->click('#sel-multiple [data-ws-remove="red"]')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-multiple .ws-selection-badge').length === 1
        "));
});

test('multiple selection syncs array to wire:model', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-multiple')
        ->click('#sel-multiple-listbox [data-ws-value="red"]')
        ->click('#sel-multiple-listbox [data-ws-value="blue"]')
        ->assertScript(js_after_tick("
            (() => {
                const val = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id')).get('multiple');
                return Array.isArray(val) && val.length === 2 && val.includes('red') && val.includes('blue');
            })()
        "));
});

// --- Search ---

test('search input receives focus on open', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-search')
        ->assertScript(js_wait_focus('#sel-search-search'));
});

test('search filters options', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-search')
        ->fill('#sel-search-search', 're')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-search-listbox .ws-select-option').length === 2
        "));
});

test('clearing search shows all options', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-search')
        ->fill('#sel-search-search', 're')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-search-listbox .ws-select-option').length === 2
        "))
        ->fill('#sel-search-search', '')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-search-listbox .ws-select-option').length === 3
        "));
});

test('empty search results shows no results message', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-search')
        ->fill('#sel-search-search', 'zzzzz')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-search-listbox .ws-select-empty') !== null
        "));
});

test('escape in search closes dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-search')
        ->assertScript(js_wait_focus('#sel-search-search'))
        ->keys('#sel-search-search', 'Escape')
        ->assertScript(js_wait_hidden('#sel-search-listbox'));
});

// --- Keyboard navigation ---

test('arrow down highlights next option', function () {
    $page = waitForSelectPage($this);
    // After open, first option (Red) is highlighted at index 0.
    // ArrowDown should move highlight to index 1 (Green).
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard-listbox .ws-select-option.highlighted').dataset.wsValue === 'green'
        "));
});

test('enter selects highlighted option', function () {
    $page = waitForSelectPage($this);
    // ArrowDown moves from Red (0) to Green (1), Enter selects Green
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->keys('#sel-keyboard', 'Enter')
        ->assertScript(js_wait_hidden('#sel-keyboard-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard .ws-selection-simple span').textContent === 'Green'
        "));
});

test('keyboard navigation in search input', function () {
    $page = waitForSelectPage($this);
    // After open, Red (0) is highlighted. ArrowDown moves to Green (1).
    openSelect($page, 'sel-search')
        ->assertScript(js_wait_focus('#sel-search-search'))
        ->keys('#sel-search-search', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-search-listbox .ws-select-option.highlighted').dataset.wsValue === 'green'
        "))
        ->keys('#sel-search-search', 'Enter')
        ->assertScript(js_wait_hidden('#sel-search-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-search .ws-selection-simple span').textContent === 'Green'
        "));
});

test('arrow down wraps from last to first option', function () {
    $page = waitForSelectPage($this);
    // 3 options: Red (0), Green (1), Blue (2)
    // Open highlights Red (0). ArrowDown×3 = Green, Blue, Red (wrap)
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard-listbox .ws-select-option.highlighted').dataset.wsValue === 'red'
        "));
});

test('arrow up wraps from first to last option', function () {
    $page = waitForSelectPage($this);
    // Open highlights Red (0). ArrowUp wraps to Blue (last)
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'ArrowUp')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard-listbox .ws-select-option.highlighted').dataset.wsValue === 'blue'
        "));
});

test('mouseover updates highlight and keyboard continues from there', function () {
    $page = waitForSelectPage($this);
    // Open highlights Red (0). Hover Blue → highlight moves to Blue.
    // ArrowDown from Blue wraps to Red.
    openSelect($page, 'sel-keyboard')
        ->hover('#sel-keyboard-listbox [data-ws-value="blue"]')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard-listbox .ws-select-option.highlighted').dataset.wsValue === 'blue'
        "))
        ->keys('#sel-keyboard', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-keyboard-listbox .ws-select-option.highlighted').dataset.wsValue === 'red'
        "));
});

// --- Disabled ---

test('disabled select blocks pointer events', function () {
    $page = waitForSelectPage($this);
    $page->assertScript(js_after_tick("
        getComputedStyle(document.querySelector('#sel-disabled')).pointerEvents === 'none'
    "));
});

test('disabled select has tabindex -1', function () {
    $page = waitForSelectPage($this);
    $page->assertScript(js_after_tick("
        document.querySelector('#sel-disabled').getAttribute('tabindex') === '-1'
    "));
});

test('disabled option cannot be selected via keyboard', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-disabled-opt');

    // Initial highlight is Red (index 0). Green (index 1) is disabled.
    // One ArrowDown skips Green → lands on Blue
    $page->keys('#sel-disabled-opt', 'ArrowDown')
        ->assertScript(js_after_tick("
            (() => {
                const highlighted = document.querySelector('#sel-disabled-opt-listbox .ws-select-option.highlighted');
                return highlighted && highlighted.dataset.wsValue === 'blue';
            })()
        "));
});

test('disabled option has aria-disabled', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-disabled-opt')
        ->assertScript(js_after_tick("
            document.querySelector('#sel-disabled-opt-listbox [data-ws-value=\"green\"]').getAttribute('aria-disabled') === 'true'
        "));
});

// --- Static options ---

test('static options render without wire-options call', function () {
    $page = waitForSelectPage($this);
    $page->click('#sel-static')
        ->assertScript(js_wait_for('#sel-static-listbox .ws-select-option'))
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-static-listbox .ws-select-option').length === 3
        "));
});

test('selecting static option updates display', function () {
    $page = waitForSelectPage($this);
    $page->click('#sel-static')
        ->assertScript(js_wait_for('#sel-static-listbox .ws-select-option'))
        ->click('#sel-static-listbox [data-ws-value="fr"]')
        ->assertScript(js_wait_hidden('#sel-static-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-static .ws-selection-simple span').textContent === 'France'
        "));
});

// --- Empty value / ws-selected class ---

test('ws-selected not applied when value equals empty-value', function () {
    $page = waitForSelectPage($this);

    // emptyVal defaults to '0' which equals empty-value="0"
    $page->assertScript(js_after_tick("
        !document.querySelector('[wire\\\\:key=\"ws-select-sel-empty-val\"]').classList.contains('ws-selected')
    "));
});

test('ws-selected applied when real value selected', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-empty-val')
        ->click('#sel-empty-val-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#sel-empty-val-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('[wire\\\\:key=\"ws-select-sel-empty-val\"]').classList.contains('ws-selected')
        "));
});

// --- Placeholder ---

test('placeholder shown when no selection', function () {
    $page = waitForSelectPage($this);
    $page->assertScript(js_after_tick("
        document.querySelector('#sel-single .ws-selection-placeholder') !== null
    "));
});

test('placeholder hidden after selection', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single .ws-selection-placeholder') === null
        "));
});

// --- ws-select-open class ---

test('ws-select-open class added when dropdown is open', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->assertScript(js_after_tick("
            document.querySelector('[wire\\\\:key=\"ws-select-sel-single\"]').classList.contains('ws-select-open')
        "));
});

test('ws-select-open class removed when dropdown closes', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-single')
        ->click('#sel-single')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            !document.querySelector('[wire\\\\:key=\"ws-select-sel-single\"]').classList.contains('ws-select-open')
        "));
});

// --- Preloaded value ---

test('wire-options loads immediately when value is pre-selected', function () {
    $page = waitForSelectPage($this);

    // preloaded = 'green' on mount → wire-options should call getOptions()
    // immediately and render the selection label without opening the dropdown
    $page->assertScript(js_wait_for('#sel-preloaded .ws-selection-simple'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-preloaded .ws-selection-simple span').textContent === 'Green'
        "));
});

test('preloaded select has no placeholder', function () {
    $page = waitForSelectPage($this);
    $page->assertScript(js_wait_for('#sel-preloaded .ws-selection-simple'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-preloaded .ws-selection-placeholder') === null
        "));
});

// --- Rich content (html_prefix / html_suffix) ---

test('rich option renders html_prefix and html_suffix in dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-rich')
        ->assertScript(js_after_tick("
            (() => {
                const option = document.querySelector('#sel-rich-listbox [data-ws-value=\"alice\"] > div');
                return option.querySelector('i.bi-person') !== null
                    && option.querySelector('.ws-test-badge') !== null
                    && option.querySelector('span').textContent === 'Alice';
            })()
        "));
});

test('rich option without prefix/suffix renders plain label', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-rich')
        ->assertScript(js_after_tick("
            (() => {
                const option = document.querySelector('#sel-rich-listbox [data-ws-value=\"charlie\"] > div');
                return option.querySelector('i') === null
                    && option.querySelector('span').textContent === 'Charlie';
            })()
        "));
});

test('rich content renders in selection after picking option', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-rich')
        ->click('#sel-rich-listbox [data-ws-value="alice"]')
        ->assertScript(js_wait_hidden('#sel-rich-listbox'))
        ->assertScript(js_after_tick("
            (() => {
                const sel = document.querySelector('#sel-rich .ws-selection-simple');
                return sel.querySelector('i.bi-person') !== null
                    && sel.querySelector('.ws-test-badge') !== null
                    && sel.querySelector('span').textContent === 'Alice';
            })()
        "));
});

// --- Reactive static options (morph) ---

test('static options update after Livewire morph', function () {
    $page = waitForSelectPage($this);

    // Initially 2 options
    $page->click('#sel-reactive')
        ->assertScript(js_wait_for('#sel-reactive-listbox .ws-select-option'))
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-reactive-listbox .ws-select-option').length === 2
        "))
        ->click('#sel-reactive')
        ->assertScript(js_wait_hidden('#sel-reactive-listbox'));

    // Trigger Livewire action that extends options
    $page->click('#btn-extend')
        ->assertScript(js_wait_for('#sel-reactive-listbox'));

    // Wait for morph to complete, then re-open
    $page->assertScript("
        new Promise((resolve, reject) => {
            const deadline = Date.now() + 5000;
            const check = () => {
                const json = document.querySelector('[wire\\\\:key=\"ws-select-sel-reactive\"]').getAttribute('data-ws-options');
                const opts = JSON.parse(json);
                if (opts.length === 4) {
                    resolve(true);
                } else if (Date.now() > deadline) {
                    reject(new Error('Options not updated after morph'));
                } else {
                    requestAnimationFrame(check);
                }
            };
            check();
        })
    ");

    $page->click('#sel-reactive')
        ->assertScript(js_wait_for('#sel-reactive-listbox .ws-select-option'))
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-reactive-listbox .ws-select-option').length === 4
        "));
});

test('selection label updates when static options change after morph', function () {
    $page = waitForSelectPage($this);

    // Select "France" first
    $page->click('#sel-reactive')
        ->assertScript(js_wait_for('#sel-reactive-listbox .ws-select-option'))
        ->click('#sel-reactive-listbox [data-ws-value="fr"]')
        ->assertScript(js_wait_hidden('#sel-reactive-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-reactive .ws-selection-simple span').textContent === 'France'
        "));

    // Trigger morph — France is still in the new option set
    $page->click('#btn-extend')
        ->assertScript("
            new Promise((resolve, reject) => {
                const deadline = Date.now() + 5000;
                const check = () => {
                    const json = document.querySelector('[wire\\\\:key=\"ws-select-sel-reactive\"]').getAttribute('data-ws-options');
                    if (JSON.parse(json).length === 4) {
                        resolve(true);
                    } else if (Date.now() > deadline) {
                        reject(new Error('Options not updated'));
                    } else {
                        requestAnimationFrame(check);
                    }
                };
                check();
            })
        ");

    // Selection label should still show France
    $page->assertScript(js_after_tick("
        document.querySelector('#sel-reactive .ws-selection-simple span').textContent === 'France'
    "));

    // New options should be available in dropdown
    $page->click('#sel-reactive')
        ->assertScript(js_wait_for('#sel-reactive-listbox .ws-select-option'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-reactive-listbox [data-ws-value=\"es\"]') !== null
        "));
});

// --- Search accent normalization ---

test('search matches accented labels with unaccented query', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-accented')
        ->fill('#sel-accented-search', 'francois')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#sel-accented-listbox .ws-select-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'fr';
            })()
        "));
});

test('search matches unaccented labels with accented query', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-accented')
        ->fill('#sel-accented-search', 'joh')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#sel-accented-listbox .ws-select-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'en';
            })()
        "));
});

// --- Optgroup rendering ---

test('optgroup labels are rendered', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const groups = document.querySelectorAll('#sel-grouped-listbox .ws-select-optgroup-label');
                return groups.length === 2
                    && groups[0].textContent === 'Fruits'
                    && groups[1].textContent === 'Vegetables';
            })()
        "));
});

test('ungrouped options appear before groups', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const container = document.querySelector('#sel-grouped-listbox .ws-d-contents');
                const children = container.children;
                // First child: ungrouped option (Water)
                return children[0].dataset.wsValue === 'water'
                    // Then: group label (Fruits), Apple, Banana, group label (Vegetables), Carrot
                    && children[1].classList.contains('ws-select-optgroup-label');
            })()
        "));
});

test('optgroup label has role presentation and aria-hidden', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const group = document.querySelector('#sel-grouped-listbox .ws-select-optgroup-label');
                return group.getAttribute('role') === 'presentation'
                    && group.getAttribute('aria-hidden') === 'true';
            })()
        "));
});

test('grouped option has data-ws-optgroup attribute', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const apple = document.querySelector('#sel-grouped-listbox [data-ws-value=\"apple\"]');
                const water = document.querySelector('#sel-grouped-listbox [data-ws-value=\"water\"]');
                return apple.hasAttribute('data-ws-optgroup')
                    && !water.hasAttribute('data-ws-optgroup');
            })()
        "));
});

// --- Multiple badge sort order ---

test('multiple badges are sorted alphabetically', function () {
    $page = waitForSelectPage($this);
    // Select in reverse alphabetical order: Red, Green, Blue
    openSelect($page, 'sel-multi-sort')
        ->click('#sel-multi-sort-listbox [data-ws-value="red"]')
        ->click('#sel-multi-sort-listbox [data-ws-value="green"]')
        ->click('#sel-multi-sort-listbox [data-ws-value="blue"]')
        ->assertScript(js_after_tick("
            (() => {
                const badges = document.querySelectorAll('#sel-multi-sort .ws-selection-badge-label span');
                return badges.length === 3
                    && badges[0].textContent === 'Blue'
                    && badges[1].textContent === 'Green'
                    && badges[2].textContent === 'Red';
            })()
        "));
});

// --- Enter on closed select ---

test('enter on closed select opens dropdown when not in form', function () {
    $page = waitForSelectPage($this);
    $page->keys('#sel-keyboard', 'Enter')
        ->assertScript(js_wait_for('#sel-keyboard-listbox .ws-select-option'));
});

// --- Option class attribute ---

test('option class applied to dropdown option', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-classed')
        ->assertScript(js_after_tick("
            (() => {
                const info = document.querySelector('#sel-classed-listbox [data-ws-value=\"info\"]');
                const plain = document.querySelector('#sel-classed-listbox [data-ws-value=\"plain\"]');
                return info.classList.contains('text-info')
                    && !plain.classList.contains('text-info');
            })()
        "));
});

test('option class applied to selection display', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-classed')
        ->click('#sel-classed-listbox [data-ws-value="info"]')
        ->assertScript(js_wait_hidden('#sel-classed-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-classed .ws-selection-simple').classList.contains('text-info')
        "));
});

// --- XSS escaping ---

test('label with HTML is escaped in dropdown', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-xss')
        ->assertScript(js_after_tick("
            (() => {
                const option = document.querySelector('#sel-xss-listbox [data-ws-value=\"xss\"] span');
                // textContent should contain the raw script tag, not execute it
                return option.textContent.includes('<script>')
                    && document.querySelectorAll('#sel-xss-listbox script').length === 0;
            })()
        "));
});

test('label with HTML is escaped in selection', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-xss')
        ->click('#sel-xss-listbox [data-ws-value="xss"]')
        ->assertScript(js_wait_hidden('#sel-xss-listbox'))
        ->assertScript(js_after_tick("
            (() => {
                const sel = document.querySelector('#sel-xss .ws-selection-simple span');
                return sel.textContent.includes('<script>')
                    && document.querySelectorAll('#sel-xss script').length === 0;
            })()
        "));
});

// --- JS API: $wirestrap.select.refresh ---

test('refresh reloads wire-options and preserves selection', function () {
    $page = waitForSelectPage($this);

    // Select an option first
    openSelect($page, 'sel-single')
        ->click('#sel-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single .ws-selection-simple span').textContent === 'Red'
        "));

    // Call refresh magic — this resets cache and reloads options
    $page->assertScript(js_wirestrap("select.refresh('sel-single')"));

    // Re-open: options should be available (reloaded from wire-options)
    openSelect($page, 'sel-single')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#sel-single-listbox .ws-select-option').length === 3
        "));

    // Selection should still be displayed
    $page->keys('#sel-single', 'Escape')
        ->assertScript(js_wait_hidden('#sel-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#sel-single .ws-selection-simple span').textContent === 'Red'
        "));
});

// --- Focus management ---

test('escape returns focus to selection', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'Escape')
        ->assertScript(js_wait_hidden('#sel-keyboard-listbox'))
        ->assertScript(js_wait_focus('#sel-keyboard'));
});

test('single selection returns focus to selection after pick', function () {
    $page = waitForSelectPage($this);
    openSelect($page, 'sel-keyboard')
        ->keys('#sel-keyboard', 'ArrowDown')
        ->keys('#sel-keyboard', 'Enter')
        ->assertScript(js_wait_hidden('#sel-keyboard-listbox'))
        ->assertScript(js_wait_focus('#sel-keyboard'));
});
