/**
 * Module-level floating element manager for tooltips, popovers, and flyouts.
 *
 * Unlike the floatingElement mixin (which is bound to an Alpine component instance),
 * this module uses pure DOM event delegation and manages all active entries in a
 * shared _active array.
 *
 * Concepts:
 *   container  - the element carrying data-ws-float-id, which holds configuration
 *                and acts as the scope boundary for pointer/focus events.
 *   floatable  - the panel that is shown/hidden, found by data-ws-floatable.
 *   anchor     - the element used as position reference for @floating-ui/dom.
 *                Defaults to the trigger; an explicit data-ws-anchor can override it.
 *   trigger    - the element carrying data-ws-float-trigger, where click/hover starts.
 *
 * Teleport support:
 *   When `append` is used (Blade component option), the floatable lives outside the
 *   container in the DOM. It carries data-ws-float-for="{containerId}" to link back.
 *   _containerFor() resolves the owning container for any element, handling both cases.
 *
 * Triggers:
 *   "hover" - show on mouseover/focusin, hide on mouseout/focusout with a 50 ms delay.
 *   "click" - toggle on click, hide on outside click.
 *
 * Livewire integration:
 *   After each morph, _schedulePrune() removes entries whose DOM elements are gone.
 *   On Livewire navigate, all active entries are torn down synchronously.
 *
 * Global configuration (Wirestrap.floating.configure):
 *   keepShownFor - array of CSS selectors whose matching elements are treated as in-scope.
 *                  Merged with the built-in defaults (.ws-select-dropdown, .ws-autocomplete-dropdown).
 *                  Useful for third-party widgets (datepickers, selects…) that teleport
 *                  their panel outside the container, to prevent unwanted close on interaction.
 *
 * Data attributes read from the container element:
 *   data-ws-float-id        - unique identifier for the container.
 *   data-ws-placement       - @floating-ui/dom placement string (default: "top").
 *   data-ws-trigger         - "hover" or "click" (default: "hover").
 *   data-ws-offset-skidding - cross-axis offset in px.
 *   data-ws-offset-distance - main-axis offset in px.
 *   data-ws-position        - @floating-ui/dom strategy ("absolute" or "fixed").
 */
import { computePosition, autoUpdate, offset, flip, shift, arrow as arrowMiddleware } from '@floating-ui/dom';
import { afterTransition } from '../../utils/transition';
import { onNavigate } from '../../utils/navigate';

/*
|--------------------------------------------------------------------------
|   State
|--------------------------------------------------------------------------
*/

const _active = []; // Currently visible entries: { container, floatable, cleanup }
const _hoverTimeouts = new WeakMap(); // Pending hide timeouts for hover-triggered elements (keyed by container)
const _transitionTimeouts = new WeakMap(); // Pending afterTransition cancellers for hide animations (keyed by floatable)
const _floatableToContainer = new WeakMap(); // Reverse lookup: floatable → container, needed for teleported floatables
let _pruneScheduled = false;

/*
|--------------------------------------------------------------------------
|   Global config
|--------------------------------------------------------------------------
*/

const _config = {
    // CSS selectors — elements matching these are treated as in-scope (e.g. third-party dropdowns teleported to body).
    // WireStrap's own select and autocomplete dropdowns are included by default so that a flyout containing
    // one of these components does not close when the user interacts with the dropdown panel.
    keepShownFor: ['.ws-select-dropdown', '.ws-autocomplete-dropdown', '.ws-autocomplete-option'],
};

/*
|--------------------------------------------------------------------------
|   Config
|--------------------------------------------------------------------------
*/

function _readConfig(container) {
    const ds = container.dataset;

    return {
        placement: ds.wsPlacement || 'top',
        trigger: ds.wsTrigger || 'hover',
        offsetSkidding: parseInt(ds.wsOffsetSkidding || 0, 10),
        offsetDistance: parseInt(ds.wsOffsetDistance || 0, 10),
        strategy: ds.wsPosition || 'absolute',
    };
}

/*
|--------------------------------------------------------------------------
|   Core
|--------------------------------------------------------------------------
*/

function show(triggerEl) {
    const container = triggerEl.closest('[data-ws-float-id]');
    if (!container || _findEntry(container)) {
        return;
    }

    const floatable = _findFloatable(container);
    if (!floatable) {
        return;
    }

    _transitionTimeouts.get(floatable)?.();
    _transitionTimeouts.delete(floatable);

    const config = _readConfig(container);
    const arrowEl = floatable.querySelector('[data-ws-arrow]');
    const anchors = Array.from(container.querySelectorAll('[data-ws-anchor]'));
    const anchor = anchors.find((a) => !floatable.contains(a) && a.closest('[data-ws-float-id]') === container) ?? triggerEl;

    const middleware = [
        offset({ crossAxis: config.offsetSkidding, mainAxis: config.offsetDistance }),
        flip({ fallbackPlacements: ['top', 'bottom', 'right', 'left'] }),
        shift(),
    ];

    if (arrowEl) {
        middleware.push(arrowMiddleware({ element: arrowEl }));
    }

    const floatingConfig = {
        placement: config.placement,
        strategy: config.strategy,
        middleware,
    };

    floatable.style.position = config.strategy;
    floatable.style.top = '0';
    floatable.style.left = '0';
    floatable.style.removeProperty('display');
    floatable.offsetHeight; // force reflow so the opacity transition plays on show
    floatable.classList.add('show');
    triggerEl.classList.add('open');

    const cleanup = autoUpdate(anchor, floatable, () => _update(anchor, floatable, floatingConfig, arrowEl));
    _active.push({ container, floatable, cleanup });
    _floatableToContainer.set(floatable, container);
}

function hide(container) {
    const entry = _findEntry(container);
    if (!entry) {
        return;
    }

    entry.cleanup();
    entry.floatable.classList.remove('show');

    const triggerEl = entry.container.querySelector('[data-ws-float-trigger]');
    if (triggerEl) {
        triggerEl.classList.remove('open');
    }

    _active.splice(_active.indexOf(entry), 1);

    const cancel = afterTransition(
        entry.floatable,
        () => {
            entry.floatable.style.display = 'none';
            _transitionTimeouts.delete(entry.floatable);
        },
        { fallback: 300 },
    );
    _transitionTimeouts.set(entry.floatable, cancel);
}

/*
|--------------------------------------------------------------------------
|   Position
|--------------------------------------------------------------------------
*/

function _update(anchor, floatable, floatingConfig, arrowEl) {
    computePosition(anchor, floatable, floatingConfig).then(({ x, y, placement, middlewareData }) => {
        Object.assign(floatable.style, { left: `${x}px`, top: `${y}px` });
        floatable.dataset.floatingPlacement = placement;

        if (middlewareData.arrow && arrowEl) {
            const { x: ax, y: ay } = middlewareData.arrow;

            Object.assign(arrowEl.style, {
                position: 'absolute',
                left: ax != null ? `${ax}px` : '',
                top: ay != null ? `${ay}px` : '',
            });
        }
    });
}

/*
|--------------------------------------------------------------------------
|   Lookup
|--------------------------------------------------------------------------
*/

function _findEntry(container) {
    return _active.find((a) => a.container === container) ?? null;
}

/**
 * When `append` is used, the floatable is teleported outside the container.
 * Look inside the container first, then fall back to a document query via data-ws-float-for.
 * Exclude floatables that are inside [data-ws-float-trigger]: those belong to nested Alpine
 * components (select, autocomplete) and are not managed by this floating-manager instance.
 */
function _findFloatable(container) {
    const trigger = container.querySelector('[data-ws-float-trigger]');
    const candidates = Array.from(container.querySelectorAll('[data-ws-floatable]'));
    const direct = candidates.find((f) => f.closest('[data-ws-float-id]') === container && !(trigger && trigger.contains(f)));
    return direct ?? document.querySelector(`[data-ws-floatable][data-ws-float-for="${container.dataset.wsFloatId}"]`);
}

/**
 * Returns the delegated container for any element, handling both direct ancestry
 * and elements inside a teleported floatable (linked via data-ws-float-for).
 */
function _containerFor(el) {
    const direct = el.closest('[data-ws-float-id]');
    if (direct) {
        return direct;
    }

    const floatable = el.closest('[data-ws-floatable][data-ws-float-for]');
    if (floatable) {
        return (
            _floatableToContainer.get(floatable) ?? document.querySelector(`[data-ws-float-id="${floatable.dataset.wsFloatFor}"]`)
        );
    }

    return null;
}

/**
 * Checks if el is within the container or its associated floatable (handles teleport).
 * Also returns true if el matches any selector in _config.keepShownFor — useful for
 * third-party widgets (datepickers, selects…) that teleport their panel to the body.
 */
function _inScope(container, el) {
    if (!el) {
        return false;
    }

    if (container.contains(el)) {
        return true;
    }

    const entry = _findEntry(container);
    if (entry?.floatable.contains(el)) {
        return true;
    }

    return _config.keepShownFor.some((selector) => el.closest(selector) !== null);
}

/*
|--------------------------------------------------------------------------
|   Prune: cleanup stale entries whose DOM elements are no longer connected
|--------------------------------------------------------------------------
*/

function _pruneDetached() {
    [..._active].filter((a) => !a.container.isConnected || !a.floatable.isConnected).forEach((a) => hide(a.container));
}

function _schedulePrune() {
    if (_pruneScheduled) {
        return;
    }

    _pruneScheduled = true;

    requestAnimationFrame(() => {
        _pruneDetached();
        _pruneScheduled = false;
    });
}

/*
|--------------------------------------------------------------------------
|   Hooks
|--------------------------------------------------------------------------
*/

if (window.Livewire) {
    Livewire.hook('morphed', _schedulePrune);
}

onNavigate(() => {
    [..._active].forEach((a) => {
        clearTimeout(_hoverTimeouts.get(a.container));
        a.cleanup();
    });

    _active.length = 0;
});

/*
|--------------------------------------------------------------------------
|   Listeners
|--------------------------------------------------------------------------
*/

document.addEventListener('ws-floating', (e) => {
    const container = e.target.closest('[data-ws-float-id]');
    if (!container) {
        return;
    }

    const { action } = e.detail || {};
    const trigger = container.querySelector('[data-ws-float-trigger]');

    if (action === 'show' && trigger) {
        requestAnimationFrame(() => show(trigger)); // delayed to avoid race condition with click outside event
    } else if (action === 'hide') {
        hide(container);
    } else if (action === 'toggle' && trigger) {
        _findEntry(container) ? hide(container) : requestAnimationFrame(() => show(trigger));
    }
});

function _handleEnter(e) {
    if (e.target.closest('[data-ws-floatable]')) {
        const container = _containerFor(e.target);

        if (container && container.dataset.wsTrigger === 'hover') {
            clearTimeout(_hoverTimeouts.get(container));
            _hoverTimeouts.delete(container);
        }
    }

    const trigger = e.target.closest('[data-ws-float-trigger]');
    if (!trigger) {
        return;
    }

    const container = trigger.closest('[data-ws-float-id]');
    if (!container || container.dataset.wsTrigger !== 'hover') {
        return;
    }

    clearTimeout(_hoverTimeouts.get(container));
    _hoverTimeouts.delete(container);
    show(trigger);
}

document.addEventListener('mouseover', _handleEnter);

document.addEventListener('mouseout', (e) => {
    _active
        .filter((a) => a.container.dataset.wsTrigger === 'hover')
        .filter((a) => _inScope(a.container, e.target))
        .filter((a) => !_inScope(a.container, e.relatedTarget))
        .forEach((a) => {
            clearTimeout(_hoverTimeouts.get(a.container));

            const timeout = setTimeout(() => {
                if (a.container.matches(':focus-within') || a.floatable.matches(':focus-within')) {
                    return;
                }

                hide(a.container);
            }, 50);

            _hoverTimeouts.set(a.container, timeout);
        });
});

document.addEventListener('focusin', _handleEnter);

document.addEventListener('focusout', (e) => {
    const container = _containerFor(e.target);
    if (!container || container.dataset.wsTrigger !== 'hover') {
        return;
    }

    const entry = _findEntry(container);
    if (!entry) {
        return;
    }

    if (_inScope(container, e.relatedTarget)) {
        return;
    }

    if (!entry.container.matches(':hover') && !entry.floatable.matches(':hover')) {
        hide(container);
    }
});

/*
|--------------------------------------------------------------------------
|   Public API
|--------------------------------------------------------------------------
*/

globalThis.Wirestrap ??= {};
globalThis.Wirestrap.floating = {
    configure(options) {
        if (options.keepShownFor !== undefined) {
            _config.keepShownFor = [..._config.keepShownFor, ...options.keepShownFor];
        }
    },
};

document.addEventListener('click', (e) => {
    [..._active].filter((a) => !_inScope(a.container, e.target)).forEach((a) => hide(a.container));

    const trigger = e.target.closest('[data-ws-float-trigger]');
    if (!trigger) {
        return;
    }

    const container = trigger.closest('[data-ws-float-id]');
    if (!container || container.dataset.wsTrigger !== 'click') {
        return;
    }

    _findEntry(container) ? hide(container) : show(trigger);
});
