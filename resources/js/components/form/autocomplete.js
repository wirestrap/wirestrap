/**
 * Alpine component for the WireStrap autocomplete input.
 *
 * Combines floatingElement and listNavigation to provide a text input with a
 * floating suggestion list. Supports single and multiple (tag) modes.
 *
 * Suggestion loading:
 *   Suggestions are loaded lazily on first focus by calling a Livewire method
 *   (data-ws-wire-options). An optional data-ws-wire-options-watch property path triggers
 *   a reload whenever its value changes in the Livewire component.
 *   The ws:refresh custom event forces a reload programmatically.
 *   The method may return list<string> or list<{value: string, label: string}>; strings
 *   are normalised to {value, label} internally.
 *
 * Ghost text:
 *   When a suggestion starts with the current query, the remaining suffix is
 *   shown as ghost text in the input via the ghostSuffix reactive property.
 *
 * Multiple (tag) mode:
 *   In multiple mode the selected values are stored as an array of strings in
 *   selectedTags and synced to Livewire via wire.set(). Comma, Tab, and Enter
 *   all confirm the current query as a new tag.
 *
 * Data attributes read from the root element:
 *   data-ws-wire-options             - Livewire method name that returns the suggestion array.
 *   data-ws-wire-options-params      - JSON-encoded list of arguments passed to the method. Each item is
 *                                      passed as-is except objects with a "ws-wire" key, resolved via
 *                                      $wire.$get(param['ws-wire']) at call time.
 *   data-ws-wire-options-watch       - dot-path to a Livewire property; invalidates cache on change.
 *   data-ws-wire-options-reset       - JSON-encoded value to set the model to when the watched property changes.
 *   data-ws-wire-options-reset-live  - "true" to trigger a server round-trip when applying the reset.
 *   data-ws-multiple           - "true" for tag mode.
 *   data-ws-live               - "true" to use live: true when syncing tags to Livewire.
 *   data-ws-wiremodel          - dot-path to the Livewire property for tag mode sync.
 */
import { floatingElement, defaultDropdownConfig } from '../../mixins/floating-element.js';
import { listNavigation } from '../../mixins/list-navigation.js';
import { lazyLoader } from '../../mixins/lazy-loader.js';

Alpine.data('wsAutocomplete', () => ({
    /**
     * Mixins
     */
    ...floatingElement(),
    ...listNavigation(),
    ...lazyLoader(),

    /**
     * Binding
     */
    autocompleteRoot: {
        ['x-bind:class']() {
            return { 'ws-autocomplete-has-value': this.hasValue };
        },
    },

    inputWrapper: {
        ['x-ref']: 'inputWrapper',
        ['x-on:click'](event) {
            const removeBtn = event.target.closest('[data-ws-tag-remove]');
            if (removeBtn) {
                this.removeTag(removeBtn.dataset.wsTagRemove);
                return;
            }

            event.target === this.$el && this.$refs.input.focus();
        },
        ['x-on:pointerdown'](event) {
            event.target.closest('[data-ws-tag-remove]') && event.preventDefault();
        },
    },

    autocompleteInput: {
        ['x-on:input'](event) {
            this._dropdownActive = true;
            this.query = event.target.value;
        },
        ['x-on:keydown'](event) {
            this.onKeydown(event);
        },
        ['x-on:focus']() {
            this.onFocus();
        },
        ['x-on:focusout'](event) {
            this.onFocusout(event);
        },
        [':aria-autocomplete']() {
            return 'list';
        },
        [':aria-expanded']() {
            return this.floatingShown;
        },
        [':aria-haspopup']() {
            return 'listbox';
        },
    },

    /**
     * State
     */
    query: '',
    ghostSuffix: '',
    suggestions: [],
    _normalizedSuggestions: [],
    _normalizedValues: [],
    _suggestionMap: null,
    selectedTags: [],
    invalidIndices: [],
    _dropdownActive: false,

    /**
     * Config
     */
    autocompleteWiremodel: null,
    autocompleteDropdownOffset: 0,
    autocompletePosition: 'absolute',
    multiple: false,
    live: false,
    autocompleteMinChars: 0,
    autocompleteNoDropdown: false,
    wireOptionsMethod: null,
    wireOptionsMethodWatch: null,
    wireOptionsResetValue: null,
    wireOptionsResetLive: false,

    /**
     * Cleanup
     */
    _refreshHandler: null,
    _watchUnsub: null,
    _valueWatchUnsub: null,
    _morphedUnsub: null,

    /**
     * Computed
     */
    get visibleOptions() {
        return this.optionElements;
    },

    get hasValue() {
        return this.query.length > 0 || (this.multiple && this.selectedTags.length > 0);
    },

    get filteredSuggestions() {
        if (this.autocompleteNoDropdown || !this._dropdownActive) {
            return [];
        }

        if (this.query.length < this.autocompleteMinChars) {
            return [];
        }

        const q = this._normalize(this.query);

        return this.suggestions.filter((s, i) => {
            const normalizedLabel = this._normalizedSuggestions[i];
            const normalizedValue = this._normalizedValues[i];

            if (q) {
                const labelMatches = normalizedLabel.startsWith(q);
                const valueMatches = normalizedValue.includes(q);

                if (!labelMatches && !valueMatches) {
                    return false;
                }

                // Exact value match: already captured in the input
                if (normalizedValue === q) {
                    return false;
                }
            }

            if (this.multiple && this.selectedTags.some((t) => this._normalize(t) === normalizedValue)) {
                return false;
            }

            return true;
        });
    },

    /**
     * Lifecycle
     */
    init() {
        this.wireOptionsMethod = this.$el.getAttribute('data-ws-wire-options');
        this.wireOptionsMethodWatch = this.$el.getAttribute('data-ws-wire-options-watch');
        this.wireOptionsResetValue = this.$el.getAttribute('data-ws-wire-options-reset');
        this.wireOptionsResetLive = this.$el.getAttribute('data-ws-wire-options-reset-live') === 'true';
        this.autocompleteDropdownOffset = parseInt(this.$el.getAttribute('data-ws-dropdown-offset') || 0, 10);
        this.autocompletePosition = this.$el.getAttribute('data-ws-position') || 'absolute';
        this.autocompleteMinChars = parseInt(this.$el.getAttribute('data-ws-min-chars') || 0, 10);
        this.autocompleteNoDropdown = this.$el.getAttribute('data-ws-no-dropdown') === 'true';

        if (this.wireOptionsMethod) {
            this._refreshHandler = () => {
                this.lazyReset();
                this.loadSuggestions();
            };
            this.$el.addEventListener('ws:refresh', this._refreshHandler);
        }
        this.multiple = this.$el.getAttribute('data-ws-multiple') === 'true';
        this.invalidIndices = this._readInvalidIndices();
        if (this.multiple && this.$wire) {
            this._morphedUnsub = this.$wire.$hook('morphed', () => {
                this.invalidIndices = this._readInvalidIndices();
                this.renderTags();
            });
        }
        this.live = this.$el.getAttribute('data-ws-live') === 'true';
        this.autocompleteWiremodel = this.$el.getAttribute('data-ws-wiremodel');

        // Keep suggestions in the DOM during the close transition
        this.onFloatingHidden = () => {
            this._dropdownActive = false;
        };

        this.$watch('filteredSuggestions', (suggestions) => {
            const inputFocused = this.$refs.input && document.activeElement === this.$refs.input;

            // Render imperatively so DOM is ready for updateOptionElements() immediately.
            this.renderSuggestions(suggestions);
            this.updateOptionElements();

            if (suggestions.length > 0 && inputFocused) {
                this.autocompleteOpen();
            } else {
                this.autocompleteClose();
            }
        });

        this.$nextTick(() => {
            const reference = this.multiple ? this.$refs.inputWrapper : this.$refs.input;
            this.floatingInit(reference, this.$refs.floatable, this.getFloatingConfig());

            if (this.wireOptionsMethodWatch && this.$wire) {
                this._watchUnsub = this.$wire.$watch(this.wireOptionsMethodWatch, () => {
                    // Mark suggestions as stale so the next focus triggers a fresh load.
                    // Only reload immediately if the dropdown is already open.
                    this.lazyReset();

                    if (this.wireOptionsResetValue !== null && this.autocompleteWiremodel) {
                        const resetVal = JSON.parse(this.wireOptionsResetValue);
                        this.$wire.$set(this.autocompleteWiremodel, resetVal, this.wireOptionsResetLive);

                        if (!this.multiple) {
                            this.query = resetVal !== null ? String(resetVal) : '';
                            this.autocompleteClose();
                        }
                    }

                    if (this.floatingShown) {
                        this.loadSuggestions();
                    }
                });
            }

            if (!this.multiple) {
                this.query = this.$refs.input.value;
            } else if (this.$wire && this.autocompleteWiremodel) {
                const current = this.$wire.$get(this.autocompleteWiremodel);
                this.selectedTags = Array.isArray(current) ? [...current] : [];
                this.renderTags();

                this._valueWatchUnsub = this.$wire.$watch(this.autocompleteWiremodel, (value) => {
                    this.selectedTags = Array.isArray(value) ? [...value] : [];
                    this.renderTags();
                });
            }
        });
    },

    destroy() {
        this._watchUnsub?.();
        this._valueWatchUnsub?.();
        this._refreshHandler && this.$el.removeEventListener('ws:refresh', this._refreshHandler);
        this._morphedUnsub?.();
        this.floatingDestroy();
    },

    /**
     * Floating autocomplete
     */
    getFloatingConfig() {
        return defaultDropdownConfig(this.autocompleteDropdownOffset, this.autocompletePosition);
    },

    autocompleteOpen() {
        if (this.$el.hasAttribute('disabled')) {
            return;
        }

        const reference = this.multiple ? this.$refs.inputWrapper : this.$refs.input;
        this.floatingEnsureInit(reference, this.$refs.floatable, this.getFloatingConfig());
        this.floatingShow();
    },

    autocompleteClose() {
        this.clearHighlight();
        this.highlightedIndex = -1;
        this.ghostSuffix = '';
        this.floatingHide();
    },

    /**
     * Suggestions
     */
    loadSuggestions() {
        if (!this.$wire || !this.wireOptionsMethod) {
            return;
        }

        const paramsJson = this.$root.getAttribute('data-ws-wire-options-params');
        const params = paramsJson ? JSON.parse(paramsJson) : [];
        const args = params.map((param) => {
            if (param !== null && typeof param === 'object' && 'ws-wire' in param) {
                return this.$wire.$get(param['ws-wire']);
            }
            return param;
        });

        this.$wire.$call(this.wireOptionsMethod, ...args).then((result) => {
            this._setSuggestions(Array.isArray(result) ? result : []);
            this._methodLoaded = true; // marks lazyLoader as loaded
        });
    },

    _setSuggestions(raw) {
        // Deduplicate by normalized value. Both string and {value, label, html_prefix, html_suffix, optgroup}
        // forms are accepted; strings are normalised to {value: s, label: s}.
        const seen = new Set();
        const suggestions = [];
        const normalizedLabels = [];
        const normalizedValues = [];
        const suggestionMap = new Map();

        for (const s of raw) {
            const isObj = s !== null && typeof s === 'object';
            const value = isObj ? String(s.value ?? '') : String(s);
            const label = isObj ? String(s.label ?? value) : String(s);
            const normalizedValue = this._normalize(value);

            if (!seen.has(normalizedValue)) {
                seen.add(normalizedValue);
                const htmlPrefix = isObj && s.html_prefix != null ? String(s.html_prefix) : null;
                const htmlSuffix = isObj && s.html_suffix != null ? String(s.html_suffix) : null;
                const optgroup = isObj && s.optgroup != null ? String(s.optgroup) : null;
                const optClass = isObj && s.class != null ? String(s.class) : null;
                suggestions.push({ value, label, optgroup, htmlPrefix, htmlSuffix, optClass });
                normalizedLabels.push(this._normalize(label));
                normalizedValues.push(normalizedValue);
                suggestionMap.set(value, { label, htmlPrefix, htmlSuffix, optClass });
            }
        }

        this.suggestions = suggestions;
        this._normalizedSuggestions = normalizedLabels;
        this._normalizedValues = normalizedValues;
        this._suggestionMap = suggestionMap;
    },

    /**
     * Selection
     */
    selectOption(index) {
        const el = this.optionElements[index];
        if (!el) {
            return;
        }

        this.onNavigationSelect(el);
    },

    addTag(value) {
        if (!value || this.selectedTags.some((t) => this._normalize(t) === this._normalize(value))) {
            return;
        }

        this.selectedTags = [...this.selectedTags, value];
        this.renderTags();
        this.$refs.input.value = '';
        this.query = '';
        this.ghostSuffix = '';
        this.autocompleteClose();
        this.$refs.input.focus();

        if (this.$wire && this.autocompleteWiremodel) {
            this.$wire.$set(this.autocompleteWiremodel, this.selectedTags, this.live);
        }
    },

    removeTag(tag) {
        const removedIndex = this.selectedTags.indexOf(tag);
        this.selectedTags = this.selectedTags.filter((t) => t !== tag);

        if (removedIndex !== -1) {
            this.invalidIndices = this.invalidIndices
                .filter((i) => i !== removedIndex)
                .map((i) => (i > removedIndex ? i - 1 : i));
        }

        this.renderTags();
        this.$refs.input.focus();

        if (this.$wire && this.autocompleteWiremodel) {
            this.$wire.$set(this.autocompleteWiremodel, this.selectedTags, this.live);
        }
    },

    /**
     * Suggestions rendering
     */
    renderSuggestions(suggestions) {
        const container = this.$refs.optionList;
        if (!container) {
            return;
        }

        container.replaceChildren();

        // Separate ungrouped and grouped options, preserving insertion order within each group.
        const ungrouped = [];
        const groups = new Map();

        for (const s of suggestions) {
            if (s.optgroup != null) {
                if (!groups.has(s.optgroup)) {
                    groups.set(s.optgroup, []);
                }
                groups.get(s.optgroup).push(s);
            } else {
                ungrouped.push(s);
            }
        }

        const items = [];
        for (const s of ungrouped) {
            items.push({ type: 'option', data: s });
        }
        for (const [label, options] of groups) {
            items.push({ type: 'group', label });
            for (const s of options) {
                items.push({ type: 'option', data: s });
            }
        }

        for (const item of items) {
            if (item.type === 'group') {
                const el = document.createElement('div');
                el.className = 'ws-autocomplete-optgroup-label';
                el.setAttribute('role', 'presentation');
                el.setAttribute('aria-hidden', 'true');
                el.textContent = item.label;
                container.appendChild(el);
            } else {
                const { value, label, optgroup, htmlPrefix, htmlSuffix, optClass } = item.data;
                const el = document.createElement('div');
                el.className = optClass ? `ws-autocomplete-option ${optClass}` : 'ws-autocomplete-option';
                el.setAttribute('role', 'option');
                el.setAttribute('data-ws-option', '');
                el.setAttribute('data-ws-value', value);
                if (optgroup != null) {
                    el.setAttribute('data-ws-optgroup', '');
                }
                el.innerHTML = (htmlPrefix ?? '') + '<span>' + this._escapeHtml(label) + '</span>' + (htmlSuffix ?? '');
                container.appendChild(el);
            }
        }
    },

    /**
     * Tags
     */
    renderTags() {
        if (!this.multiple || !this.$refs.tagList) {
            return;
        }

        const container = this.$refs.tagList;
        container.replaceChildren();

        const labelRemove = this.$el.getAttribute('data-ws-label-remove') ?? '';

        for (let i = 0; i < this.selectedTags.length; i++) {
            const value = this.selectedTags[i];
            const entry = this._suggestionMap?.get(value) ?? null;
            const label = entry?.label ?? value;
            const { htmlPrefix, htmlSuffix, optClass } = entry ?? { htmlPrefix: null, htmlSuffix: null, optClass: null };

            const tagEl = document.createElement('span');
            tagEl.className =
                'ws-autocomplete-tag' +
                (this.invalidIndices.includes(i) ? ' ws-autocomplete-tag-invalid' : '') +
                (optClass ? ' ' + optClass : '');

            const labelEl = document.createElement('span');
            labelEl.className = 'ws-autocomplete-tag-label';
            if (htmlPrefix || htmlSuffix) {
                labelEl.innerHTML = (htmlPrefix ?? '') + '<span>' + this._escapeHtml(label) + '</span>' + (htmlSuffix ?? '');
            } else {
                labelEl.textContent = label;
            }
            tagEl.appendChild(labelEl);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ws-autocomplete-tag-remove';
            btn.dataset.wsTagRemove = value;
            btn.tabIndex = -1;
            btn.setAttribute('aria-label', labelRemove + ' ' + label);
            btn.appendChild(document.createElement('span'));
            tagEl.appendChild(btn);

            container.appendChild(tagEl);
        }
    },

    /**
     * Handlers
     */
    onFocus() {
        this._dropdownActive = true;

        !this.autocompleteNoDropdown && this.wireOptionsMethod && this.lazyLoad(() => this.loadSuggestions());
    },

    onFocusout(event) {
        if (!this.$el.contains(event.relatedTarget)) {
            this.autocompleteClose();
        }
    },

    onKeydown(event) {
        if (event.key === 'ArrowDown') {
            event.preventDefault();

            if (this.floatingShown) {
                this.navigate(1);
            } else {
                this.onFocus();
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.navigate(-1);
        } else if (event.key === 'Enter') {
            if (this.floatingShown && this.highlightedIndex >= 0) {
                event.preventDefault();
                this.navigateSelect();
            } else if (this.multiple && this.query.trim()) {
                event.preventDefault();
                this.addTag(this.query.trim());
            }
        } else if (event.key === 'Tab') {
            if (this.floatingShown && this.highlightedIndex >= 0) {
                event.preventDefault();
                this.navigateSelect();
            } else if (this.multiple && this.query.trim()) {
                event.preventDefault();
                this.addTag(this.query.trim());
            }
        } else if (event.key === ',') {
            if (this.multiple && this.query.trim()) {
                event.preventDefault();
                this.addTag(this.query.trim());
            }
        } else if (event.key === 'Backspace') {
            if (this.multiple && !this.query && this.selectedTags.length > 0) {
                this.removeTag(this.selectedTags[this.selectedTags.length - 1]);
            }
        } else if (event.key === 'Escape') {
            this.autocompleteClose();
            event.preventDefault();
            event.stopPropagation();
        }
    },

    onNavigationUpdate() {
        this.ghostSuffix = '';

        if (this.optionElements.length > 0) {
            this.setHighlight(0);
        }
    },

    onNavigationHighlight(el) {
        if (!el) {
            this.ghostSuffix = '';
            return;
        }

        // Ghost text is based on the value (what gets injected into the input),
        // not the label. Slice from the original query length to preserve casing.
        const value = el.dataset.wsValue ?? el.textContent.trim();
        const normalizedValue = this._normalize(value);
        const normalizedQuery = this._normalize(this.query);

        if (normalizedValue.startsWith(normalizedQuery)) {
            this.ghostSuffix = value.slice(this.query.length);
        } else {
            this.ghostSuffix = '';
        }
    },

    onNavigationSelect(el) {
        if (!el) {
            return;
        }

        const value = el.dataset.wsValue ?? el.textContent.trim();

        if (this.multiple) {
            this.addTag(value);
            return;
        }

        this.$refs.input.value = value;
        this.query = value;
        this.ghostSuffix = '';
        this.autocompleteClose();
        this.$refs.input.focus();
        this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
        this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
    },

    /**
     * Utils
     */
    _readInvalidIndices() {
        const raw = this.$el.getAttribute('data-ws-invalid-indices');
        if (!raw) {
            return [];
        }
        try {
            return JSON.parse(raw);
        } catch {
            return [];
        }
    },

    _normalize(str) {
        return String(str)
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    },

    _escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
}));
