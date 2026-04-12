/**
 * Alpine component for the WireStrap navigation menu.
 *
 * Functionally similar to the accordion, but designed for nested navigation menus
 * where collapsible sub-sections can be nested inside each other.
 *
 * Key difference from accordion:
 *   When a nested sub-menu is opened while its parent is still collapsed, the
 *   parent panel is expanded instantly (no animation) so its scrollHeight is correct
 *   before the parent's own animation starts. This avoids clipped animations when
 *   cascading through multiple levels.
 *
 * When closing a parent, all visible children are hidden immediately (no animation)
 * and removed from openItems so the parent animates to height 0 cleanly.
 *
 * External control:
 *   When data-ws-events="true", the component listens for ws-menu custom events.
 *   The magic helper ($wirestrap.menu) dispatches these by element id.
 *
 * Data attributes read from the root element:
 *   data-ws-single - "true" to allow only one open item per nesting level.
 *   data-ws-events - "true" to enable external event control.
 *
 * Accordion triggers carry data-ws-menu-accordion="{key}"; panels carry
 * data-ws-menu-accordion-panel="{key}" and data-ws-open="true" for initially open ones.
 */

import { animateHeight } from '../../utils/animateHeight';

// Cancel functions keyed by panel element, allowing in-progress animations to be
// aborted before a new one starts on the same panel.
const _panelCancels = new WeakMap();

function _cancelPanelAnimation(panel) {
    const cancel = _panelCancels.get(panel);
    if (cancel) {
        cancel();
        _panelCancels.delete(panel);
    }
}

Alpine.data('wsMenu', () => ({
    /**
     * Binding
     */
    menuAccordionTrigger: {
        ['x-on:click']() {
            this.toggle(this.$el.getAttribute('data-ws-menu-accordion'));
        },
        [':class']() {
            return { open: this.isOpen(this.$el.getAttribute('data-ws-menu-accordion')) };
        },
        [':aria-expanded']() {
            return this.isOpen(this.$el.getAttribute('data-ws-menu-accordion')).toString();
        },
    },

    /**
     * State
     */
    openItems: [],

    /**
     * Config
     */
    _single: false,

    /**
     * Cleanup
     */
    _cleanup: null,

    /**
     * Lifecycle
     */
    init() {
        this._single = this.$root.getAttribute('data-ws-single') === 'true';

        this.$root.querySelectorAll('[data-ws-menu-accordion-panel][data-ws-open="true"]').forEach((el) => {
            if (this._single && this.openItems.length > 0) {
                return;
            }
            const key = el.getAttribute('data-ws-menu-accordion-panel');
            if (key) {
                this.openItems.push(key);
            }
        });

        if (this.$root.getAttribute('data-ws-events') === 'true') {
            const onEvent = (e) => {
                const { action, key } = e.detail || {};
                if (action === 'show') this.show(key);
                else if (action === 'hide') this.hide(key);
                else if (action === 'toggle') this.toggle(key);
            };
            this.$root.addEventListener('ws-menu', onEvent);
            this._cleanup = () => this.$root.removeEventListener('ws-menu', onEvent);
        }
    },

    destroy() {
        this._cleanup?.();
    },

    /**
     * Menu
     */
    isOpen(key) {
        return this.openItems.includes(key);
    },

    toggle(key) {
        this.isOpen(key) ? this.hide(key) : this.show(key);
    },

    show(key) {
        if (this.isOpen(key)) {
            return;
        }

        const panel = this.$root.querySelector(`[data-ws-menu-accordion-panel="${key}"]`);
        if (!panel) {
            return;
        }

        const trigger = this.$root.querySelector(`[data-ws-menu-accordion="${key}"]`);
        const parentPanel = trigger ? trigger.closest('[data-ws-menu-accordion-panel]') : null;

        if (this._single) {
            [...this.openItems].forEach((k) => {
                const siblingTrigger = this.$root.querySelector(`[data-ws-menu-accordion="${k}"]`);
                const siblingParentPanel = siblingTrigger ? siblingTrigger.closest('[data-ws-menu-accordion-panel]') : null;
                if (siblingParentPanel === parentPanel) {
                    this.hide(k);
                }
            });
        }

        this.openItems = [...this.openItems, key];

        const parentKey = parentPanel ? parentPanel.getAttribute('data-ws-menu-accordion-panel') : null;
        if (parentKey && !this.isOpen(parentKey)) {
            // Parent is collapsed: reveal this panel instantly so the parent can animate
            // to the correct full scrollHeight (which must include this panel's content).
            _cancelPanelAnimation(panel);
            panel.style.display = 'block';
            panel.style.height = '';
            panel.style.overflow = '';
            panel.style.opacity = '';
            panel.style.transition = '';
            this.show(parentKey);
        } else {
            this._open(panel);
        }
    },

    hide(key) {
        if (!this.isOpen(key)) {
            return;
        }

        const panel = this.$root.querySelector(`[data-ws-menu-accordion-panel="${key}"]`);
        if (!panel) {
            return;
        }

        const removedChildren = this.openItems.filter((k) => {
            if (k === key) return false;
            const child = this.$root.querySelector(`[data-ws-menu-accordion-panel="${k}"]`);
            return child && panel.contains(child);
        });

        this.openItems = this.openItems.filter((k) => {
            if (k === key) return false;
            const child = this.$root.querySelector(`[data-ws-menu-accordion-panel="${k}"]`);
            return !child || !panel.contains(child);
        });

        // Hide open children immediately (no animation) before the parent closes.
        // If a child were allowed to animate independently, the parent's scrollHeight
        // would shrink before the child finished, causing the parent animation to clip.
        removedChildren.forEach((k) => {
            const childPanel = this.$root.querySelector(`[data-ws-menu-accordion-panel="${k}"]`);
            if (childPanel) {
                _cancelPanelAnimation(childPanel);
                childPanel.style.display = 'none';
                childPanel.style.height = '';
                childPanel.style.overflow = '';
                childPanel.style.opacity = '';
                childPanel.style.transition = '';
            }
        });

        this._close(panel);
    },

    _open(panel) {
        _cancelPanelAnimation(panel);

        panel.style.display = 'block';
        panel.style.height = '0';
        panel.style.overflow = 'hidden';
        panel.style.opacity = '0';

        const cancel = animateHeight(
            panel,
            panel.scrollHeight,
            'height var(--ws-menu-transition-duration, 200ms) ease, opacity var(--ws-menu-transition-duration, 200ms) ease',
            () => {
                panel.style.transition = '';
                panel.style.height = '';
                panel.style.overflow = '';
                panel.style.opacity = '';
                _panelCancels.delete(panel);
            },
        );
        _panelCancels.set(panel, cancel);
    },

    _close(panel) {
        _cancelPanelAnimation(panel);

        panel.style.height = `${panel.scrollHeight}px`;
        panel.style.overflow = 'hidden';

        const cancel = animateHeight(
            panel,
            0,
            'height var(--ws-menu-transition-duration, 200ms) ease, opacity var(--ws-menu-transition-duration, 200ms) ease',
            () => {
                panel.style.display = 'none';
                panel.style.transition = '';
                panel.style.height = '';
                panel.style.overflow = '';
                panel.style.opacity = '';
                _panelCancels.delete(panel);
            },
        );
        _panelCancels.set(panel, cancel);
    },
}));
