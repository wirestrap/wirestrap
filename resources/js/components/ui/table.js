/**
 * Alpine component for the WireStrap data table.
 *
 * Provides two independent features that can be used together or separately:
 *
 * 1. Bulk selection
 *    Hidden checkboxes inside table rows are bound via wsBulkCheck (per-row) and
 *    wsBulkSelectAll (header). Selected values are synced to a Livewire property
 *    named by data-ws-model. A bulk action bar (data-ws-bulk-container) animates
 *    in/out with a height + opacity transition when the selection goes from 0 to >0
 *    or back.
 *
 * 2. Column label tooltips
 *    Truncated column header cells (data-ws-label) display a floating tooltip on
 *    hover. The tooltip element is created once and appended to document.body.
 *    Tooltip classes (wrapper, inner, arrow) are read from data attributes on the
 *    root element so they follow the configured CSS framework mapping.
 *    Cells with data-ws-tip-always show the tooltip even when not truncated.
 *
 * Data attributes read from the root element:
 *   data-ws-model      - dot-path to the Livewire property for selected values.
 *   data-ws-tip        - CSS classes for the tooltip container element.
 *   data-ws-tip-inner  - CSS classes for the tooltip inner text element.
 *   data-ws-tip-arrow  - CSS classes for the tooltip arrow element.
 *
 * Row checkbox elements must carry data-ws-bulk; the header checkbox must use
 * x-bind="wsBulkSelectAll".
 */
import { computePosition, autoUpdate, offset, flip, shift, arrow as arrowMiddleware } from '@floating-ui/dom';
import { afterTransition } from '../../utils/transition';
import { animateHeight } from '../../utils/animateHeight';

Alpine.data('wsTable', () => ({
    /**
     * Binding
     */
    wsBulkCheck: {
        ['x-on:change'](e) {
            const current = [...this._getSelected()];
            const value = e.target.value;

            if (e.target.checked) {
                if (!current.includes(value)) {
                    current.push(value);
                }
            } else {
                const idx = current.indexOf(value);
                if (idx > -1) {
                    current.splice(idx, 1);
                }
            }

            this.$wire.$set(this._modelName, current, false);
        },
        ['x-bind:checked']() {
            return this.selectedItems.includes(this.$el.value);
        },
    },

    wsBulkSelectAll: {
        ['x-on:change'](e) {
            const checked = e.target.checked;
            const allValues = this._getCheckboxes().map((cb) => cb.value);
            this.$wire.$set(this._modelName, checked ? allValues : [], false);
        },
        ['x-effect']() {
            const checkboxes = this._getCheckboxes();
            const someChecked = checkboxes.some((cb) => this.selectedItems.includes(cb.value));
            const allChecked = checkboxes.length > 0 && checkboxes.every((cb) => this.selectedItems.includes(cb.value));
            this.$el.indeterminate = someChecked && !allChecked;
        },
        ['x-bind:checked']() {
            const checkboxes = this._getCheckboxes();
            return checkboxes.length > 0 && checkboxes.every((cb) => this.selectedItems.includes(cb.value));
        },
    },

    /**
     * State
     */
    selectedItems: [],
    _checkboxEls: [],
    _currentEl: null,
    _bulkEl: null,
    _tableEl: null,
    _flipState: null,
    _removingEls: null,
    _removingQueue: null,
    _removingRafId: null,

    /**
     * Config
     */
    _modelName: null,
    _animate: true,
    _animateDuration: 300,

    /**
     * Tooltip elements
     */
    _tooltipEl: null,
    _tooltipArrowEl: null,
    _tooltipInnerEl: null,

    /**
     * Cleanup
     */
    _morphedUnsub: null,
    _modelWatchUnsub: null,
    _morphSnapUnsub: null,
    _removingUnsub: null,
    _tooltipCleanup: null,
    _cancelTooltipHide: null,
    _cancelBulkAnimation: null,
    _onMouseOver: null,
    _onMouseOut: null,

    /**
     * Lifecycle
     */
    init() {
        this._tableEl = this.$el;
        this._modelName = this.$el.dataset.wsModel ?? null;
        this._animate = this.$el.dataset.wsAnimate !== 'false';
        this.$el.querySelector('[data-ws-empty]')?.setAttribute('data-ws-init', '');

        if (this._animate && this.$wire) {
            const tableEl = this._tableEl.querySelector('table');
            const raw = tableEl ? getComputedStyle(tableEl).getPropertyValue('--ws-table-animate-duration').trim() : '';
            this._animateDuration = parseInt(raw) || 300;
            this._flipState = new Map();
            this._removingEls = new Set();
            this._removingQueue = [];

            // morph.removing fires once per removed row, synchronously, before any paint.
            // Buffering all rows and processing them in a single rAF reduces
            // the setup cost to one reflow regardless of how many rows are removed.
            this._removingUnsub = this.$wire.$hook('morph.removing', ({ el, skip }) => {
                if (!el.matches('tbody tr[wire\\:key]')) {
                    return;
                }
                if (this._removingEls.has(el)) {
                    return;
                }

                skip();
                this._removingEls.add(el);
                this._removingQueue.push(el);

                if (this._removingRafId === null) {
                    this._removingRafId = requestAnimationFrame(() => {
                        this._removingRafId = null;
                        const queue = this._removingQueue.splice(0);
                        this._processRemoveAnimations(queue);
                    });
                }
            });

            this._morphSnapUnsub = this.$wire.$hook('morph', () => {
                this._tableEl.querySelectorAll('tbody tr[wire\\:key]').forEach((el) => {
                    this._flipState.set(el.getAttribute('wire:key'), el.getBoundingClientRect());
                });
            });
        }

        // Tooltip element - classes sourced from config via data attributes
        this._tooltipArrowEl = document.createElement('div');
        this._tooltipArrowEl.className = this.$el.dataset.wsTipArrow;
        this._tooltipArrowEl.dataset.wsArrow = '';

        this._tooltipInnerEl = document.createElement('div');
        this._tooltipInnerEl.className = this.$el.dataset.wsTipInner;

        this._tooltipEl = document.createElement('div');
        this._tooltipEl.className = this.$el.dataset.wsTip;
        this._tooltipEl.style.cssText = 'display: none; position: absolute; top: 0; left: 0;';
        this._tooltipEl.appendChild(this._tooltipArrowEl);
        this._tooltipEl.appendChild(this._tooltipInnerEl);
        document.body.appendChild(this._tooltipEl);

        this._onMouseOver = (e) => {
            const el = e.target.closest('[data-ws-label]');

            if (el === this._currentEl) {
                return;
            }

            this._currentEl = el;

            if (!el) {
                this._hideTooltip();
                return;
            }

            if (!el.hasAttribute('data-ws-tip-always')) {
                if (el.scrollWidth <= el.offsetWidth) {
                    this._hideTooltip();
                    return;
                }
            }

            this._showTooltip(el);
        };

        this._onMouseOut = (e) => {
            const el = e.target.closest('[data-ws-label]');

            // Suppress hide when moving within the same <th>: the label element may not
            // fill the entire cell and moving to padding space would flicker the tooltip.
            if (!el || el.closest('th')?.contains(e.relatedTarget)) {
                return;
            }

            this._hideTooltip();
        };

        this.$el.addEventListener('mouseover', this._onMouseOver);
        this.$el.addEventListener('mouseout', this._onMouseOut);

        this._bulkEl = this.$el.querySelector('[data-ws-bulk-container]');
        this._updateCheckboxes();

        this.$wire &&
            (this._morphedUnsub = this.$wire.$hook('morphed', () => {
                this._updateCheckboxes();

                if (!this._animate) {
                    return;
                }

                this._tableEl.querySelectorAll('tbody tr[wire\\:key]').forEach((el) => {
                    const key = el.getAttribute('wire:key');
                    const before = this._flipState.get(key);

                    if (!before) {
                        el.animate(
                            [
                                { opacity: 0, transform: 'translateY(-6px)' },
                                { opacity: 1, transform: 'none' },
                            ],
                            { duration: this._animateDuration, easing: 'ease-out' },
                        );
                        return;
                    }

                    const after = el.getBoundingClientRect();
                    const dy = before.top - after.top;
                    if (dy === 0) {
                        return;
                    }

                    el.animate([{ transform: `translateY(${dy}px)` }, { transform: 'none' }], {
                        duration: this._animateDuration,
                        easing: 'ease-out',
                    });
                });

                this._flipState.clear();
            }));

        if (this._modelName && this.$wire) {
            const raw = this.$wire.$get(this._modelName);
            this.selectedItems = Array.isArray(raw) ? raw : [];

            this._modelWatchUnsub = this.$wire.$watch(this._modelName, (val) => {
                this.selectedItems = Array.isArray(val) ? val : [];
            });
        }

        if (this._modelName && this._bulkEl) {
            // Show immediately without animation if items are already selected on mount
            if (this.selectedItems.length > 0) {
                this._bulkEl.style.display = '';
            }

            this.$watch(
                () => this.selectedItems.length,
                (length, oldLength) => {
                    if (length > 0 && oldLength === 0) {
                        this._showBulk();
                    } else if (length === 0) {
                        this._hideBulk();
                    }
                },
            );
        }
    },

    destroy() {
        this.$el.removeEventListener('mouseover', this._onMouseOver);
        this.$el.removeEventListener('mouseout', this._onMouseOut);
        this._tooltipCleanup?.();
        this._cancelTooltipHide?.();
        this._tooltipEl?.remove();
        this._cancelBulkAnimation?.();
        this._morphedUnsub?.();
        this._modelWatchUnsub?.();
        this._morphSnapUnsub?.();
        this._removingUnsub?.();
        if (this._removingRafId !== null) {
            cancelAnimationFrame(this._removingRafId);
            this._removingRafId = null;
        }
        this._removingQueue?.forEach((el) => el.remove());
        this._removingEls?.forEach((el) => el.remove());
    },

    /**
     * Row remove animations
     */
    _processRemoveAnimations(rows) {
        const rowCells = rows.map((row) => Array.from(row.querySelectorAll(':scope > td, :scope > th')));

        // Read padding once: uniform across all cells via CSS variables
        const firstCell = rowCells.find((cells) => cells.length > 0)?.[0];
        let paddingTop = '0px',
            paddingBottom = '0px';
        if (firstCell) {
            const s = getComputedStyle(firstCell);
            paddingTop = s.paddingTop;
            paddingBottom = s.paddingBottom;
        }

        // Batch write: create all wrappers across all rows
        const rowWrappers = rows.map((row, i) => {
            row.classList.add('ws-removing');
            return rowCells[i].map((cell) => {
                const wrapper = document.createElement('div');
                wrapper.style.overflow = 'hidden';
                wrapper.style.paddingTop = paddingTop;
                wrapper.style.paddingBottom = paddingBottom;
                cell.style.paddingTop = '0';
                cell.style.paddingBottom = '0';
                while (cell.firstChild) {
                    wrapper.appendChild(cell.firstChild);
                }
                cell.appendChild(wrapper);
                return wrapper;
            });
        });

        // Single reflow for the entire batch
        this._tableEl.offsetHeight;

        // Batch height reads + start animations
        rows.forEach((row, i) => {
            const wrappers = rowWrappers[i].map((wrapper) => ({ wrapper, height: wrapper.offsetHeight }));
            this._startRowRemoveAnimation(row, wrappers);
        });
    },

    _startRowRemoveAnimation(el, wrappers) {
        const fadeDuration = this._animateDuration;
        const collapseDuration = this._animateDuration;
        // Collapse starts at 40% into the fade so both finish close together.
        const collapseDelay = Math.round(this._animateDuration * 0.4);

        el.animate([{ opacity: 1 }, { opacity: 0 }], { duration: fadeDuration, easing: 'ease-in', fill: 'forwards' });

        const collapseAnims = wrappers.map(({ wrapper, height }) =>
            wrapper.animate([{ height: `${height}px` }, { height: '0px' }], {
                duration: collapseDuration,
                delay: collapseDelay,
                easing: 'ease-in-out',
                fill: 'forwards',
            }),
        );

        Promise.all(collapseAnims.map((a) => a.finished)).then(() => {
            el.remove();
            this._removingEls.delete(el);
        });
    },

    /**
     * Bulk
     */
    _showBulk() {
        this._cancelBulkAnimation?.();

        this._bulkEl.style.display = '';
        this._bulkEl.style.height = '0';
        this._bulkEl.style.opacity = '0';
        this._bulkEl.style.overflow = 'hidden';

        this._cancelBulkAnimation = animateHeight(
            this._bulkEl,
            this._bulkEl.scrollHeight,
            'height var(--ws-table-bulk-transition-duration, 200ms) ease, opacity var(--ws-table-bulk-transition-duration, 200ms) ease',
            () => {
                this._bulkEl.style.transition = '';
                this._bulkEl.style.height = '';
                this._bulkEl.style.overflow = '';
                this._bulkEl.style.opacity = '';
                this._cancelBulkAnimation = null;
            },
        );
    },

    _hideBulk() {
        this._cancelBulkAnimation?.();

        this._bulkEl.style.height = `${this._bulkEl.scrollHeight}px`;
        this._bulkEl.style.overflow = 'hidden';

        this._cancelBulkAnimation = animateHeight(
            this._bulkEl,
            0,
            'height var(--ws-table-bulk-transition-duration, 200ms) ease, opacity var(--ws-table-bulk-transition-duration, 200ms) ease',
            () => {
                this._bulkEl.style.display = 'none';
                this._bulkEl.style.transition = '';
                this._bulkEl.style.height = '';
                this._bulkEl.style.overflow = '';
                this._bulkEl.style.opacity = '';
                this._cancelBulkAnimation = null;
            },
        );
    },

    /**
     * Tooltip
     */
    _showTooltip(th) {
        this._tooltipCleanup?.();

        this._cancelTooltipHide?.();
        this._cancelTooltipHide = null;

        this._tooltipInnerEl.textContent = th.dataset.wsLabel;
        this._tooltipEl.style.removeProperty('display');

        this._tooltipCleanup = autoUpdate(th, this._tooltipEl, () => {
            computePosition(th, this._tooltipEl, {
                placement: 'top',
                strategy: 'absolute',
                middleware: [offset(8), flip(), shift(), arrowMiddleware({ element: this._tooltipArrowEl })],
            }).then(({ x, y, placement, middlewareData }) => {
                Object.assign(this._tooltipEl.style, {
                    left: `${x}px`,
                    top: `${y}px`,
                });

                this._tooltipEl.dataset.floatingPlacement = placement;

                if (middlewareData.arrow) {
                    this._tooltipArrowEl.style.position = 'absolute';
                    this._tooltipArrowEl.style.left = middlewareData.arrow.x != null ? `${middlewareData.arrow.x}px` : '';
                    this._tooltipArrowEl.style.top = middlewareData.arrow.y != null ? `${middlewareData.arrow.y}px` : '';
                }

                this._tooltipEl.offsetHeight; // force reflow so opacity transition plays on show
                this._tooltipEl.classList.add('show');
            });
        });
    },

    _hideTooltip() {
        this._tooltipCleanup?.();
        this._tooltipCleanup = null;
        this._tooltipEl.classList.remove('show');
        this._currentEl = null;

        this._cancelTooltipHide = afterTransition(
            this._tooltipEl,
            () => {
                this._tooltipEl.style.display = 'none';
                this._cancelTooltipHide = null;
            },
            { fallback: 300 },
        );
    },

    /**
     * Utils
     */
    _updateCheckboxes() {
        this._checkboxEls = this._tableEl ? Array.from(this._tableEl.querySelectorAll('[data-ws-bulk]')) : [];
    },

    _getCheckboxes() {
        return this._checkboxEls;
    },

    _getSelected() {
        return this.selectedItems;
    },
}));
