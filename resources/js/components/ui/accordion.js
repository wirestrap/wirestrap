import { animateHeight } from '../../utils/animateHeight';

// Cancel functions keyed by panel element, shared across all accordion instances.
const _panelAnimations = new WeakMap();

function _cancelPanelAnimation(panel) {
    const cancel = _panelAnimations.get(panel);
    if (cancel) {
        cancel();
        _panelAnimations.delete(panel);
    }
}

/**
 * Alpine component for the WireStrap accordion.
 *
 * Manages a list of open panel keys and animates each panel open/closed using
 * a CSS height transition. Multiple panels can be open simultaneously unless
 * data-ws-single="true" is set, in which case opening a panel closes any other.
 *
 * Validation awareness:
 *   When data-ws-invalid-class is set, the component scans each panel for
 *   elements with that class after every Livewire morph and highlights the
 *   corresponding trigger button via the ws-accordion-button-invalid class.
 *
 * External control:
 *   When data-ws-events="true", the component listens for ws-accordion custom
 *   events on its root element. The magic helper ($wirestrap.accordion) dispatches
 *   these events by element id.
 *
 * Data attributes read from the root element:
 *   data-ws-single        - "true" to allow only one open panel at a time.
 *   data-ws-events        - "true" to enable external event control.
 *   data-ws-invalid-class - CSS class name indicating a validation error inside a panel.
 *
 * Panel elements carry data-ws-accordion-panel="{key}" and data-ws-open="true"
 * for panels that should be open on mount.
 */
Alpine.data('wsAccordion', () => ({
    /**
     * Binding
     */
    accordionTrigger: {
        [':aria-expanded']() {
            const key = this.$el.getAttribute('data-ws-accordion-item');
            return this.isOpen(key);
        },
        ['x-bind:class']() {
            const key = this.$el.getAttribute('data-ws-accordion-item');
            return {
                show: this.isOpen(key),
                'ws-accordion-button-invalid': this.invalidItems.includes(key),
            };
        },
        ['x-on:click']() {
            this.toggle(this.$el.getAttribute('data-ws-accordion-item'));
        },
    },

    /**
     * State
     */
    openItems: [],
    invalidItems: [],

    /**
     * Config
     */
    _single: false,
    _invalidClass: null,

    /**
     * Cleanup
     */
    _cleanup: null,
    _invalidCleanup: null,

    /**
     * Lifecycle
     */
    init() {
        this._single = this.$root.getAttribute('data-ws-single') === 'true';

        // Initialize open items from DOM markers set by Blade.
        this.$root.querySelectorAll('[data-ws-accordion-panel][data-ws-open="true"]').forEach((el) => {
            if (this._single && this.openItems.length > 0) {
                return;
            }
            const key = el.getAttribute('data-ws-accordion-panel');
            if (key) {
                this.openItems.push(key);
            }
        });

        this._invalidClass = this.$root.getAttribute('data-ws-invalid-class') || null;
        if (this._invalidClass && this.$wire) {
            this._invalidCleanup = this.$wire.$hook('morphed', () => this._scanInvalid());
        }

        if (this.$root.getAttribute('data-ws-events') === 'true') {
            const onEvent = (e) => {
                const { action, key } = e.detail || {};
                if (action === 'show') this.show(key);
                else if (action === 'hide') this.hide(key);
                else if (action === 'toggle') this.toggle(key);
            };
            this.$root.addEventListener('ws-accordion', onEvent);
            this._cleanup = () => this.$root.removeEventListener('ws-accordion', onEvent);
        }
    },

    destroy() {
        this._cleanup?.();
        this._invalidCleanup?.();
    },

    /**
     * Accordion
     */
    isOpen(key) {
        return this.openItems.includes(key);
    },

    show(key) {
        if (this.isOpen(key)) {
            return;
        }

        const panel = this.$root.querySelector(`[data-ws-accordion-panel="${key}"]`);
        if (!panel) {
            return;
        }

        if (this._single) {
            const toClose = [...this.openItems];
            toClose.forEach((openKey) => {
                const openPanel = this.$root.querySelector(`[data-ws-accordion-panel="${openKey}"]`);
                if (openPanel) {
                    this._close(openKey, openPanel);
                }
            });
        }

        this._open(key, panel);
    },

    hide(key) {
        if (!this.isOpen(key)) {
            return;
        }

        const panel = this.$root.querySelector(`[data-ws-accordion-panel="${key}"]`);
        if (!panel) {
            return;
        }

        this._close(key, panel);
    },

    toggle(key) {
        if (this.isOpen(key)) {
            this.hide(key);
        } else {
            this.show(key);
        }
    },

    _open(key, panel) {
        this.openItems = [...this.openItems, key];

        _cancelPanelAnimation(panel);

        panel.style.display = 'block';
        // Notify components inside the panel (e.g. wsTextarea) that they are now visible
        // so they can perform deferred initialization (resize, measure, etc.).
        panel.dispatchEvent(new CustomEvent('ws-show'));
        panel.style.height = '0';
        panel.style.overflow = 'hidden';
        panel.style.opacity = '0';

        const cancel = animateHeight(
            panel,
            panel.scrollHeight,
            'height var(--ws-accordion-transition-duration, 200ms) ease, opacity var(--ws-accordion-transition-duration, 200ms) ease',
            () => {
                panel.style.transition = '';
                panel.style.height = '';
                panel.style.overflow = '';
                panel.style.opacity = '';
                _panelAnimations.delete(panel);
            },
        );
        _panelAnimations.set(panel, cancel);
    },

    _close(key, panel) {
        this.openItems = this.openItems.filter((k) => k !== key);

        _cancelPanelAnimation(panel);

        // Pin height to a fixed pixel value before the rAF so the transition has
        // a concrete start point.
        panel.style.height = `${panel.scrollHeight}px`;
        panel.style.overflow = 'hidden';

        const cancel = animateHeight(
            panel,
            0,
            'height var(--ws-accordion-transition-duration, 200ms) ease, opacity var(--ws-accordion-transition-duration, 200ms) ease',
            () => {
                panel.style.display = 'none';
                panel.style.transition = '';
                panel.style.height = '';
                panel.style.overflow = '';
                panel.style.opacity = '';
                _panelAnimations.delete(panel);
            },
        );
        _panelAnimations.set(panel, cancel);
    },

    /**
     * Utils
     */
    _scanInvalid() {
        const panels = this.$root.querySelectorAll('[data-ws-accordion-panel]');

        this.invalidItems = Array.from(panels)
            .filter((panel) => panel.querySelector('.' + this._invalidClass))
            .map((panel) => panel.dataset.wsAccordionPanel);
    },
}));
