<?php

// Helper: wait for Livewire lazy component to load
function waitForAutocompletePage(mixed $page): mixed
{
    return $page->visit('/_ws/test/form/autocomplete')
        ->assertScript(js_wait_for('#ac-single'));
}

// Helper: focus input and wait for suggestions to load
function focusAndWait(mixed $page, string $id): mixed
{
    return $page->click("#$id")
        ->assertScript(js_wait_for("#$id-listbox .ws-autocomplete-option"));
}

// Helper: JS expression to get the autocomplete root from input id
function acRoot(string $id): string
{
    return "document.querySelector('#$id').closest('.ws-autocomplete')";
}

// Helper: wait for tags to render inside the autocomplete containing the given input
function waitForTags(string $inputId, int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            const root = document.querySelector('#{$inputId}')?.closest('.ws-autocomplete');
            if (root && root.querySelector('.ws-autocomplete-tag')) {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for tags in #{$inputId}'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

// --- Open / Close ---

test('focus opens dropdown when min-chars is 0', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox').style.display !== 'none'
        "));
});

test('typing filters and shows dropdown', function () {
    $page = waitForAutocompletePage($this);
    $page->click('#ac-search')
        ->assertScript(js_wait_for('#ac-search-listbox .ws-autocomplete-option'))
        ->fill('#ac-search', 'gre')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#ac-search-listbox .ws-autocomplete-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'green';
            })()
        "));
});

test('escape closes dropdown', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'Escape')
        ->assertScript(js_wait_hidden('#ac-single-listbox'));
});

test('focusout closes dropdown', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->click('#ac-search')
        ->assertScript(js_wait_hidden('#ac-single-listbox'));
});

// --- Single selection ---

test('clicking suggestion fills input', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->click('#ac-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#ac-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single').value === 'red'
        "));
});

test('selecting suggestion syncs to wire:model', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->click('#ac-single-listbox [data-ws-value="green"]')
        ->assertScript(js_wait_hidden('#ac-single-listbox'))
        ->assertScript(js_after_tick("
            Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id')).get('single') === 'green'
        "));
});

test('enter selects highlighted suggestion', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'ArrowDown')
        ->keys('#ac-single', 'Enter')
        ->assertScript(js_wait_hidden('#ac-single-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single').value === 'green'
        "));
});

test('tab selects highlighted suggestion', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'ArrowDown')
        ->keys('#ac-single', 'Tab')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single').value === 'green'
        "));
});

// --- Multiple (tags) ---

test('enter adds tag in multiple mode', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'hello')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

test('comma adds tag in multiple mode', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'newtag')
        ->keys('#ac-tags', ',')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

test('clicking suggestion adds tag', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    $page->click('#ac-tags')
        ->assertScript(js_wait_for('#ac-tags-listbox .ws-autocomplete-option'))
        ->click('#ac-tags-listbox [data-ws-value="rust"]')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

test('backspace removes last tag', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    // Add a tag first, then remove with Backspace
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'hello')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("{$root}.querySelectorAll('.ws-autocomplete-tag').length === 1"))
        ->keys('#ac-tags', 'Backspace')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 0
        "));
});

test('tag remove button removes specific tag', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    // Add two tags
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'alpha')
        ->keys('#ac-tags', 'Enter')
        ->fill('#ac-tags', 'beta')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("{$root}.querySelectorAll('.ws-autocomplete-tag').length === 2"))
        ->assertScript("
            (() => {
                const btn = {$root}.querySelector('[data-ws-tag-remove=\"alpha\"]');
                btn.click();
                return true;
            })()
        ")
        ->assertScript(js_after_tick("
            (() => {
                const tags = {$root}.querySelectorAll('.ws-autocomplete-tag');
                return tags.length === 1
                    && tags[0].querySelector('.ws-autocomplete-tag-label').textContent.trim() === 'beta';
            })()
        "));
});

test('tags sync array to wire:model', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'hello')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("{$root}.querySelectorAll('.ws-autocomplete-tag').length === 1"))
        ->assertScript(js_after_tick("
            (() => {
                const val = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id')).get('tags');
                return Array.isArray(val) && val.length === 1 && val.includes('hello');
            })()
        "));
});

test('duplicate tag is not added', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    // Add a tag, then try adding the same one
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'hello')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("{$root}.querySelectorAll('.ws-autocomplete-tag').length === 1"))
        ->fill('#ac-tags', 'hello')
        ->keys('#ac-tags', 'Enter')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

test('focusout commits current query as tag', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-tags');
    $page->click('#ac-tags')
        ->fill('#ac-tags', 'newtag')
        ->click('#ac-single')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

// --- Search / Filtering ---

test('typing filters suggestions by label', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-search')
        ->fill('#ac-search', 'gre')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#ac-search-listbox .ws-autocomplete-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'green';
            })()
        "));
});

test('typing filters suggestions by value', function () {
    $page = waitForAutocompletePage($this);
    // "ree" does NOT startsWith-match any label, but includes-matches value "green"
    focusAndWait($page, 'ac-search')
        ->fill('#ac-search', 'ree')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#ac-search-listbox .ws-autocomplete-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'green';
            })()
        "));
});

test('clearing input shows all suggestions', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-search')
        ->fill('#ac-search', 'gre')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#ac-search-listbox .ws-autocomplete-option').length === 1
        "))
        ->fill('#ac-search', '')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#ac-search-listbox .ws-autocomplete-option').length === 3
        "));
});

test('search normalizes accents', function () {
    $page = waitForAutocompletePage($this);
    // "gun" (no diacritic) must match "Günter" (ü → u normalization)
    focusAndWait($page, 'ac-accented')
        ->fill('#ac-accented', 'gun')
        ->assertScript(js_after_tick("
            (() => {
                const opts = document.querySelectorAll('#ac-accented-listbox .ws-autocomplete-option');
                return opts.length === 1 && opts[0].dataset.wsValue === 'gunter';
            })()
        "));
});

// --- Min-chars ---

test('min-chars prevents dropdown before threshold', function () {
    $page = waitForAutocompletePage($this);
    // min-chars=2, type only 1 character
    $page->click('#ac-minchars')
        ->fill('#ac-minchars', 'r')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-minchars-listbox').style.display === 'none'
            || document.querySelectorAll('#ac-minchars-listbox .ws-autocomplete-option').length === 0
        "));
});

test('min-chars shows dropdown after threshold', function () {
    $page = waitForAutocompletePage($this);
    // "re" matches Red (label startsWith) and Green (value includes) = 2 options
    $page->click('#ac-minchars')
        ->fill('#ac-minchars', 're')
        ->assertScript(js_wait_for('#ac-minchars-listbox .ws-autocomplete-option'))
        ->assertScript(js_after_tick("
            document.querySelectorAll('#ac-minchars-listbox .ws-autocomplete-option').length === 2
        "));
});

// --- Ghost text ---

test('ghost suffix appears for matching prefix', function () {
    $page = waitForAutocompletePage($this);
    // "app" → first match is "apple", ghost suffix should be "le"
    focusAndWait($page, 'ac-ghost')
        ->fill('#ac-ghost', 'app')
        ->assertScript(js_after_tick("
            (() => {
                const suffix = document.querySelector('#ac-ghost').closest('.ws-autocomplete').querySelector('.ws-autocomplete-ghost-suffix');
                return suffix && suffix.textContent === 'le';
            })()
        "));
});

test('ghost suffix disappears for non-matching query', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-ghost')
        ->fill('#ac-ghost', 'xyz')
        ->assertScript(js_after_tick("
            (() => {
                const suffix = document.querySelector('#ac-ghost').closest('.ws-autocomplete').querySelector('.ws-autocomplete-ghost-suffix');
                return suffix.textContent === '';
            })()
        "));
});

// --- Keyboard navigation ---

test('arrow down highlights next suggestion', function () {
    $page = waitForAutocompletePage($this);
    // After focus + load, first option highlighted (index 0 = Red)
    // ArrowDown moves to Green (index 1)
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox .ws-autocomplete-option.highlighted').dataset.wsValue === 'green'
        "));
});

test('arrow down wraps from last to first', function () {
    $page = waitForAutocompletePage($this);
    // 3 options: Red(0), Green(1), Blue(2)
    // Open highlights Red(0). 3 ArrowDowns: Green, Blue, Red (wrap)
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'ArrowDown')
        ->keys('#ac-single', 'ArrowDown')
        ->keys('#ac-single', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox .ws-autocomplete-option.highlighted').dataset.wsValue === 'red'
        "));
});

test('arrow up wraps from first to last', function () {
    $page = waitForAutocompletePage($this);
    // Open highlights Red(0). ArrowUp wraps to Blue (last)
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'ArrowUp')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox .ws-autocomplete-option.highlighted').dataset.wsValue === 'blue'
        "));
});

test('mouseover updates highlight and keyboard continues from there', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->hover('#ac-single-listbox [data-ws-value="blue"]')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox .ws-autocomplete-option.highlighted').dataset.wsValue === 'blue'
        "))
        ->keys('#ac-single', 'ArrowDown')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-single-listbox .ws-autocomplete-option.highlighted').dataset.wsValue === 'red'
        "));
});

// --- Disabled ---

test('disabled input cannot receive focus', function () {
    $page = waitForAutocompletePage($this);
    $page->assertScript(js_after_tick("
        document.querySelector('#ac-disabled').disabled === true
    "));
});

// --- Optgroup rendering ---

test('optgroup labels are rendered', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const groups = document.querySelectorAll('#ac-grouped-listbox .ws-autocomplete-optgroup-label');
                return groups.length === 2
                    && groups[0].textContent === 'Fruits'
                    && groups[1].textContent === 'Vegetables';
            })()
        "));
});

test('ungrouped options appear before groups', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const container = document.querySelector('#ac-grouped-listbox > div');
                const children = container.children;
                return children[0].dataset.wsValue === 'water'
                    && children[1].classList.contains('ws-autocomplete-optgroup-label');
            })()
        "));
});

test('optgroup label has role presentation and aria-hidden', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-grouped')
        ->assertScript(js_after_tick("
            (() => {
                const group = document.querySelector('#ac-grouped-listbox .ws-autocomplete-optgroup-label');
                return group.getAttribute('role') === 'presentation'
                    && group.getAttribute('aria-hidden') === 'true';
            })()
        "));
});

// --- Rich content ---

test('rich suggestion renders html_prefix and html_suffix', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-rich')
        ->assertScript(js_after_tick("
            (() => {
                const option = document.querySelector('#ac-rich-listbox [data-ws-value=\"alice\"]');
                return option.querySelector('i.bi-person') !== null
                    && option.querySelector('.ws-test-badge') !== null
                    && option.querySelector('span').textContent === 'Alice';
            })()
        "));
});

test('rich suggestion selection fills input with value', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-rich')
        ->click('#ac-rich-listbox [data-ws-value="alice"]')
        ->assertScript(js_wait_hidden('#ac-rich-listbox'))
        ->assertScript(js_after_tick("
            document.querySelector('#ac-rich').value === 'alice'
        "));
});

// --- Option class ---

test('option class applied to suggestion', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-classed')
        ->assertScript(js_after_tick("
            (() => {
                const info = document.querySelector('#ac-classed-listbox [data-ws-value=\"info\"]');
                const plain = document.querySelector('#ac-classed-listbox [data-ws-value=\"plain\"]');
                return info.classList.contains('text-info')
                    && !plain.classList.contains('text-info');
            })()
        "));
});

// --- XSS escaping ---

test('label with HTML is escaped in suggestion', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-xss')
        ->assertScript(js_after_tick("
            (() => {
                const option = document.querySelector('#ac-xss-listbox [data-ws-value=\"xss\"] span');
                return option.textContent.includes('<script>')
                    && document.querySelectorAll('#ac-xss-listbox script').length === 0;
            })()
        "));
});

// --- Preloaded tags ---

test('preloaded tags render on mount', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-preloaded');
    $page->assertScript(waitForTags('ac-preloaded'))
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 2
        "));
});

test('preloaded tags load suggestions eagerly for labels', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-preloaded');
    $page->assertScript(waitForTags('ac-preloaded'))
        ->assertScript(js_after_tick("
            (() => {
                const labels = {$root}.querySelectorAll('.ws-autocomplete-tag-label');
                return labels.length === 2
                    && labels[0].textContent.trim() === 'php'
                    && labels[1].textContent.trim() === 'js';
            })()
        "));
});

// --- JS API ---

test('refresh reloads suggestions', function () {
    $page = waitForAutocompletePage($this);

    // Select a value first
    focusAndWait($page, 'ac-single')
        ->click('#ac-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#ac-single-listbox'));

    // Call refresh magic
    $page->assertScript(js_wirestrap("autocomplete.refresh('ac-single')"));

    // Clear the input (selecting "red" filled value "red" which triggers exact-match exclusion),
    // then re-focus: all suggestions should appear (reloaded)
    $page->fill('#ac-single', '')
        ->click('#ac-single')
        ->assertScript(js_wait_for('#ac-single-listbox .ws-autocomplete-option'))
        ->assertScript(js_after_tick("
            document.querySelectorAll('#ac-single-listbox .ws-autocomplete-option').length === 3
        "));
});

// --- Focus management ---

test('escape returns focus to input', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->keys('#ac-single', 'Escape')
        ->assertScript(js_wait_hidden('#ac-single-listbox'))
        ->assertScript(js_wait_focus('#ac-single'));
});

test('selecting suggestion returns focus to input', function () {
    $page = waitForAutocompletePage($this);
    focusAndWait($page, 'ac-single')
        ->click('#ac-single-listbox [data-ws-value="red"]')
        ->assertScript(js_wait_hidden('#ac-single-listbox'))
        ->assertScript(js_wait_focus('#ac-single'));
});

// --- Wire-options-watch ---

test('wire-options-watch reloads suggestions when watched property changes', function () {
    $page = waitForAutocompletePage($this);
    // Initially category = 'fruits' → Apple, Banana
    focusAndWait($page, 'ac-watched')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-watched-listbox [data-ws-value=\"apple\"]') !== null
        "))
        // Close and blur: Escape closes dropdown, click elsewhere blurs input
        ->keys('#ac-watched', 'Escape')
        ->assertScript(js_wait_hidden('#ac-watched-listbox'))
        ->click('#ac-single');

    // Change category → triggers watch → lazyReset + reset model to null
    $page->assertScript("
        (() => {
            const w = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id'));
            w.set('category', 'vegetables');
            return true;
        })()
    ");

    // Re-focus: onFocus fires, stale cache triggers reload with new category
    $page->click('#ac-watched')
        ->assertScript(js_wait_for('#ac-watched-listbox .ws-autocomplete-option'))
        ->assertScript(js_after_tick("
            document.querySelector('#ac-watched-listbox [data-ws-value=\"carrot\"]') !== null
                && document.querySelector('#ac-watched-listbox [data-ws-value=\"apple\"]') === null
        "));
});

test('wire-options-watch resets model value on property change', function () {
    $page = waitForAutocompletePage($this);
    // Select a value
    focusAndWait($page, 'ac-watched')
        ->click('#ac-watched-listbox [data-ws-value="apple"]')
        ->assertScript(js_wait_hidden('#ac-watched-listbox'))
        ->assertScript(js_after_tick("document.querySelector('#ac-watched').value === 'apple'"));

    // Change category → watch resets model to null, clears query
    $page->assertScript("
        (() => {
            const w = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id'));
            w.set('category', 'vegetables');
            return true;
        })()
    ")
        ->assertScript(js_after_tick("document.querySelector('#ac-watched').value === ''"));
});

// --- No-dropdown mode ---

test('no-dropdown mode does not show suggestions on focus', function () {
    $page = waitForAutocompletePage($this);
    $page->click('#ac-nodropdown')
        ->assertScript(js_after_tick("
            document.querySelector('#ac-nodropdown-listbox').style.display === 'none'
        "));
});

test('no-dropdown mode still allows tag creation', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-nodropdown');
    $page->click('#ac-nodropdown')
        ->fill('#ac-nodropdown', 'hello')
        ->keys('#ac-nodropdown', 'Enter')
        ->assertScript(js_after_tick("
            {$root}.querySelectorAll('.ws-autocomplete-tag').length === 1
        "));
});

// --- Min-chars = 1 ---

test('min-chars 1 does not open dropdown on focus alone', function () {
    $page = waitForAutocompletePage($this);
    $page->click('#ac-minchars1')
        ->assertScript(js_after_tick("
            document.querySelectorAll('#ac-minchars1-listbox .ws-autocomplete-option').length === 0
        "));
});

test('min-chars 1 opens dropdown after one character', function () {
    $page = waitForAutocompletePage($this);
    $page->click('#ac-minchars1')
        ->fill('#ac-minchars1', 'r')
        ->assertScript(js_wait_for('#ac-minchars1-listbox .ws-autocomplete-option'))
        ->assertScript(js_after_tick("
            document.querySelector('#ac-minchars1-listbox [data-ws-value=\"red\"]') !== null
        "));
});

// --- Validation (per-tag invalid indices) ---

test('validation highlights invalid tags', function () {
    $page = waitForAutocompletePage($this);
    $root = acRoot('ac-validated');

    // Add two tags: one invalid email, one valid
    $page->click('#ac-validated')
        ->fill('#ac-validated', 'notanemail')
        ->keys('#ac-validated', 'Enter')
        ->fill('#ac-validated', 'valid@test.com')
        ->keys('#ac-validated', 'Enter')
        ->assertScript(js_after_tick("{$root}.querySelectorAll('.ws-autocomplete-tag').length === 2"));

    // Trigger server-side validation
    $page->click('#btn-validate');

    // After Livewire morph, first tag (index 0) should be invalid, second should not
    $page->assertScript("new Promise((resolve, reject) => {
            const deadline = Date.now() + 10000;
            const check = () => {
                const root = document.querySelector('#ac-validated')?.closest('.ws-autocomplete');
                if (root && root.querySelector('.ws-autocomplete-tag-invalid')) {
                    resolve(true);
                } else if (Date.now() > deadline) {
                    reject(new Error('Timed out waiting for invalid tag highlight'));
                } else {
                    requestAnimationFrame(check);
                }
            };
            check();
        })")
        ->assertScript(js_after_tick("
            (() => {
                const tags = {$root}.querySelectorAll('.ws-autocomplete-tag');
                return tags.length === 2
                    && tags[0].classList.contains('ws-autocomplete-tag-invalid')
                    && !tags[1].classList.contains('ws-autocomplete-tag-invalid');
            })()
        "));
});
