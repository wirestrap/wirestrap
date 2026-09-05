/**
 * Alpine $wirestrap magic helper.
 *
 * Registered as Alpine.magic('wirestrap'), it exposes a structured API
 * for imperatively controlling WireStrap components from within Alpine
 * expressions or Livewire component markup:
 *
 *   $wirestrap.modal.show('myModalId')
 *   $wirestrap.toast({ message: 'Saved!' })
 *   $wirestrap.alert.confirm('Are you sure?', 'deleteRecord', id)
 *
 * Floating elements (flyout, popover, tooltip) share the same show/hide/toggle
 * interface and are dispatched through the ws-floating custom event.
 */
import './components/ui/toast';
import './components/ui/alert';

// Dispatches a ws-floating event to a floating element by its DOM id.
const _floatingDispatch = (id, action) => {
    document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-floating', { bubbles: true, detail: { action } }));
};

Alpine.magic('wirestrap', (el) => ({
    autocomplete: {
        refresh: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws:refresh', { bubbles: true })),
    },

    accordion: {
        show: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-accordion', { detail: { action: 'show', key } })),
        hide: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-accordion', { detail: { action: 'hide', key } })),
        toggle: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-accordion', { detail: { action: 'toggle', key } })),
    },

    alert: {
        show: Wirestrap.alert.show,

        /**
         * Shorthand confirm: accepts either a plain message string or a full options object.
         * Automatically resolves Livewire component so callers don't have to pass a $wire reference manually.
         *
         * @param {string|object} optionsOrMessage
         * @param {string} [method]   Livewire method to call on confirm (string shorthand only).
         * @param {...*}   [params]   Arguments forwarded to the Livewire method.
         */
        confirm: (optionsOrMessage, method, ...params) => {
            const wire = Alpine.evaluate(el, '$wire');
            const options =
                typeof optionsOrMessage === 'string'
                    ? { message: optionsOrMessage, wire, method, params }
                    : { ...optionsOrMessage, wire };
            Wirestrap.alert.confirm.show(options);
        },
    },

    flyout: {
        show: (id) => _floatingDispatch(id, 'show'),
        hide: (id) => _floatingDispatch(id, 'hide'),
        toggle: (id) => _floatingDispatch(id, 'toggle'),
    },

    menu: {
        show: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-menu', { detail: { action: 'show', key } })),
        hide: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-menu', { detail: { action: 'hide', key } })),
        toggle: (id, key) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-menu', { detail: { action: 'toggle', key } })),
    },

    modal: {
        show: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-modal', { detail: { action: 'show' } })),
        hide: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-modal', { detail: { action: 'hide' } })),
    },

    popover: {
        show: (id) => _floatingDispatch(id, 'show'),
        hide: (id) => _floatingDispatch(id, 'hide'),
        toggle: (id) => _floatingDispatch(id, 'toggle'),
    },

    select: {
        refresh: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws:refresh', { bubbles: true })),
    },

    slider: {
        prev: (id, step = null) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-slider', { detail: { action: 'prev', step } })),
        next: (id, step = null) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-slider', { detail: { action: 'next', step } })),
        goTo: (id, index) =>
            document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-slider', { detail: { action: 'goTo', index } })),
        first: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-slider', { detail: { action: 'first' } })),
        last: (id) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-slider', { detail: { action: 'last' } })),
    },

    tabs: {
        show: (id, key) => document.getElementById(id)?.dispatchEvent(new CustomEvent('ws-tabs', { detail: key })),
    },

    toast: Wirestrap.toast.add,

    tooltip: {
        show: (id) => _floatingDispatch(id, 'show'),
        hide: (id) => _floatingDispatch(id, 'hide'),
        toggle: (id) => _floatingDispatch(id, 'toggle'),
    },
}));
