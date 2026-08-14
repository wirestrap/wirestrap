/**
 * Alpine component for the WireStrap modal dialog.
 *
 * Combines the draggable and resizable mixins to support movable, resizable
 * modal windows. Multiple modals can be stacked; z-index is managed via
 * _wsModalGetTopZIndex so the last-opened modal is always on top.
 *
 * Shared singletons (module-level):
 *   _wsBackdrop   - a single backdrop element, reference-counted across all modals
 *                   that have data-ws-backdrop="true".
 *   _wsScrollLock - prevents body scrolling while a non-draggable modal is open;
 *                   reference-counted so stacked modals share a single lock.
 *
 * Fullscreen toggle (modalToggleFullscreen):
 *   Uses the FLIP technique (First/Last/Invert/Play) on the content element to
 *   animate between the natural size and fullscreen. When collapsing, the saved
 *   drag/resize state is restored so the window returns to its previous position.
 *
 * Minimize (modalMinimize):
 *   Appends a button to a shared taskbar (#minimized_modals). The content
 *   animates toward the button in parallel with the CSS fade-out. Clicking the
 *   button calls modalRestore() which re-runs modalShow() and plays the reverse
 *   animation from the button rect back to the modal content rect.
 *
 * Static mode (data-ws-static="true"):
 *   Backdrop clicks and Escape key trigger a shake animation instead of closing.
 *
 * Data attributes read from the root element:
 *   data-ws-draggable  - "true" to enable dragging via the header.
 *   data-ws-resizable  - "true" to enable resize handles (.ws-modal-resize-handle).
 *   data-ws-static     - "true" to prevent dismiss on backdrop/Escape.
 *   data-ws-backdrop   - "true" to show/hide the shared backdrop.
 *   data-ws-label      - label shown on the minimized taskbar button.
 *   data-ws-btn-class  - CSS classes for the minimized taskbar button.
 */
import { draggable } from '../../mixins/draggable';
import { resizable } from '../../mixins/resizable';
import { afterTransition } from '../../utils/transition';
import { onNavigate } from '../../utils/navigate';

/**
 * Returns the highest z-index among currently visible modals, falling back to the default (1055).
 * Optionally excludes one element from the scan (e.g. the modal being brought to front).
 */
function _wsModalGetTopZIndex(exclude = null) {
    const base = 1055;
    let max = base;
    document.querySelectorAll('.ws-modal.ws-modal-visible').forEach((el) => {
        if (el === exclude) {
            return;
        }
        const z = parseInt(el.style.zIndex);
        if (!isNaN(z) && z > max) {
            max = z;
        }
    });
    return max;
}

/**
 * Returns the visible modal with the highest z-index (the one that should respond to ESC)
 */
function _wsModalGetTopModal() {
    let top = null;
    let topZ = -1;
    document.querySelectorAll('.ws-modal.ws-modal-visible').forEach((el) => {
        const z = parseInt(el.style.zIndex) || 0;
        if (z > topZ) {
            topZ = z;
            top = el;
        }
    });
    return top;
}

/**
 * Single shared backdrop element, reference-counted across modals that request one
 */
const _wsBackdrop = {
    el: null,
    count: 0,
    _cancelHide: null,

    show() {
        this.count++;
        if (!this.el) {
            this.el = document.createElement('div');
            this.el.className = 'ws-modal-backdrop';
            document.body.appendChild(this.el);
            this.el.offsetHeight; // trigger reflow for CSS transition
            this.el.classList.add('ws-modal-visible');
        }
    },

    hide() {
        this.count = Math.max(0, this.count - 1);
        if (this.count > 0 || !this.el) {
            return;
        }

        const el = this.el;
        this.el = null;
        el.classList.remove('ws-modal-visible');
        this._cancelHide = afterTransition(el, () => {
            this._cancelHide = null;
            el.remove();
        });
    },

    forceRemove() {
        this._cancelHide?.();
        this._cancelHide = null;
        this.el?.remove();
        this.el = null;
        this.count = 0;
    },
};

/**
 * Body scroll lock, reference-counted so multiple stacked modals share a single lock
 */
const _wsScrollLock = {
    count: 0,

    lock() {
        if (this.count === 0) {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.body.dataset.wsScrollPad = document.body.style.paddingRight;
            if (scrollbarWidth > 0) {
                document.body.style.paddingRight = `${scrollbarWidth}px`;
            }
            document.body.style.overflow = 'hidden';
        }
        this.count++;
    },

    unlock() {
        this.count = Math.max(0, this.count - 1);
        if (this.count === 0) {
            document.body.style.overflow = '';
            document.body.style.paddingRight = document.body.dataset.wsScrollPad || '';
            delete document.body.dataset.wsScrollPad;
        }
    },

    forceReset() {
        this.count = 0;
        document.body.style.overflow = '';
        document.body.style.paddingRight = document.body.dataset.wsScrollPad || '';
        delete document.body.dataset.wsScrollPad;
    },
};

onNavigate(() => {
    _wsBackdrop.forceRemove();
    _wsScrollLock.forceReset();
});

Alpine.data('wsModal', () => ({
    /**
     * Mixins
     */
    ...draggable(),
    ...resizable(),

    /**
     * Binding
     */
    closeBtn: {
        ['x-on:click']() {
            this.modalHide();
        },
    },

    minimizeBtn: {
        ['x-on:click']() {
            this.modalMinimize();
        },
    },

    expandBtn: {
        ['x-on:click']() {
            this.modalToggleFullscreen();
        },
    },

    modalEl: {
        ['x-ref']: 'modalEl',
    },

    /**
     * State
     */
    _isExpanded: false,
    _savedDragState: null,
    _minimizedBtn: null,

    /**
     * Config
     */
    _isDraggable: false,
    _isStatic: false,
    _hasBackdrop: false,
    _isResizable: false,

    /**
     * Cleanup
     */
    _modalCleanup: null,
    _onKeydown: null,
    _onDismissClick: null,
    _onBackdropClick: null,
    _shakeTimeout: null,
    _cancelHideTransition: null,
    _cancelExpandTransition: null,
    _cancelMinimizeTransition: null,

    /**
     * Lifecycle
     */
    init() {
        this.$nextTick(() => {
            const modal = this.$refs.modalEl;

            this._isDraggable = this.$el.getAttribute('data-ws-draggable') === 'true';
            this._isStatic = this.$el.getAttribute('data-ws-static') === 'true';
            this._hasBackdrop = this.$el.getAttribute('data-ws-backdrop') === 'true';
            this._isResizable = this.$el.getAttribute('data-ws-resizable') === 'true';
            this._destroyOnDismiss = this.$el.getAttribute('data-ws-destroy-on-dismiss') === 'true';

            // Support data-ws-dismiss="modal" inside slot content for convenience.
            this._onDismissClick = (e) => {
                if (e.target.closest('[data-ws-dismiss="modal"]')) {
                    this.modalHide();
                }
            };

            // Backdrop click: close, or shake if static mode.
            this._onBackdropClick = (e) => {
                if (e.target !== modal) {
                    return;
                }
                if (this._isStatic) {
                    this._modalShake();
                } else {
                    this.modalHide();
                }
            };

            // ESC: only close the topmost open modal. Attached on show, detached on hide.
            this._onKeydown = (e) => {
                if (e.key !== 'Escape' || _wsModalGetTopModal() !== modal) {
                    return;
                }
                e.stopImmediatePropagation();
                if (this._isStatic) {
                    this._modalShake();
                } else {
                    this.modalHide();
                }
            };

            const dialog = modal.querySelector('.ws-modal-dialog');
            const header = modal.querySelector('.ws-modal-header');
            const content = modal.querySelector('.ws-modal-content');

            if (this._isDraggable && dialog && header) {
                this.draggableInit(header, dialog, modal, content, 'ws-dragging', () => {
                    modal.style.zIndex = _wsModalGetTopZIndex(modal) + 1;
                });
            }

            if (this._isResizable && dialog && content) {
                const resizeHandles = modal.querySelectorAll('.ws-modal-resize-handle');
                if (resizeHandles.length) {
                    this.resizableInit(resizeHandles, dialog, content, modal, 300, () => header?.offsetHeight ?? 0);
                }
            }

            const onEvent = (e) => {
                if (e.detail?.action === 'show') {
                    this.modalShow();
                } else if (e.detail?.action === 'hide') {
                    this.modalHide();
                }
            };
            modal.addEventListener('ws-modal', onEvent);

            this._modalCleanup = () => {
                modal.removeEventListener('ws-modal', onEvent);
            };

            if (this.$el.getAttribute('data-ws-auto-show') === 'true') {
                this.modalShow();
            }
        });
    },

    destroy() {
        const modal = this.$refs.modalEl;
        const wasOpen = modal?.classList.contains('ws-modal-visible');

        this._modalRemoveTaskbarBtn();
        this._modalCleanup?.();
        clearTimeout(this._shakeTimeout);
        this._cancelHideTransition?.();
        this._cancelExpandTransition?.();
        this._cancelMinimizeTransition?.();
        this.draggableDestroy();
        this.resizableDestroy();

        if (wasOpen) {
            this._modalDetachListeners();
            if (!this._isDraggable) {
                _wsScrollLock.unlock();
            }
            if (this._hasBackdrop) {
                _wsBackdrop.hide();
            }
        }
    },

    /**
     * Modal
     */
    modalShow() {
        const modal = this.$refs.modalEl;
        if (modal?.classList.contains('ws-modal-visible')) {
            return;
        }

        this._cancelHideTransition?.();
        this._cancelHideTransition = null;

        // Capture button rect before removing it - used for the restore animation.
        const btnRect = this._minimizedBtn?.getBoundingClientRect() ?? null;
        this._modalRemoveTaskbarBtn();

        modal.style.display = 'block';
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('aria-modal', 'true');
        modal.style.zIndex = _wsModalGetTopZIndex(modal) + 1;

        // Trigger CSS transition (.ws-modal → .ws-modal.ws-modal-visible).
        modal.offsetHeight;
        modal.classList.add('ws-modal-visible');
        this._modalAttachListeners();

        // Draggable modals do not lock scroll: multiple can be open simultaneously.
        if (!this._isDraggable) {
            _wsScrollLock.lock();
        }

        if (this._hasBackdrop) {
            _wsBackdrop.show();
        }

        // Move focus inside the modal (no trap, just initial positioning).
        this.$nextTick(() => {
            const focusable = modal.querySelector(
                'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
            );
            (focusable ?? modal).focus();
        });

        // Animate content from the minimized button position if opening from minimized state.
        if (btnRect) {
            const content = modal.querySelector('.ws-modal-content');
            const contentRect = content.getBoundingClientRect();
            const duration =
                Math.max(
                    0,
                    ...getComputedStyle(modal)
                        .transitionDuration.split(',')
                        .map((s) => parseFloat(s) * 1000),
                ) || 300;

            const scaleX = Math.max(btnRect.width / contentRect.width, 0.001);
            const scaleY = Math.max(btnRect.height / contentRect.height, 0.001);
            const dx = btnRect.left + btnRect.width / 2 - (contentRect.left + contentRect.width / 2);
            const dy = btnRect.top + btnRect.height / 2 - (contentRect.top + contentRect.height / 2);

            // Instantly position content at the button (no transition).
            content.style.transition = 'none';
            content.style.transformOrigin = '50% 50%';
            content.style.transform = `translate(${dx}px, ${dy}px) scale(${scaleX}, ${scaleY})`;

            content.offsetHeight; // force reflow

            // Animate to natural position in parallel with the CSS fade-in.
            this._cancelMinimizeTransition?.();
            content.style.transition = `transform ${duration}ms linear`;
            content.style.removeProperty('transform');
            this._cancelMinimizeTransition = afterTransition(
                content,
                () => {
                    this._cancelMinimizeTransition = null;
                    content.style.removeProperty('transition');
                    content.style.removeProperty('transformOrigin');
                },
                { property: 'transform', fallback: duration },
            );
        }
    },

    modalHide() {
        const modal = this.$refs.modalEl;
        if (!modal?.classList.contains('ws-modal-visible')) {
            return;
        }

        // Move focus out before aria-hidden to avoid AT warning (focused descendant inside aria-hidden ancestor).
        if (modal.contains(document.activeElement)) {
            document.activeElement.blur();
        }

        modal.classList.remove('ws-modal-visible');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        // Preserve saved drag state when minimizing so it can be restored after un-expanding
        if (!this._minimizedBtn) {
            this._savedDragState = null;
        }
        this._modalDetachListeners();

        if (this._hasBackdrop) {
            _wsBackdrop.hide();
        }

        // Capture before afterTransition: _minimizedBtn may be cleared by modalRestore() in the meantime.
        const wasMinimized = !!this._minimizedBtn;

        this._cancelHideTransition = afterTransition(modal, () => {
            this._cancelHideTransition = null;
            if (!modal.classList.contains('ws-modal-visible')) {
                modal.style.display = 'none';

                // Unlock scroll after the transition so the scrollbar doesn't reappear mid-fade.
                if (!this._isDraggable) {
                    _wsScrollLock.unlock();
                }

                // Reset drag position after the transition, not before (would snap during fade).
                if (this._isDraggable && !wasMinimized) {
                    this.draggableReset();
                }

                if (this._isResizable && !wasMinimized) {
                    this.resizableReset();
                }

                if (this._destroyOnDismiss && !wasMinimized) {
                    this.$dispatch('ws-modal-manager:destroy', { key: modal.id });
                }
            }
        });
    },

    modalToggleFullscreen() {
        const modal = this.$refs.modalEl;
        const dialog = modal.querySelector('.ws-modal-dialog');
        const btn = modal.querySelector('.ws-modal-expand-btn');
        const content = modal.querySelector('.ws-modal-content');

        this._isExpanded = !this._isExpanded;
        btn?.setAttribute('aria-pressed', this._isExpanded ? 'true' : 'false');

        if (this._isExpanded) {
            modal.style.zIndex = _wsModalGetTopZIndex(modal) + 1;
        }

        const isAbsolute = dialog.style.position === 'absolute';
        const hasRestore = !this._isExpanded && this._savedDragState !== null;

        // Animate directly from current state to final state using width/height/translate.
        // Use content's rect - dialog min-height fills the viewport even for short modals,
        // making its dimensions unreliable as animation bounds.
        // Read CSS transition duration before suppressing it.
        const duration = Math.max(
            0,
            ...getComputedStyle(dialog)
                .transitionDuration.split(',')
                .map((s) => parseFloat(s) * 1000),
        );
        const first = content.getBoundingClientRect();

        // Save inline state before resetting, only when there is an absolute position to restore.
        if (this._isExpanded && isAbsolute) {
            const dialogProps = ['position', 'left', 'top', 'width', 'margin', 'max-width', 'min-height', 'align-items'];
            const contentProps = ['height', 'max-height'];
            this._savedDragState = {
                dialog: Object.fromEntries(dialogProps.map((p) => [p, dialog.style.getPropertyValue(p)]).filter(([, v]) => v)),
                content: Object.fromEntries(contentProps.map((p) => [p, content.style.getPropertyValue(p)]).filter(([, v]) => v)),
                resizableAbsoluteInit: this._resizableAbsoluteInit,
                draggablePositionInitialized: this._draggablePositionInitialized,
            };
        }

        // Suppress transitions so final state is computed instantly.
        dialog.style.transition = 'none';
        content.style.transition = 'none';
        dialog.classList.add('ws-expanding');

        if (hasRestore) {
            // Collapsing: restore saved position/size as the final state.
            const state = this._savedDragState;
            this._savedDragState = null;
            dialog.classList.remove('ws-modal-fullscreen');
            for (const [prop, value] of Object.entries(state.dialog)) {
                dialog.style.setProperty(prop, value);
            }
            for (const [prop, value] of Object.entries(state.content)) {
                content.style.setProperty(prop, value);
            }
            this._resizableAbsoluteInit = state.resizableAbsoluteInit;
            this._draggablePositionInitialized = state.draggablePositionInitialized;
        } else {
            this.draggableReset();
            this.resizableReset();
            dialog.classList.toggle('ws-modal-fullscreen', this._isExpanded);
        }

        content.offsetHeight; // force reflow to compute final layout
        const last = content.getBoundingClientRect();

        // Capture inline height/max-height before the animation overwrites them - they may
        // have been restored from a saved resizable state and must be preserved after cleanup.
        const savedHeight = content.style.getPropertyValue('height');
        const savedMaxHeight = content.style.getPropertyValue('max-height');

        // Suppress min/max-height so they don't override the animated height values.
        // flex-shrink: 0 prevents the flex container from compressing the initial width.
        content.style.minHeight = '0';
        content.style.maxHeight = 'none';
        content.style.flexShrink = '0';
        content.style.width = `${first.width}px`;
        content.style.height = `${first.height}px`;

        // Re-measure after setting the initial size: flex align-items: center on the dialog
        // will re-center the content at its new (smaller) height, so last.top is no longer
        // the correct reference point for the translate.
        content.offsetHeight;
        const actualStart = content.getBoundingClientRect();
        const dx = first.left - actualStart.left;
        const dy = first.top - actualStart.top;

        content.style.transform = `translate(${dx}px, ${dy}px)`;

        content.offsetHeight; // force reflow to apply start state

        content.style.transition = `width ${duration}ms linear, height ${duration}ms linear, transform ${duration}ms linear`;
        content.style.width = `${last.width}px`;
        content.style.height = `${last.height}px`;
        content.style.transform = 'none';
        this._cancelExpandTransition?.();
        this._cancelExpandTransition = afterTransition(
            content,
            () => {
                this._cancelExpandTransition = null;
                content.style.removeProperty('transition');
                content.style.removeProperty('transform');
                content.style.removeProperty('flex-shrink');
                content.style.removeProperty('width');
                savedHeight ? content.style.setProperty('height', savedHeight) : content.style.removeProperty('height');
                content.style.removeProperty('min-height');
                savedMaxHeight
                    ? content.style.setProperty('max-height', savedMaxHeight)
                    : content.style.removeProperty('max-height');
                dialog.style.removeProperty('transition');
                dialog.classList.remove('ws-expanding');
            },
            { property: 'transform', fallback: duration },
        );
    },

    modalMinimize() {
        const label = this.$root.getAttribute('data-ws-label') || '';
        const btnClass = this.$root.getAttribute('data-ws-btn-class') || '';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = btnClass;
        btn.textContent = label;
        btn.addEventListener('click', () => this.modalRestore());

        this._minimizedBtn = btn;
        this._modalGetOrCreateTaskbar().appendChild(btn);

        // Measure positions while both elements are in the DOM.
        const modal = this.$refs.modalEl;
        const content = modal.querySelector('.ws-modal-content');
        const first = content.getBoundingClientRect();
        const last = btn.getBoundingClientRect();
        const duration =
            Math.max(
                0,
                ...getComputedStyle(modal)
                    .transitionDuration.split(',')
                    .map((s) => parseFloat(s) * 1000),
            ) || 300;

        // Start CSS fade-out.
        this.modalHide();

        // Animate content toward button in parallel with the CSS fade-out.
        const scaleX = Math.max(last.width / first.width, 0.001);
        const scaleY = Math.max(last.height / first.height, 0.001);
        const dx = last.left + last.width / 2 - (first.left + first.width / 2);
        const dy = last.top + last.height / 2 - (first.top + first.height / 2);

        content.style.transformOrigin = '50% 50%';
        content.style.transition = `transform ${duration}ms linear`;
        content.style.transform = `translate(${dx}px, ${dy}px) scale(${scaleX}, ${scaleY})`;

        this._cancelMinimizeTransition?.();
        this._cancelMinimizeTransition = afterTransition(
            content,
            () => {
                this._cancelMinimizeTransition = null;
                content.style.removeProperty('transform');
                content.style.removeProperty('transformOrigin');
                content.style.removeProperty('transition');
            },
            { property: 'transform', fallback: duration },
        );
    },

    modalRestore() {
        this.modalShow();
    },

    /**
     * Listeners
     */
    _modalAttachListeners() {
        const modal = this.$refs.modalEl;
        modal.addEventListener('click', this._onDismissClick);
        modal.addEventListener('click', this._onBackdropClick);
        document.addEventListener('keydown', this._onKeydown);
    },

    _modalDetachListeners() {
        const modal = this.$refs.modalEl;
        modal.removeEventListener('click', this._onDismissClick);
        modal.removeEventListener('click', this._onBackdropClick);
        document.removeEventListener('keydown', this._onKeydown);
    },

    /**
     * Utils
     */
    _modalShake() {
        const modal = this.$refs.modalEl;
        clearTimeout(this._shakeTimeout);
        modal.classList.remove('ws-modal-static');
        modal.offsetHeight; // force reflow to allow animation replay
        modal.classList.add('ws-modal-static');
        this._shakeTimeout = setTimeout(() => {
            this._shakeTimeout = null;
            modal.classList.remove('ws-modal-static');
        }, 300);
    },

    _modalGetOrCreateTaskbar() {
        let bar = document.getElementById('minimized_modals');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'minimized_modals';
            document.body.appendChild(bar);
        }
        return bar;
    },

    _modalRemoveTaskbarBtn() {
        if (this._minimizedBtn) {
            this._minimizedBtn.remove();
            this._minimizedBtn = null;
        }
    },
}));
