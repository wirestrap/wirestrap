/**
 * Alpine component for the WireStrap custom select.
 *
 * Options are loaded lazily on first open via a Livewire method (data-ws-wire-options)
 * and unloaded from the DOM when the dropdown is closed. Supports single and
 * multiple selection modes, optional search filtering, and syncs to Livewire
 * via $wire.$set().
 *
 * The list method must return an array of {value, label} objects:
 *   [['value' => 1, 'label' => 'Option A'], ...]
 *
 * Data attributes read from the root element:
 *   data-ws-wiremodel      - dot-path to the Livewire property.
 *   data-ws-multiple       - "true" for multi-select.
 *   data-ws-placeholder    - placeholder text.
 *   data-ws-empty-value    - value treated as "no selection" (toggles ws-selected class).
 *   data-ws-live           - "true" to trigger a server round-trip on each change.
 *   data-ws-wire-options         - Livewire method name returning the options array.
 *   data-ws-wire-options-params  - JSON-encoded argument(s) passed to the method: a scalar or
 *                                  a list<mixed> (a scalar prop is automatically wrapped in an array
 *                                  by the Blade component). Each item is passed as-is except objects
 *                                  with a "ws-wire" key, resolved via $wire.$get(param['ws-wire']) at call time.
 *   data-ws-wire-options-watch   - dot-path to a Livewire property; reload options on change.
 */
import { floatingElement, defaultDropdownConfig } from '../../mixins/floating-element.js';
import { listNavigation } from '../../mixins/list-navigation.js';
import { lazyLoader } from '../../mixins/lazy-loader.js';

Alpine.data('wsSelect', () => ({
    /**
     * Mixins
     */
    ...floatingElement(),
    ...listNavigation(),
    ...lazyLoader(),

    /**
     * Binding
     */
    select: {
        ['x-ref']: 'select',
        ['x-bind:class']() {
            const classes = { 'ws-select-open': this.floatingShown };
            if (this.selectEmptyValue === null) {
                return classes;
            }

            const isEmptyValue =
                !this.selectMultiple && this.checkedValues.length === 1 && this.checkedValues[0] === this.selectEmptyValue;
            return { ...classes, 'ws-selected': !isEmptyValue && this.checkedValues.length > 0 };
        },
        ['x-on:keydown.enter'](event) {
            this.onEnter(event);
        },
    },

    optionList: {
        ['x-ref']: 'optionList',
    },

    selection: {
        ['x-ref']: 'selection',
        [':tabindex']() {
            return this.$refs.select.hasAttribute('disabled') ? '-1' : '0';
        },
        ['x-on:pointerdown'](event) {
            if (this.$el.hasAttribute('disabled') || this.floatingShown) {
                return;
            }

            if (event.target.closest('[data-ws-remove]')) {
                return;
            }

            this.wireOptionsMethod && this.lazyLoad(() => this.loadOptions());
        },
        ['x-on:mousedown'](event) {
            if (event.target.closest('[data-ws-remove]')) {
                this._removing = true;
            }
        },
        ['x-on:click'](event) {
            if (event.target.closest('[data-ws-remove]')) {
                this._removing = false;
                this.deselectOption(event.target.closest('[data-ws-remove]').dataset.wsRemove);
                return;
            }

            this._removing = false;
            this.floatingShown ? this.selectHide() : this.selectShow();
        },
        [':aria-expanded']() {
            return this.floatingShown;
        },
        [':aria-haspopup']() {
            return 'listbox';
        },
        ['x-on:keydown'](event) {
            if (!this.floatingShown) {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    this.selectShow();
                }
                return;
            }

            // When open with a searchInput, keydown is handled there instead
            if (this.$refs.searchInput) {
                return;
            }

            if (event.key === 'Escape') {
                this.selectHide(true);
                event.preventDefault();
                event.stopPropagation();
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                event.stopPropagation();
                this.navigate(1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                event.stopPropagation();
                this.navigate(-1);
            }
        },
        ['x-on:focusout'](event) {
            // When open with a searchInput, focusout is handled there instead
            if (!this.floatingShown || this.$refs.searchInput) {
                return;
            }

            if (this._removing) {
                requestAnimationFrame(() => this.$refs.selection?.focus());
                return;
            }

            !this.$el.contains(event.relatedTarget) && this.selectHide();
        },
    },

    searchInput: {
        ['x-ref']: 'searchInput',
        ['x-model']: 'searchQuery',
        ['x-on:keydown'](event) {
            if (!this.floatingShown) {
                return;
            }

            if (event.key === 'Escape') {
                this.selectHide(true);
                event.stopPropagation();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                event.stopPropagation();
                this.navigateSelect();
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                event.stopPropagation();
                this.navigate(1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                event.stopPropagation();
                this.navigate(-1);
            }
        },
        ['x-on:focusout'](event) {
            if (!this.floatingShown || this._closing) {
                return;
            }

            if (this.$refs.selection.contains(event.relatedTarget)) {
                this.$refs.searchInput && requestAnimationFrame(() => this.$refs.searchInput?.focus());
                return;
            }

            !this.$el.contains(event.relatedTarget) && this.selectHide();
        },
    },

    loader: {
        class: 'ws-select-loader',
        'aria-hidden': 'true',
        ['x-show']() {
            return this.wireOptionsMethod && !this._methodLoaded;
        },
    },

    /**
     * State
     */
    options: [],
    checkedValues: [],
    searchQuery: '',
    _listActive: false,
    _removing: false,
    _closing: false,
    _domOptionsJson: null,

    /**
     * Config
     */
    selectWiremodel: null,
    selectMultiple: false,
    selectEmptyValue: null,
    selectLive: false,
    selectDropdownOffset: 0,
    selectPosition: 'absolute',
    wireOptionsMethod: null,
    wireOptionsMethodWatch: null,
    _placeholderText: '',

    /**
     * Cleanup
     */
    _refreshHandler: null,
    _morphedUnsub: null,
    _watchUnsub: null,
    _wireModelUnsub: null,
    _onClickOutside: null,

    /**
     * Computed
     */
    get visibleOptions() {
        return this.optionElements;
    },

    get displayedOptions() {
        if (!this._listActive) {
            return [];
        }

        let source = this.options;

        if (this.searchQuery.trim()) {
            const query = this.searchQuery.toLowerCase();
            source = this.options.filter((opt) => String(opt.label).toLowerCase().includes(query));
        }

        const ungrouped = [];
        const groups = new Map();

        for (const option of source) {
            if (option.optgroup != null) {
                if (!groups.has(option.optgroup)) {
                    groups.set(option.optgroup, []);
                }
                groups.get(option.optgroup).push({ type: 'option', ...option });
            } else {
                ungrouped.push({ type: 'option', ...option });
            }
        }

        const items = [...ungrouped];

        for (const [label, options] of groups) {
            items.push({ type: 'group', label }, ...options);
        }

        return items;
    },

    /**
     * Lifecycle
     */
    init() {
        this.selectWiremodel = this.$el.getAttribute('data-ws-wiremodel');
        this.selectMultiple = this.$el.getAttribute('data-ws-multiple') === 'true';
        this.selectEmptyValue = this.$el.getAttribute('data-ws-empty-value');
        this.selectLive = this.$el.getAttribute('data-ws-live') === 'true';
        this.selectDropdownOffset = parseInt(this.$el.getAttribute('data-ws-dropdown-offset') || 0, 10);
        this.selectPosition = this.$el.getAttribute('data-ws-position') || 'absolute';
        this.wireOptionsMethod = this.$el.getAttribute('data-ws-wire-options');
        this.wireOptionsMethodWatch = this.$el.getAttribute('data-ws-wire-options-watch');

        const domOptionsJson = this.$el.getAttribute('data-ws-options');
        if (domOptionsJson) {
            this._loadDomOptions(domOptionsJson);
        }

        if (this.wireOptionsMethod) {
            this._refreshHandler = () => {
                this.lazyReset();
                this.loadOptions();
            };
            this.$el.addEventListener('ws:refresh', this._refreshHandler);
        } else {
            this._morphedUnsub = this.$wire.$hook('morphed', () => {
                const json = this.$el.getAttribute('data-ws-options');
                if (json !== this._domOptionsJson) {
                    this._loadDomOptions(json);
                    this.renderSelection();
                }
            });
        }

        this._onClickOutside = (event) => {
            document.contains(event.target) &&
                !this.$el.contains(event.target) &&
                !this._floatingEl?.contains(event.target) &&
                this.selectHide();
        };

        this.onFloatingShow = () => {
            this.$nextTick(() => {
                document.addEventListener('click', this._onClickOutside);
                this.$refs.searchInput?.focus();
            });
        };

        // Keep options in the DOM during the close transition
        this.onFloatingHidden = () => {
            this._listActive = false;
            document.removeEventListener('click', this._onClickOutside);
        };

        // Re-render options imperatively whenever the visible option set changes.
        this.$watch('displayedOptions', () => {
            this.renderOptions();
        });

        this.$nextTick(() => {
            this._placeholderText = this.$el.getAttribute('data-ws-placeholder') ?? 'Select an option';

            if (this.$refs.selection.offsetParent !== null) {
                this.floatingInit(this.$refs.selection, this.$refs.floatable, this.getFloatingConfig());
            }

            this.syncCheckedValues();

            // Load options immediately when a real value is already selected so
            // the selection label is visible before the user opens the dropdown.
            // A "real" value is anything that isn't null/empty/equal to emptyValue.
            if (this._hasRealSelection() && this.wireOptionsMethod) {
                this.loadOptions();
            }

            this.renderSelection();

            if (this.wireOptionsMethodWatch && this.$wire) {
                this._watchUnsub = this.$wire.$watch(this.wireOptionsMethodWatch, () => {
                    this.lazyReset();
                    if (this.floatingShown || this._hasRealSelection()) {
                        this.loadOptions();
                    }
                });
            }

            // Keep checkedValues and the selection display in sync when the
            // wire:model property changes externally (e.g. another component on
            // the same page, or a Livewire action that doesn't trigger a morph).
            if (this.selectWiremodel && this.$wire) {
                this._wireModelUnsub = this.$wire.$watch(this.selectWiremodel, () => {
                    this.syncCheckedValues();

                    if (this._hasRealSelection() && this.wireOptionsMethod && this.options.length === 0) {
                        this.loadOptions();
                    }

                    this.renderSelection();
                });
            }
        });
    },

    destroy() {
        this._watchUnsub?.();
        this._wireModelUnsub?.();
        this._refreshHandler && this.$el.removeEventListener('ws:refresh', this._refreshHandler);
        this._morphedUnsub?.();
        document.removeEventListener('click', this._onClickOutside);
        this.floatingDestroy();
    },

    /**
     * Floating select
     */
    getFloatingConfig() {
        return defaultDropdownConfig(this.selectDropdownOffset, this.selectPosition);
    },

    selectShow() {
        if (this.$el.hasAttribute('disabled')) {
            return;
        }

        // floatingInit is skipped in init() when the element is hidden (offsetParent === null,
        // e.g. inside a collapsed accordion or inactive tab). Initialize lazily on first open.
        this.floatingEnsureInit(this.$refs.selection, this.$refs.floatable, this.getFloatingConfig());

        this.lazyLoad(() => this.loadOptions());

        this._listActive = true;
        this.searchQuery = '';
        this.floatingShow();
    },

    selectHide(focusBack = false) {
        this.clearHighlight();
        this.highlightedIndex = -1;
        if (focusBack) {
            this._closing = true;
            this.$refs.selection.focus();
            this._closing = false;
        }
        this.floatingHide();
    },

    /**
     * Options
     */
    loadOptions() {
        if (!this.$wire || !this.wireOptionsMethod) {
            return;
        }

        this.options = [];
        const paramsJson = this.$root.getAttribute('data-ws-wire-options-params');
        const params = paramsJson ? JSON.parse(paramsJson) : [];
        const args = params.map((param) => {
            if (param !== null && typeof param === 'object' && 'ws-wire' in param) {
                return this.$wire.$get(param['ws-wire']);
            }
            return param;
        });
        this.$wire.$call(this.wireOptionsMethod, ...args).then((result) => {
            if (Array.isArray(result)) {
                this.options = result.map((item) => ({
                    value: String(item.value),
                    label: String(item.label),
                    disabled: item.disabled === true,
                    optgroup: item.optgroup != null ? String(item.optgroup) : null,
                    class: item.class != null ? String(item.class) : null,
                    htmlPrefix: item.html_prefix != null ? String(item.html_prefix) : null,
                    htmlSuffix: item.html_suffix != null ? String(item.html_suffix) : null,
                }));
            }

            this._methodLoaded = true; // marks lazyLoader as loaded

            // Skipping this when there is no selection avoids replacing the
            // placeholder DOM node between pointerdown and click, which would
            // suppress the click event and leave the dropdown closed
            if (this._hasRealSelection()) {
                this.renderSelection();
            }
        });
    },

    _loadDomOptions(json) {
        this._domOptionsJson = json;
        try {
            const parsed = JSON.parse(json);
            if (Array.isArray(parsed)) {
                this.options = parsed.map((item) => ({
                    value: String(item.value),
                    label: String(item.label),
                    disabled: item.disabled === true,
                    optgroup: item.optgroup != null ? String(item.optgroup) : null,
                    class: item.class != null ? String(item.class) : null,
                    htmlPrefix: item.html_prefix != null ? String(item.html_prefix) : null,
                    htmlSuffix: item.html_suffix != null ? String(item.html_suffix) : null,
                }));
            }
        } catch {}
        this._methodLoaded = true;
    },

    syncCheckedValues() {
        if (!this.$wire || !this.selectWiremodel) {
            this.checkedValues = [];
            return;
        }

        const value = this.$wire.$get(this.selectWiremodel);

        if (this.selectMultiple) {
            this.checkedValues = Array.isArray(value) ? value.map(String) : [];
        } else {
            this.checkedValues = value !== null && value !== undefined ? [String(value)] : [];
        }
    },

    selectOption(index) {
        const el = this.optionElements[index];
        if (!el) {
            return;
        }

        const option = this.options.find((o) => o.value === el.dataset.wsValue);
        if (!option || option.disabled) {
            return;
        }

        if (this.selectMultiple) {
            const alreadyChecked = this.checkedValues.includes(option.value);
            this.checkedValues = alreadyChecked
                ? this.checkedValues.filter((v) => v !== option.value)
                : [...this.checkedValues, option.value];
        } else {
            this.checkedValues = [option.value];
        }

        this.syncToWire();
        this.renderSelection();
        this.optionElements.forEach((el) => {
            el.setAttribute('aria-selected', String(this.checkedValues.includes(el.dataset.wsValue)));
        });

        !this.selectMultiple && this.selectHide(true);
    },

    deselectOption(value) {
        this.checkedValues = this.checkedValues.filter((v) => v !== String(value));
        this.syncToWire();
        this.renderSelection();
        this.optionElements.forEach((el) => {
            el.setAttribute('aria-selected', String(this.checkedValues.includes(el.dataset.wsValue)));
        });
    },

    syncToWire() {
        if (!this.$wire || !this.selectWiremodel) {
            return;
        }

        const value = this.selectMultiple ? this.checkedValues : (this.checkedValues[0] ?? null);

        this.$wire.$set(this.selectWiremodel, value, this.selectLive);
    },

    /**
     * Rendering
     */
    renderSelection() {
        const value = this.$wire.$get(this.selectWiremodel);
        this.$refs.selection.replaceChildren();

        const isEmptyValue =
            this.selectEmptyValue !== null &&
            value !== null &&
            value !== undefined &&
            !Array.isArray(value) &&
            String(value) === this.selectEmptyValue;

        const hasSelection =
            !isEmptyValue && value !== null && value !== undefined && (Array.isArray(value) ? value.length > 0 : value !== '');

        if (!hasSelection) {
            const ph = document.createElement('span');
            ph.className = 'ws-selection-placeholder';
            ph.textContent = this._placeholderText;
            this.$refs.selection.appendChild(ph);
            return;
        }

        const fragment = this.formatLabels(Array.isArray(value) ? value : [value]);

        if (fragment.childNodes.length === 0) {
            const ph = document.createElement('span');
            ph.className = 'ws-selection-placeholder';
            ph.textContent = this._placeholderText;
            this.$refs.selection.appendChild(ph);
            return;
        }

        this.$refs.selection.appendChild(fragment);
    },

    formatLabels(values) {
        const fragment = document.createDocumentFragment();

        values
            .map((value) => {
                const option = this.options.find((o) => o.value === String(value));
                if (!option) {
                    return null;
                }
                return {
                    value: option.value,
                    label: option.label,
                    class: option.class,
                    htmlPrefix: option.htmlPrefix,
                    htmlSuffix: option.htmlSuffix,
                };
            })
            .filter(Boolean)
            .sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }))
            .forEach(({ value, label, class: optionClass, htmlPrefix, htmlSuffix }) => {
                if (this.selectMultiple) {
                    const badge = document.createElement('span');
                    badge.className = optionClass ? `ws-selection-badge ${optionClass}` : 'ws-selection-badge';

                    const badgeLabel = document.createElement('span');
                    badgeLabel.className = 'ws-selection-badge-label';
                    if (htmlPrefix || htmlSuffix) {
                        badgeLabel.innerHTML =
                            (htmlPrefix ?? '') + '<span>' + this._escapeHtml(label) + '</span>' + (htmlSuffix ?? '');
                    } else {
                        badgeLabel.innerHTML = '<span>' + this._escapeHtml(label) + '</span>';
                    }
                    badge.appendChild(badgeLabel);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'ws-selection-badge-remove';
                    removeBtn.dataset.wsRemove = String(value);
                    removeBtn.tabIndex = -1;
                    removeBtn.appendChild(document.createElement('span'));
                    badge.appendChild(removeBtn);

                    fragment.appendChild(badge);
                } else {
                    const span = document.createElement('span');
                    span.className = optionClass ? `ws-selection-simple ${optionClass}` : 'ws-selection-simple';

                    if (htmlPrefix || htmlSuffix) {
                        span.innerHTML = (htmlPrefix ?? '') + '<span>' + this._escapeHtml(label) + '</span>' + (htmlSuffix ?? '');
                    } else {
                        span.innerHTML = '<span>' + this._escapeHtml(label) + '</span>';
                    }

                    fragment.appendChild(span);
                }
            });

        return fragment;
    },

    renderOptions() {
        const container = this.$refs.optionList;
        container.replaceChildren();

        for (const item of this.displayedOptions) {
            if (item.type === 'group') {
                const el = document.createElement('div');
                el.className = 'ws-select-optgroup-label';
                el.setAttribute('role', 'presentation');
                el.setAttribute('aria-hidden', 'true');
                el.textContent = item.label;
                container.appendChild(el);
                continue;
            }

            const el = document.createElement('div');
            el.className = item.class ? `ws-select-option ${item.class}` : 'ws-select-option';
            el.setAttribute('role', 'option');
            el.setAttribute('data-ws-option', '');
            el.setAttribute('data-ws-value', item.value);
            el.setAttribute('aria-selected', String(this.checkedValues.includes(item.value)));

            if (item.optgroup != null) {
                el.setAttribute('data-ws-optgroup', '');
            }

            if (item.disabled) {
                el.setAttribute('aria-disabled', 'true');
            }

            const inner = document.createElement('div');

            if (item.htmlPrefix || item.htmlSuffix) {
                inner.innerHTML =
                    (item.htmlPrefix ?? '') + '<span>' + this._escapeHtml(item.label) + '</span>' + (item.htmlSuffix ?? '');
            } else {
                inner.innerHTML = '<span>' + this._escapeHtml(item.label) + '</span>';
            }

            el.appendChild(inner);
            container.appendChild(el);
        }

        this.updateOptionElements();

        if (this.floatingShown && this.optionElements.length > 0) {
            const selectedEl = this.optionElements.find((el) => this.checkedValues.includes(el.dataset.wsValue));
            this.setHighlight(selectedEl ? this.optionElements.indexOf(selectedEl) : 0);
            this.scrollToHighlighted();
        }
    },

    _escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    optionAttrs(option) {
        const attrs = {
            class: option.class ? `ws-select-option ${option.class}` : 'ws-select-option',
            role: 'option',
            'data-ws-option': '',
            'data-ws-value': option.value,
            'aria-selected': this.checkedValues.includes(option.value),
        };

        if (option.optgroup != null) {
            attrs['data-ws-optgroup'] = '';
        }

        if (option.disabled) {
            attrs['aria-disabled'] = 'true';
        }

        return attrs;
    },

    groupAttrs() {
        return {
            class: 'ws-select-optgroup-label',
            role: 'presentation',
            'aria-hidden': 'true',
        };
    },

    /**
     * Handlers
     */
    onEnter(event) {
        if (this.floatingShown) {
            event.preventDefault();
            this.navigateSelect();
            return;
        }

        const form = this.$el.closest('form');
        if (form) {
            form.requestSubmit();
        } else {
            event.preventDefault();
            this.selectShow();
        }
    },

    onNavigationSelect(el) {
        this.selectOption(this.optionElements.indexOf(el));
    },

    /**
     * Utils
     */
    _hasRealSelection() {
        if (!this.$wire || !this.selectWiremodel) {
            return false;
        }

        const value = this.$wire.$get(this.selectWiremodel);

        if (value === null || value === undefined) {
            return false;
        }

        if (Array.isArray(value)) {
            return value.length > 0;
        }

        if (String(value) === '') {
            return false;
        }

        if (this.selectEmptyValue !== null && String(value) === this.selectEmptyValue) {
            return false;
        }

        return true;
    },
}));
