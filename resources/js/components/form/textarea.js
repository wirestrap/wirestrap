/**
 * Alpine component for the WireStrap auto-growing textarea.
 *
 * Resizes the textarea to fit its content on input and on external value changes.
 *
 * Deferred resize:
 *   If the textarea is inside a hidden panel (tab or accordion) at mount time,
 *   resize is deferred until the panel emits a ws-show event. If no panel is
 *   found, a ResizeObserver watches for the element to become visible.
 *
 * Data attributes read from the root element:
 *   data-ws-wiremodel - dot-path to the Livewire property to watch.
 */
Alpine.data('wsTextarea', () => ({
    /**
     * Binding
     */
    textarea: {
        ['x-on:input']() {
            this.resize();
        },
    },

    /**
     * Cleanup
     */
    _wireWatchUnsub: null,
    _resizeObserver: null,

    /**
     * Lifecycle
     */
    init() {
        const wiremodel = this.$el.dataset.wsWiremodel ?? null;

        if (wiremodel) {
            this._wireWatchUnsub = this.$wire.$watch(wiremodel, () => this.resize());
        }

        this.resize();

        if (!this.$el.offsetParent) {
            const panel = this.$el.closest('[data-ws-tab], [data-ws-accordion-panel]');

            if (panel) {
                panel.addEventListener('ws-show', () => this.resize(), { once: true });
            } else {
                this._resizeObserver = new ResizeObserver(() => {
                    if (this.$el.offsetParent) {
                        this._resizeObserver.disconnect();
                        this.resize();
                    }
                });
                this._resizeObserver.observe(this.$el);
            }
        }
    },

    destroy() {
        this._wireWatchUnsub?.();
        this._resizeObserver?.disconnect();
    },

    /**
     * Autosize
     */
    resize() {
        if (this.$el.style.height && this.$el.style.height !== 'auto') {
            const currentHeight = this.$el.offsetHeight;
            this.$el.parentElement.style.setProperty('--ws-textarea-height', `${currentHeight}px`);
            return;
        }

        this.$el.style.height = '0';
        const newHeight = this.$el.scrollHeight + 10;
        this.$el.style.height = '';

        this.$el.parentElement.style.setProperty('--ws-textarea-height', `${newHeight}px`);
    },
}));
