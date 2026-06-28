/**
 * Provides drag-to-move behavior for a target element within a bounding container.
 *
 * Contract: the consuming component must call draggableInit(handle, target, container)
 * and draggableDestroy() in destroy(). Optionally pass positionRef as the 4th argument
 * to use a different element for measuring the initial visual position (useful when target
 * is a flex container whose bounding rect differs from its visible content).
 *
 * draggableReset() restores target to its original non-absolute position.
 */
export function draggable() {
    return {
        _draggableTarget: null,
        _draggableCleanup: null,
        _draggablePositionInitialized: false,
        _draggingClass: null,

        draggableInit(handle, target, container, positionRef = null, draggingClass = null, onDragStartCallback = null) {
            this._draggableTarget = target;
            this._draggingClass = draggingClass;

            let isDragging = false;
            let startX = 0,
                startY = 0;
            let targetStartLeft = 0,
                targetStartTop = 0;

            const getCoords = (e) =>
                e.touches ? { x: e.touches[0].clientX, y: e.touches[0].clientY } : { x: e.clientX, y: e.clientY };

            const initAbsolutePosition = () => {
                if (this._draggablePositionInitialized) {
                    return;
                }

                const ref = (positionRef ?? target).getBoundingClientRect();

                target.style.margin = '0';
                target.style.position = 'absolute';
                target.style.width = `${ref.width}px`;
                // Reset flex properties that would otherwise fight the absolute positioning:
                // min-height may be set by the flex parent, and align-items: center would
                // vertically offset the content when the absolute height is unconstrained.
                target.style.minHeight = 'auto';
                target.style.alignItems = 'flex-start';
                target.style.left = `${ref.left}px`;
                target.style.top = `${ref.top}px`;
                this._draggablePositionInitialized = true;
            };

            const onDragStart = (e) => {
                // Let clicks on interactive elements inside the handle (e.g. close button,
                // a link) pass through normally instead of starting a drag.
                if (e.target.closest('button') || e.target.closest('a')) {
                    return;
                }

                initAbsolutePosition();
                isDragging = true;
                this._draggingClass && handle.classList.add(this._draggingClass);
                document.body.classList.add('ws-dragging');

                const coords = getCoords(e);
                startX = coords.x;
                startY = coords.y;
                targetStartLeft = parseFloat(target.style.left) || 0;
                targetStartTop = parseFloat(target.style.top) || 0;

                onDragStartCallback?.();

                document.addEventListener('mousemove', onDragMove);
                document.addEventListener('mouseup', onDragEnd);
                document.addEventListener('touchmove', onDragMove, { passive: false });
                document.addEventListener('touchend', onDragEnd);
            };

            const onDragMove = (e) => {
                if (!isDragging) {
                    return;
                }
                if (e.cancelable) {
                    e.preventDefault();
                }

                const coords = getCoords(e);
                const dx = coords.x - startX;
                const dy = coords.y - startY;

                const newLeft = Math.max(0, Math.min(targetStartLeft + dx, container.clientWidth - target.offsetWidth));
                const newTop = Math.max(0, Math.min(targetStartTop + dy, container.clientHeight - target.offsetHeight));

                target.style.left = `${newLeft}px`;
                target.style.top = `${newTop}px`;
            };

            const onDragEnd = () => {
                isDragging = false;
                this._draggingClass && handle.classList.remove(this._draggingClass);
                document.body.classList.remove('ws-dragging');

                document.removeEventListener('mousemove', onDragMove);
                document.removeEventListener('mouseup', onDragEnd);
                document.removeEventListener('touchmove', onDragMove);
                document.removeEventListener('touchend', onDragEnd);
            };

            const onResize = () => {
                if (!this._draggablePositionInitialized) {
                    return;
                }

                const currentLeft = parseFloat(target.style.left) || 0;
                const currentTop = parseFloat(target.style.top) || 0;

                target.style.left = `${Math.max(0, Math.min(currentLeft, container.clientWidth - target.offsetWidth))}px`;
                target.style.top = `${Math.max(0, Math.min(currentTop, container.clientHeight - target.offsetHeight))}px`;
            };

            handle.addEventListener('mousedown', onDragStart);
            handle.addEventListener('touchstart', onDragStart, { passive: true });
            window.addEventListener('resize', onResize);

            this._draggableCleanup = () => {
                handle.removeEventListener('mousedown', onDragStart);
                handle.removeEventListener('touchstart', onDragStart);
                document.removeEventListener('mousemove', onDragMove);
                document.removeEventListener('mouseup', onDragEnd);
                document.removeEventListener('touchmove', onDragMove);
                document.removeEventListener('touchend', onDragEnd);
                window.removeEventListener('resize', onResize);
            };
        },

        draggableReset() {
            if (!this._draggableTarget) {
                return;
            }

            this._draggablePositionInitialized = false;
            this._draggableTarget.style.removeProperty('margin');
            this._draggableTarget.style.removeProperty('position');
            this._draggableTarget.style.removeProperty('width');
            this._draggableTarget.style.removeProperty('min-height');
            this._draggableTarget.style.removeProperty('align-items');
            this._draggableTarget.style.removeProperty('left');
            this._draggableTarget.style.removeProperty('top');
        },

        draggableDestroy() {
            this._draggableCleanup?.();
        },
    };
}
