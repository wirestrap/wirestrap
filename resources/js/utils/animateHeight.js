import { afterTransition } from './transition';

/**
 * Animates an element's height and opacity to a target state.
 *
 * For open  (targetPx > 0): height 0 → targetPx, opacity 0 → 1.
 * For close (targetPx === 0): height pinned → 0, opacity 1 → 0.
 *
 * The caller must set up the initial state before calling:
 *   Open:  display: block, height: 0, overflow: hidden, opacity: 0
 *   Close: height: scrollHeight, overflow: hidden
 *
 * @param {HTMLElement} el
 * @param {number} targetPx - Target height in px.
 * @param {string} cssTransition - Full CSS transition string.
 * @param {Function} onDone - Called after the transition completes. Caller handles cleanup.
 * @returns {Function} Cancel function — prevents onDone from firing and aborts the rAF if not yet run.
 */
export function animateHeight(el, targetPx, cssTransition, onDone) {
    let cancelTransition = null;
    let innerRafId = null;

    // Double rAF: ensures the initial state is painted before adding the visible classes
    const outerRafId = requestAnimationFrame(() => {
        innerRafId = requestAnimationFrame(() => {
            el.style.transition = cssTransition;
            el.style.height = `${targetPx}px`;
            el.style.opacity = targetPx > 0 ? '1' : '0';

            cancelTransition = afterTransition(el, onDone, { property: 'height' });
        });
    });

    return () => {
        cancelAnimationFrame(outerRafId);
        cancelAnimationFrame(innerRafId);
        cancelTransition?.();
    };
}
