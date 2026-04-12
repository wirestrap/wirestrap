/**
 * Provides keyboard navigation and highlight state management for option lists.
 *
 * Contract: the consuming component must define:
 *   visibleOptions         - getter returning the navigable subset of optionElements.
 *   selectOption(index)    - called by the floatable click handler.
 *
 * Hooks to override in consuming components:
 *   onNavigationSelect(el)    - called by navigateSelect() to define what "selecting" means.
 *   onNavigationHighlight(el) - called when a new item is highlighted (e.g. to move DOM focus).
 *   onNavigationUpdate()      - called after updateOptionElements() re-scans the DOM.
 *
 * Disabled options are detected via the `aria-disabled="true"` attribute on the
 * option element itself, which must be set by the component's Blade template.
 *
 * Provided descriptor (x-bind):
 *   floatable  - binds click, mouseover, and pointerdown on the floating list container.
 *                Override in the consuming component to add extra bindings.
 */
export function listNavigation() {
    return {
        highlightedIndex: -1,
        optionElements: [],

        floatable: {
            ['x-ref']: 'floatable',
            ['role']: 'listbox',
            ['x-bind:class']() {
                return { show: this.floatingShown };
            },
            ['x-on:pointerdown'](event) {
                // Prevent the list from stealing focus; allow clicks on inputs inside the floatable.
                !event.target.closest('input') && event.preventDefault();
            },
            ['x-on:click'](event) {
                const option = event.target.closest('[data-ws-option]');
                if (!option) {
                    return;
                }

                this.selectOption(this.optionElements.indexOf(option));
            },
            ['x-on:mouseover'](event) {
                const option = event.target.closest('[data-ws-option]');
                if (!option) {
                    return;
                }

                const index = this.optionElements.indexOf(option);
                if (index !== this.highlightedIndex) {
                    this.setHighlight(index);
                }
            },
        },

        enabledOptions() {
            return this.optionElements.filter((el) => el.getAttribute('aria-disabled') !== 'true');
        },

        navigate(direction) {
            const navigable = this.visibleOptions.filter((el) => el.getAttribute('aria-disabled') !== 'true');
            if (navigable.length === 0) {
                return;
            }

            // highlightedIndex is an index into the full optionElements array, but wrapping
            // logic must operate on the navigable subset (disabled/hidden items excluded).
            // Look up the current element in optionElements first, then find its position in navigable.
            const currentInNavigable = navigable.indexOf(this.optionElements[this.highlightedIndex]);
            let nextInNavigable;

            if (direction === 1) {
                // Down: wrap to first if at end or not found
                nextInNavigable =
                    currentInNavigable === -1 || currentInNavigable === navigable.length - 1 ? 0 : currentInNavigable + 1;
            } else {
                // Up: wrap to last if at start or not found
                nextInNavigable = currentInNavigable <= 0 ? navigable.length - 1 : currentInNavigable - 1;
            }

            this.setHighlight(this.optionElements.indexOf(navigable[nextInNavigable]));
            this.scrollToHighlighted();
        },

        // Triggers selection of the currently highlighted item.
        // Each consuming component defines the actual behavior via onNavigationSelect().
        navigateSelect() {
            if (this.highlightedIndex < 0) {
                return;
            }

            this.onNavigationSelect(this.optionElements[this.highlightedIndex]);
        },

        updateOptionElements() {
            this.optionElements = Array.from(this.$refs.floatable.querySelectorAll('[data-ws-option]'));
            this.highlightedIndex = -1;
            this.onNavigationUpdate();
        },

        // Override in consuming component to define selection behavior.
        onNavigationSelect(_el) {},

        // Override in consuming component to react to highlight changes (e.g. move DOM focus).
        onNavigationHighlight(_el) {},

        // Override in consuming component to react after the option list is re-scanned.
        onNavigationUpdate() {},

        setHighlight(index) {
            // Remove previous highlight
            if (this.highlightedIndex >= 0) {
                this.optionElements[this.highlightedIndex]?.classList.remove('highlighted');
            }

            // Add new highlight
            this.highlightedIndex = index;
            if (index >= 0) {
                this.optionElements[index]?.classList.add('highlighted');
                this.onNavigationHighlight(this.optionElements[index]);
            }
        },

        clearHighlight() {
            this.optionElements.forEach((el) => el.classList.remove('highlighted'));
        },

        scrollToHighlighted() {
            // Defer to next tick: when options are filtered the highlighted element may not
            // be visible in the DOM yet (display: none → '') at the time this is called.
            this.$nextTick(() => {
                this.optionElements[this.highlightedIndex]?.scrollIntoView({ block: 'nearest' });
            });
        },
    };
}
