<?php

/**
 * Wait for a row count (tbody tr:not([data-ws-empty])) to match.
 */
function js_wait_rows(string $selector, int $count, int $timeout = 5000): string
{
    return "new Promise((resolve, reject) => {
        const deadline = Date.now() + {$timeout};
        const check = () => {
            const rows = document.querySelectorAll('{$selector} tbody tr:not([data-ws-empty])');
            if (rows.length === {$count}) {
                resolve(true);
            } else if (Date.now() > deadline) {
                reject(new Error('Timed out waiting for row count {$count}'));
            } else {
                requestAnimationFrame(check);
            }
        };
        check();
    })";
}

// --- Bulk selection ---

test('clicking per-row checkbox checks it', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]:checked').length === 1"
        ));
});

test('select-all checks all non-disabled rows', function () {
    // 3 rows: Alice, Bob (selectable), Charlie (disabled) → 2 checked
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]:checked').length === 2"
        ));
});

test('select-all skips disabled checkboxes', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk][disabled]').checked === false"
        ));
});

test('indeterminate state when partially selected', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').indeterminate === true"
        ));
});

test('deselect-all unchecks all rows', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        // Select all
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]:checked').length === 2"
        ))
        // Deselect all
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]:checked').length === 0"
        ));
});

test('select-all header checkbox is checked when all rows selected', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('[data-ws-model=\"selectedIds\"] thead input[type=checkbox]').checked === true"
        ));
});

// --- Bulk actions bar ---

test('bulk actions bar hidden when no selection', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-bulk-container]'))
        ->assertScript("document.querySelector('[data-ws-bulk-container]').style.display === 'none'");
});

test('bulk actions bar appears when rows are selected', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('[data-ws-bulk-container]').style.display !== 'none'"
        ));
});

test('bulk actions bar hides when all deselected', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        // Select one
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('[data-ws-bulk-container]').style.display !== 'none'"
        ))
        // Deselect
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_wait_hidden('[data-ws-bulk-container]'));
});

test('bulk action delete removes selected rows', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('[data-ws-model="selectedIds"]'))
        // Select first row
        ->assertScript("(() => {
            document.querySelector('[data-ws-model=\"selectedIds\"] tbody [data-ws-bulk]').click();
            return true;
        })()")
        ->assertScript(js_after_tick("true"))
        // Click delete — triggers wire:click which sends deferred $set + action
        ->assertScript("(() => {
            document.querySelector('[data-ws-bulk-container] button').click();
            return true;
        })()")
        ->assertScript(js_wait_rows('[data-ws-model="selectedIds"]', 2));
});

// --- Tooltip ---

test('tooltip element appended to body on init', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('body > .ws-tooltip'));
});

test('always-tooltip shows on hover', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('body > .ws-tooltip'))
        ->assertScript("(() => {
            const span = document.querySelector('#table-custom-tip thead th span[data-ws-tip-always]');
            span.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
            return true;
        })()")
        ->assertScript(js_wait_for('body > .ws-tooltip.show'));
});

test('mouseout hides tooltip', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('body > .ws-tooltip'))
        // Show tooltip
        ->assertScript("(() => {
            const span = document.querySelector('#table-custom-tip thead th span[data-ws-tip-always]');
            span.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
            return true;
        })()")
        ->assertScript(js_wait_for('body > .ws-tooltip.show'))
        // Hide tooltip
        ->assertScript("(() => {
            const span = document.querySelector('#table-custom-tip thead th span[data-ws-tip-always]');
            span.dispatchEvent(new MouseEvent('mouseout', { bubbles: true, relatedTarget: document.body }));
            return true;
        })()")
        ->assertScript(js_wait_hidden('.ws-tooltip.show'));
});

test('tooltip displays correct text from data-ws-label', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertScript(js_wait_for('body > .ws-tooltip'))
        ->assertScript("(() => {
            const span = document.querySelector('#table-custom-tip thead th span[data-ws-tip-always]');
            span.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
            return true;
        })()")
        ->assertScript(js_wait_for('body > .ws-tooltip.show'))
        ->assertScript("document.querySelector('.ws-tooltip .ws-tooltip-content').textContent === 'This is a custom tooltip'");
});

// --- Livewire operations (with animate) ---

test('adding a row increases row count', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertPresent('#btn-add-row')
        ->click('#btn-add-row')
        ->assertScript(js_wait_rows('[data-ws-model="selectedIds"]', 4));
});

test('removing a row decreases row count', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertPresent('#btn-remove-row')
        ->click('#btn-remove-row')
        ->assertScript(js_wait_rows('[data-ws-model="selectedIds"]', 2, 10000));
});

test('reordering rows changes DOM order', function () {
    $this->visit('/_ws/test/ui/table')
        ->assertPresent('#btn-reorder')
        ->assertScript("(() => {
            const rows = document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody tr:not([data-ws-empty])');
            window._firstRowKey = rows[0]?.getAttribute('wire:key');
            return window._firstRowKey !== null;
        })()")
        ->click('#btn-reorder')
        ->assertScript("new Promise((resolve, reject) => {
            const deadline = Date.now() + 5000;
            const check = () => {
                const rows = document.querySelectorAll('[data-ws-model=\"selectedIds\"] tbody tr:not([data-ws-empty])');
                if (rows[0]?.getAttribute('wire:key') !== window._firstRowKey) {
                    resolve(true);
                } else if (Date.now() > deadline) {
                    reject(new Error('Timed out waiting for reorder'));
                } else {
                    requestAnimationFrame(check);
                }
            };
            check();
        })");
});
