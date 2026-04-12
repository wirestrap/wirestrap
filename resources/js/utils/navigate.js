/**
 * Livewire navigate lifecycle cleanup registry.
 *
 * Components that hold state across page navigations (open floating elements,
 * active toasts, etc.) register a teardown callback via onNavigate(). All
 * callbacks are flushed when the livewire:navigating event fires (see wirestrap.js).
 */

const _cleanups = [];

/**
 * Registers a callback to run before Livewire navigates away from the page.
 *
 * @param {Function} fn
 */
export function onNavigate(fn) {
    _cleanups.push(fn);
}

/** @internal Called by wirestrap.js on the livewire:navigating event. */
export function _runNavigateCleanups() {
    _cleanups.forEach((fn) => fn());
}
