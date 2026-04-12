/**
 * Alpine component for the WireStrap animated counter.
 *
 * Animates a numeric value from its current position to a target using
 * requestAnimationFrame with a linear easing over a configurable duration.
 *
 * Two usage modes:
 *   - Static: reads data-ws-count from the element on mount and watches it
 *     via MutationObserver so Livewire morphs trigger re-animation automatically.
 *   - Reactive (x-effect): when x-effect is present on the element, the consumer
 *     drives updates by calling animate() directly; the MutationObserver is skipped.
 *
 * Data attributes read from the root element:
 *   data-ws-count    - numeric target value.
 *   data-ws-decimals - number of decimal places to display (default: 0).
 *   data-ws-duration - animation duration in ms (default: 1000).
 */
Alpine.data('wsCounter', () => ({
    /**
     * State
     */
    count: '0',
    _current: 0,
    _target: null,
    _frameId: null,

    /**
     * Config
     */
    _decimals: 0,
    _duration: 1000,

    /**
     * Cleanup
     */
    _observer: null,

    /**
     * Lifecycle
     */
    init() {
        this._decimals = parseInt(this.$el.getAttribute('data-ws-decimals')) || 0;
        this._duration = parseInt(this.$el.getAttribute('data-ws-duration')) || 1000;
        this.count = (0).toFixed(this._decimals);

        // When the consumer uses x-effect to call animate() directly (e.g. for reactive
        // Livewire values), skip the MutationObserver-based auto-animation to avoid conflicts.
        if (this.$el.hasAttribute('x-effect')) {
            return;
        }

        this.animate(this.$el.getAttribute('data-ws-count'));

        this._observer = new MutationObserver(() => {
            this.animate(this.$el.getAttribute('data-ws-count'));
        });

        this._observer.observe(this.$el, { attributes: true, attributeFilter: ['data-ws-count'] });
    },

    destroy() {
        if (this._frameId !== null) {
            cancelAnimationFrame(this._frameId);
        }

        if (this._observer) {
            this._observer.disconnect();
        }
    },

    /**
     * Counter
     */
    animate(end) {
        end = +end;
        if (!isFinite(end)) {
            return;
        }

        if (end === this._target) {
            return;
        }

        this._target = end;
        this._startAnimation(end);
    },

    _startAnimation(end) {
        if (this._frameId !== null) {
            cancelAnimationFrame(this._frameId);
            this._frameId = null;
        }

        const start = this._current;
        const range = end - start;

        if (range === 0) {
            return;
        }

        const duration = this._duration;
        const startTime = performance.now();

        const step = (now) => {
            const elapsed = Math.max(0, now - startTime);
            const progress = Math.min(elapsed / duration, 1);

            this._current = start + range * progress;
            this.count = this._current.toFixed(this._decimals);

            if (progress < 1) {
                this._frameId = requestAnimationFrame(step);
            } else {
                this._current = end;
                this.count = end.toFixed(this._decimals);
                this._frameId = null;
            }
        };

        this._frameId = requestAnimationFrame(step);
    },
}));
