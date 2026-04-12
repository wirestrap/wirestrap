import { computePosition, autoUpdate, offset, flip, shift, size } from '@floating-ui/dom';
import { afterTransition } from '../utils/transition';

/**
 * Default @floating-ui/dom config for dropdown-style floating elements (select, autocomplete).
 * Places the floatable below the reference at fixed position, flips to top if needed,
 * and matches the reference width.
 *
 * @param {number} offsetY - Main-axis (Y) offset in pixels between the reference and the dropdown.
 * @returns {object}
 */
export function defaultDropdownConfig(offsetY = 0) {
    return {
        placement: 'bottom',
        strategy: 'fixed',
        middleware: [
            offset({ mainAxis: offsetY }),
            flip({ fallbackPlacements: ['top', 'bottom'] }),
            shift(),
            size({
                apply({ rects, elements }) {
                    elements.floating.style.width = `${rects.reference.width}px`;
                },
            }),
        ],
    };
}

/**
 * Provides core @floating-ui/dom lifecycle management for floating elements
 * (dropdowns, popovers, tooltips, selects, autocompletes, etc.).
 *
 * Contract: the consuming component must call floatingInit(reference, floatable, config)
 * in init() and floatingDestroy() in destroy().
 *
 * Usage:
 *   1. Spread the mixin into your Alpine.data component.
 *   2. In init(), build your @floating-ui/dom config and call floatingInit(reference, floatable, config).
 *   3. In destroy(), call floatingDestroy().
 *
 * Configurable state:
 *   floatingHideDelay
 *      - milliseconds to wait before applying hide logic (default: 0).
 *      Set to e.g. 50 for hover-triggered elements to allow the cursor
 *      to travel between trigger and floatable without closing.
 *
 *   floatingTriggerOnHover
 *      - when true, floatingHide() re-checks hover state after the delay
 *      and cancels the hide if the element is still hovered.
 *      Also gates floatingHandleHover(). (default: false)
 *
 *   onFloatingShow   - callback
 *   onFloatingHide   - callback
 *   onFloatingHidden - callback
 */
export function floatingElement() {
    return {
        _floatingCleanup: null,
        _floatingReference: null,
        _floatingEl: null,
        _floatingArrowEl: null,
        _floatingConfig: null,
        floatingShown: false,
        floatingHideDelay: 0,
        floatingTriggerOnHover: false,
        floatingShowTimeout: null,
        _floatingCancelTransition: null,
        floatingListeners: [],
        onFloatingShow: null,
        onFloatingHide: null,
        onFloatingHidden: null,

        floatingInit(reference, floatable, config) {
            this._floatingReference = reference;
            this._floatingEl = floatable;
            this._floatingArrowEl = floatable.querySelector('[data-ws-arrow]');
            this._floatingConfig = config;

            floatable.style.position = config.strategy ?? 'absolute';
            floatable.style.top = '0';
            floatable.style.left = '0';
            floatable.style.display = 'none';

            if (this.$wire) {
                const unsub = this.$wire.$hook('morphed', () => {
                    if (this.floatingShown) {
                        // First call repositions immediately after the morph DOM patch.
                        // Second call (rAF) runs after the browser has painted the new layout,
                        // catching cases where morph changes sizes that only resolve on next frame.
                        this._floatingUpdate();
                        requestAnimationFrame(() => this._floatingUpdate());
                    }
                });
                this.floatingListeners.push(unsub);
            }
        },

        _floatingUpdate() {
            if (!this._floatingReference || !this._floatingEl || !this._floatingConfig) {
                return;
            }

            computePosition(this._floatingReference, this._floatingEl, this._floatingConfig).then(
                ({ x, y, placement, middlewareData }) => {
                    Object.assign(this._floatingEl.style, {
                        left: `${x}px`,
                        top: `${y}px`,
                    });

                    this._floatingEl.dataset.floatingPlacement = placement;

                    if (middlewareData.arrow && this._floatingArrowEl) {
                        const { x: arrowX, y: arrowY } = middlewareData.arrow;
                        this._floatingArrowEl.style.position = 'absolute';

                        Object.assign(this._floatingArrowEl.style, {
                            left: arrowX != null ? `${arrowX}px` : '',
                            top: arrowY != null ? `${arrowY}px` : '',
                        });
                    }
                },
            );
        },

        _floatingSetAutoUpdate(enabled) {
            if (enabled) {
                if (!this._floatingCleanup) {
                    this._floatingCleanup = autoUpdate(this._floatingReference, this._floatingEl, () => this._floatingUpdate());
                }
            } else {
                this._floatingCleanup?.();
                this._floatingCleanup = null;
            }
        },

        floatingEnsureInit(reference, floatable, config) {
            if (this._floatingEl) {
                return;
            }

            this.floatingInit(reference, floatable, config);
        },

        floatingShow() {
            clearTimeout(this.floatingShowTimeout);
            this._floatingCancelTransition?.();
            this._floatingCancelTransition = null;
            this._floatingEl.style.removeProperty('display');
            this._floatingSetAutoUpdate(true);
            this.floatingShown = true;
            this.onFloatingShow?.();
        },

        floatingHide() {
            if (!this.floatingShown) {
                return;
            }

            clearTimeout(this.floatingShowTimeout);
            this._floatingCancelTransition?.();
            this._floatingCancelTransition = null;
            // Capture before the timeout: the component may be destroyed before the delay
            // fires, which would null out this._floatingEl and break the hide logic.
            const floatingEl = this._floatingEl;
            this.floatingShowTimeout = setTimeout(() => {
                if (this.floatingTriggerOnHover) {
                    this.floatingShown =
                        this.$el?.matches(':hover, :focus, :focus-within') ||
                        floatingEl?.matches(':hover, :focus, :focus-within');
                } else {
                    this.floatingShown = false;
                }

                if (!this.floatingShown) {
                    this._floatingSetAutoUpdate(false);
                    this.onFloatingHide?.();
                    if (floatingEl) {
                        this._floatingCancelTransition = afterTransition(floatingEl, () => {
                            floatingEl.style.display = 'none';
                            this._floatingCancelTransition = null;
                            this.onFloatingHidden?.();
                        });
                    }
                } else {
                    this._floatingCancelTransition?.();
                    this._floatingCancelTransition = null;
                }
            }, this.floatingHideDelay);
        },

        floatingToggle() {
            this.floatingShown ? this.floatingHide() : this.floatingShow();
        },

        floatingHandleHover(show) {
            this.floatingTriggerOnHover && (show ? this.floatingShow() : this.floatingHide());
        },

        floatingEventShow() {
            this.$nextTick(() => this.floatingShow());
        },

        floatingEventHide() {
            this.$nextTick(() => this.floatingHide());
        },

        floatingEventToggle() {
            this.$nextTick(() => (this.floatingShown ? this.floatingHide() : this.floatingShow()));
        },

        floatingDestroy() {
            this._floatingCleanup?.();
            clearTimeout(this.floatingShowTimeout);
            this._floatingCancelTransition?.();
            this._floatingCancelTransition = null;
            this.floatingListeners.forEach((listener) => listener());
        },
    };
}
