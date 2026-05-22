/**
 * Provides multi-directional resize behavior via edge/corner handles.
 *
 * Each handle must have a data-ws-resize attribute: 'e', 'w', 's', 'se', 'sw'.
 * Left/right resize requires absolute positioning to anchor the opposite edge.
 * The mixin initializes it automatically and tracks whether it did so for cleanup.
 *
 * Contract: call resizableInit(handles, target, heightTarget, container) and
 * resizableDestroy() in destroy(). resizableReset() restores the original state.
 */
export function resizable() {
    return {
        _resizableTarget: null,
        _resizableHeightTarget: null,
        _resizableAbsoluteInit: false,
        _resizableCleanup: [],

        resizableInit(handles, target, heightTarget, container, minWidth = 300, minHeightFn = () => 0) {
            this._resizableTarget = target;
            this._resizableHeightTarget = heightTarget;

            const getCoords = (e) =>
                e.touches ? { x: e.touches[0].clientX, y: e.touches[0].clientY } : { x: e.clientX, y: e.clientY };

            const initAbsoluteIfNeeded = () => {
                if (target.style.position === 'absolute') {
                    return;
                }

                // Left/right resize must anchor the opposite edge, which requires explicit
                // left + width values. We switch to absolute positioning to gain full control,
                // snapping target to its current visual position before any drag starts.
                const contentRect = heightTarget.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();

                target.style.position = 'absolute';
                target.style.margin = '0';
                target.style.maxWidth = 'none';
                // Same flex overrides as the draggable mixin - unconstrain height so the
                // absolute box doesn't collapse or get offset by the flex container.
                target.style.minHeight = 'auto';
                target.style.alignItems = 'flex-start';
                target.style.width = `${contentRect.width}px`;
                target.style.left = `${contentRect.left - containerRect.left}px`;
                target.style.top = `${contentRect.top - containerRect.top}px`;
                // Remove CSS max-height on the content so the user can freely resize taller.
                heightTarget.style.maxHeight = 'none';

                this._resizableAbsoluteInit = true;
            };

            let cancelActiveResize = null;

            handles.forEach((handle) => {
                const direction = handle.dataset.wsResize || 'se';
                const resizesLeft = direction === 'w' || direction === 'sw';
                const resizesRight = direction === 'e' || direction === 'se';
                const resizesBottom = direction === 's' || direction === 'se' || direction === 'sw';

                const onResizeStart = (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    target.style.transition = 'none';
                    heightTarget.style.transition = 'none';

                    if (resizesLeft || resizesRight || resizesBottom) {
                        initAbsoluteIfNeeded();
                    }

                    const coords = getCoords(e);
                    const startX = coords.x;
                    const startY = coords.y;
                    const startWidth = target.offsetWidth;
                    const startHeight = heightTarget.offsetHeight;
                    const startLeft = parseFloat(target.style.left) || 0;
                    const startRight = startLeft + startWidth;
                    // Respect a CSS-defined min-width (e.g. from the component's SCSS variables)
                    // over the JS fallback so theming constraints are honoured at runtime.
                    const effectiveMinWidth = parseFloat(getComputedStyle(target).minWidth?.match(/[\d.]+px/)?.[0]) || minWidth;

                    const onResizeMove = (e) => {
                        if (e.cancelable) {
                            e.preventDefault();
                        }

                        const coords = getCoords(e);

                        if (resizesRight) {
                            const maxWidth = container.clientWidth - (parseFloat(target.style.left) || 0);
                            const newWidth = Math.min(maxWidth, Math.max(effectiveMinWidth, startWidth + (coords.x - startX)));
                            target.style.width = `${newWidth}px`;
                        }

                        if (resizesLeft) {
                            const newWidth = Math.max(effectiveMinWidth, startRight - coords.x);
                            const newLeft = Math.max(0, startRight - newWidth);
                            target.style.width = `${newWidth}px`;
                            target.style.left = `${newLeft}px`;
                        }

                        if (resizesBottom) {
                            const maxHeight = container.clientHeight - (parseFloat(target.style.top) || 0);
                            const newHeight = Math.min(maxHeight, Math.max(minHeightFn(), startHeight + (coords.y - startY)));
                            heightTarget.style.height = `${newHeight}px`;
                        }
                    };

                    const onResizeEnd = () => {
                        target.style.removeProperty('transition');
                        heightTarget.style.removeProperty('transition');
                        cancelActiveResize?.();
                        cancelActiveResize = null;
                    };

                    cancelActiveResize = () => {
                        document.removeEventListener('mousemove', onResizeMove);
                        document.removeEventListener('mouseup', onResizeEnd);
                        document.removeEventListener('touchmove', onResizeMove);
                        document.removeEventListener('touchend', onResizeEnd);
                    };

                    document.addEventListener('mousemove', onResizeMove);
                    document.addEventListener('mouseup', onResizeEnd);
                    document.addEventListener('touchmove', onResizeMove, { passive: false });
                    document.addEventListener('touchend', onResizeEnd);
                };

                handle.addEventListener('mousedown', onResizeStart);
                handle.addEventListener('touchstart', onResizeStart, { passive: false });

                this._resizableCleanup.push(() => {
                    cancelActiveResize?.();
                    cancelActiveResize = null;
                    handle.removeEventListener('mousedown', onResizeStart);
                    handle.removeEventListener('touchstart', onResizeStart);
                });
            });
        },

        resizableReset() {
            this._resizableTarget?.style.removeProperty('width');
            this._resizableHeightTarget?.style.removeProperty('height');

            if (this._resizableAbsoluteInit) {
                ['position', 'left', 'top', 'margin', 'max-width', 'min-height', 'align-items'].forEach((prop) => {
                    this._resizableTarget?.style.removeProperty(prop);
                });
                this._resizableHeightTarget?.style.removeProperty('max-height');
                this._resizableAbsoluteInit = false;
            }
        },

        resizableDestroy() {
            this._resizableCleanup.forEach((fn) => fn());
            this._resizableCleanup = [];
        },
    };
}
