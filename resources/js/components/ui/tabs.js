/**
 * Alpine component for the WireStrap tabs.
 *
 * Manages the active tab key and runs a cross-fade + height transition when
 * switching panels. During the transition a _transitioning flag blocks re-entry.
 *
 * Transition sequence:
 *   1. Fade out the old panel over _duration ms.
 *   2. Hide old, show new at opacity 0, measure new panel height.
 *   3. Animate the inner container height and fade in the new panel in parallel.
 *   4. Clean up inline styles and release the transition lock.
 *
 * The transition duration is read from the --ws-tabs-transition-duration CSS
 * custom property (in seconds or ms - values < 5 are assumed to be seconds).
 *
 * Validation awareness:
 *   Mirrors accordion: after each Livewire morph, panels are scanned for elements
 *   with the configured invalid class and the matching nav link is marked.
 *
 * External control:
 *   When data-ws-events="true", listens for ws-tabs custom events carrying the
 *   target tab key as the event detail.
 *
 * Data attributes read from the root element:
 *   data-ws-default       - key of the initially active tab.
 *   data-ws-events        - "true" to enable external event control.
 *   data-ws-invalid-class - CSS class indicating a validation error inside a panel.
 */
Alpine.data('wsTabs', () => ({
    /**
     * Binding
     */
    navLink: {
        [':aria-selected']() {
            const key = this.$el.getAttribute('data-ws-tab');
            return this.activeTab === key;
        },
        ['x-bind:class']() {
            const key = this.$el.getAttribute('data-ws-tab');
            return {
                active: this.activeTab === key,
                'ws-tabs-nav-link-invalid': this.invalidTabs.includes(key),
            };
        },
        ['x-on:click']() {
            this.setTab(this.$el.getAttribute('data-ws-tab'));
        },
    },

    /**
     * State
     */
    activeTab: null,
    invalidTabs: [],
    _transitioning: false,

    /**
     * Config
     */
    _invalidClass: null,
    _duration: 0,

    /**
     * Cleanup
     */
    _cleanup: null,
    _invalidCleanup: null,
    _timeout1: null,
    _timeout2: null,

    /**
     * Lifecycle
     */
    init() {
        this.activeTab = this.$el.getAttribute('data-ws-default');
        // CSS custom properties express durations in seconds (e.g. 0.3), but setTimeout
        // expects milliseconds. Values < 5 are assumed to be seconds and are converted.
        const rawDuration = parseFloat(
            getComputedStyle(this.$refs.tabsContent).getPropertyValue('--ws-tabs-transition-duration'),
        );
        this._duration = rawDuration && rawDuration < 5 ? rawDuration * 1000 : rawDuration;

        if (this.$el.getAttribute('data-ws-events') === 'true') {
            const onSet = (e) => this.setTab(e.detail);
            this.$el.addEventListener('ws-tabs', onSet);
            this._cleanup = () => this.$el.removeEventListener('ws-tabs', onSet);
        }

        this._invalidClass = this.$el.getAttribute('data-ws-invalid-class') || null;
        if (this._invalidClass && this.$wire) {
            this._invalidCleanup = this.$wire.$hook('morphed', () => this._scanInvalid());
        }
    },

    destroy() {
        this._cleanup?.();
        this._invalidCleanup?.();
        clearTimeout(this._timeout1);
        clearTimeout(this._timeout2);
    },

    /**
     * Tabs
     */
    setTab(key) {
        if (key === this.activeTab || this._transitioning) {
            return;
        }

        const content = this.$refs.tabsContent;
        const inner = this.$refs.tabsInner;
        const oldPanel = content.querySelector(`[data-ws-tab="${this.activeTab}"]`);
        const newPanel = content.querySelector(`[data-ws-tab="${key}"]`);

        if (!oldPanel || !newPanel) {
            this.activeTab = key;
            return;
        }

        this._transitioning = true;
        this.activeTab = key;

        // Lock inner height to prevent layout jump
        inner.style.height = inner.offsetHeight + 'px';

        // Fade out old panel
        oldPanel.style.transition = `opacity ${this._duration}ms ease`;
        oldPanel.style.opacity = '0';

        this._timeout1 = setTimeout(() => {
            oldPanel.classList.add('ws-tabs-panel--hidden');
            oldPanel.style.transition = '';
            oldPanel.style.opacity = '';

            // Place new panel at opacity 0 before showing it
            newPanel.style.opacity = '0';
            newPanel.classList.remove('ws-tabs-panel--hidden');
            newPanel.dispatchEvent(new CustomEvent('ws-show'));

            // First rAF: let the browser paint the newly shown panel (display: block, opacity: 0).
            // Second rAF: measure the panel's natural height, then start the animation.
            // Two frames are needed because offsetHeight forces a reflow that must happen
            // after the panel is both visible and in its final layout state.
            requestAnimationFrame(() => {
                const targetHeight = newPanel.offsetHeight;

                requestAnimationFrame(() => {
                    // Animate inner to new panel height
                    inner.style.transition = `height ${this._duration}ms ease`;
                    inner.style.height = `${targetHeight}px`;

                    // Fade in new panel
                    newPanel.style.transition = `opacity ${this._duration}ms ease`;
                    newPanel.style.opacity = '1';

                    this._timeout2 = setTimeout(() => {
                        newPanel.style.transition = '';
                        newPanel.style.opacity = '';
                        inner.style.transition = '';
                        inner.style.height = '';
                        this._transitioning = false;
                    }, this._duration);
                });
            });
        }, this._duration);
    },

    /**
     * Utils
     */
    _scanInvalid() {
        const panels = this.$refs.tabsContent.querySelectorAll('[data-ws-tab]');

        this.invalidTabs = Array.from(panels)
            .filter((panel) => panel.querySelector('.' + this._invalidClass))
            .map((panel) => panel.dataset.wsTab);
    },
}));
