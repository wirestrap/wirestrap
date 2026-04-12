/**
 * Runs a callback after an element's CSS transition completes.
 *
 * Uses the transitionend event as the primary signal, with a fallback timeout
 * as a safety net for cases where the event never fires (no transition defined,
 * element removed from DOM, transition cancelled, etc.).
 *
 * The bubbling issue is handled by filtering on e.target === el.
 * When multiple properties transition simultaneously, the callback fires on the
 * first matching transitionend event - use the `property` option to target a
 * specific CSS property if needed.
 *
 * @param {HTMLElement} el
 * @param {Function} callback
 * @param {object} [options]
 * @param {string|null} [options.property] - Only react to transitionend for this CSS property name.
 * @param {number|null} [options.fallback=null] - Fallback timeout in ms if transitionend never fires.
 *   Defaults to the element's computed transition-duration, so elements without a transition
 *   get an immediate cleanup (next tick) rather than a fixed delay.
 * @returns {Function} Cancel function - call it to prevent the callback from firing.
 */
export function afterTransition(el, callback, { property = null, fallback = null } = {}) {
    const resolvedFallback =
        fallback !== null
            ? fallback
            : Math.max(
                  0,
                  ...getComputedStyle(el)
                      .transitionDuration.split(',')
                      .map((s) => parseFloat(s) * 1000),
              );
    let done = false;

    const finish = () => {
        if (done) {
            return;
        }
        done = true;
        el.removeEventListener('transitionend', handler);
        clearTimeout(timeoutId);
        callback();
    };

    const handler = (e) => {
        if (e.target !== el) {
            return;
        }
        if (property && e.propertyName !== property) {
            return;
        }
        finish();
    };

    el.addEventListener('transitionend', handler);
    const timeoutId = setTimeout(finish, resolvedFallback);

    return () => {
        done = true;
        el.removeEventListener('transitionend', handler);
        clearTimeout(timeoutId);
    };
}
