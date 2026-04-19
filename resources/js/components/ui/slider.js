/**
 * Alpine component for the WireStrap slider (carousel).
 *
 * Implements a transform-based slide carousel. Navigation is offset-based:
 * _currentOffset is the index of the first visible slide. The number of
 * simultaneously visible slides is read from the --ws-slider-columns CSS
 * custom property, which can change with breakpoints.
 *
 * A ResizeObserver watches the root element and clamps the offset to the
 * maximum allowed value when the number of visible columns changes.
 *
 * External control:
 *   When data-ws-events="true", the component listens for ws-slider custom
 *   events and dispatches ws-slider-change after each navigation.
 *
 * Data attributes read from the root element:
 *   data-ws-scroll-by   - number of slides to advance per prev/next call (default: 1).
 *   data-ws-scroll-page - "true" to advance by the number of visible columns instead.
 *   data-ws-events      - "true" to enable external event control.
 */
Alpine.data('wsSlider', () => ({
    /**
     * Binding
     */
    prevArrow: {
        ['x-on:click']() {
            this.scrollPrev();
        },
        ['x-bind:disabled']() {
            return !this.canScrollPrev;
        },
    },

    nextArrow: {
        ['x-on:click']() {
            this.scrollNext();
        },
        ['x-bind:disabled']() {
            return !this.canScrollNext;
        },
    },

    /**
     * State
     */
    canScrollPrev: false,
    canScrollNext: false,
    _currentOffset: 0,
    _totalSlides: 0,
    _inner: null,

    /**
     * Config
     */
    _scrollBy: 1,
    _scrollPage: false,
    _events: false,

    /**
     * Cleanup
     */
    _resizeObserver: null,
    _cleanup: null,

    /**
     * Lifecycle
     */
    init() {
        this._inner = this.$refs.inner;
        this._scrollBy = parseInt(this.$root.getAttribute('data-ws-scroll-by') || '1');
        this._scrollPage = this.$root.getAttribute('data-ws-scroll-page') === 'true';
        this._events = this.$root.getAttribute('data-ws-events') === 'true';
        this._totalSlides = this._inner.querySelectorAll('.ws-slider-slide').length;

        this._updateArrows();

        this._resizeObserver = new ResizeObserver(() => {
            const maxOffset = Math.max(0, this._totalSlides - this._getColumns());

            if (this._currentOffset > maxOffset) {
                this._currentOffset = maxOffset;
            }

            this._applyTransform();
            this._updateArrows();
        });

        this._resizeObserver.observe(this.$root);

        if (this._events) {
            const onEvent = (e) => {
                const { action, index, step } = e.detail || {};
                if (action === 'prev') this.scrollPrev(step);
                else if (action === 'next') this.scrollNext(step);
                else if (action === 'goTo') this.goTo(index);
                else if (action === 'first') this.goTo(0);
                else if (action === 'last') this.goTo(Infinity); // clamped to maxOffset in goTo()
            };
            this.$root.addEventListener('ws-slider', onEvent);
            this._cleanup = () => this.$root.removeEventListener('ws-slider', onEvent);
        }
    },

    destroy() {
        this._resizeObserver?.disconnect();
        this._cleanup?.();
    },

    /**
     * Slider
     */

    scrollPrev(step = null) {
        if (!this.canScrollPrev) {
            return;
        }

        const columns = this._getColumns();
        const resolvedStep = step ?? (this._scrollPage ? columns : this._scrollBy);

        this._currentOffset = Math.max(0, this._currentOffset - resolvedStep);
        this._applyTransform();
        this._updateArrows();
    },

    scrollNext(step = null) {
        if (!this.canScrollNext) {
            return;
        }

        const columns = this._getColumns();
        const maxOffset = Math.max(0, this._totalSlides - columns);
        const resolvedStep = step ?? (this._scrollPage ? columns : this._scrollBy);

        this._currentOffset = Math.min(maxOffset, this._currentOffset + resolvedStep);
        this._applyTransform();
        this._updateArrows();
    },

    goTo(index) {
        const columns = this._getColumns();
        const maxOffset = Math.max(0, this._totalSlides - columns);
        this._currentOffset = Math.min(maxOffset, Math.max(0, index));
        this._applyTransform();
        this._updateArrows();
    },

    /**
     * Utils
     */
    _getColumns() {
        // Column count is driven by CSS (via the --ws-slider-columns custom property) so that
        // responsive breakpoints defined in SCSS automatically change the visible slide count.
        return parseInt(getComputedStyle(this.$root).getPropertyValue('--ws-slider-columns').trim()) || 1;
    },

    _getSlideWidth() {
        const firstSlide = this._inner.querySelector('.ws-slider-slide');
        return firstSlide ? firstSlide.offsetWidth : 0;
    },

    _getGap() {
        return parseFloat(getComputedStyle(this._inner).columnGap) || 0;
    },

    _applyTransform() {
        const translateX = -(this._currentOffset * (this._getSlideWidth() + this._getGap()));
        this._inner.style.transform = `translateX(${translateX}px)`;
    },

    _updateArrows() {
        this.canScrollPrev = this._currentOffset > 0;
        this.canScrollNext = this._currentOffset < this._totalSlides - this._getColumns();

        if (this._events) {
            this.$root.dispatchEvent(
                new CustomEvent('ws-slider-change', {
                    bubbles: true,
                    detail: {
                        offset: this._currentOffset,
                        canScrollPrev: this.canScrollPrev,
                        canScrollNext: this.canScrollNext,
                    },
                }),
            );
        }
    },
}));
