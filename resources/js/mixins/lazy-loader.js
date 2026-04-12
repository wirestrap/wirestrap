/**
 * Tracks whether a one-shot async load has completed, preventing redundant calls.
 *
 * Usage:
 *   - Call lazyLoad(fn) instead of fn() directly: fn is only invoked if not yet loaded.
 *   - Call lazyReset() to invalidate the loaded state (e.g. when a dependency changes).
 *   - Set this._methodLoaded = true inside the load function's callback once data is ready.
 */
export function lazyLoader() {
    return {
        _methodLoaded: false,
        _methodLoading: false,

        lazyLoad(fn) {
            if (this._methodLoaded || this._methodLoading) {
                return;
            }

            this._methodLoading = true;
            fn();
        },

        lazyReset() {
            this._methodLoaded = false;
            this._methodLoading = false;
        },
    };
}
