/**
 * Toast system.
 *
 * Provides addToast() and global Wirestrap.toast config.
 * Toasts are added to a dynamically created container.
 *
 * Timer:
 *   Uses rAF for progress + auto-dismiss.
 *   Pauses on hover, resumes where it stopped.
 *
 * On Livewire navigation, all toasts are cleared.
 *
 * Config:
 *   duration  - default timeout (0 = persistent)
 *   max       - max visible toasts
 *   placement - container position
 *
 * Options:
 *   message  - required (or string shorthand)
 *   title    - optional
 *   type     - style (default: primary)
 *   duration - override
 */
import { afterTransition } from '../../utils/transition';
import { onNavigate } from '../../utils/navigate';

/*
|--------------------------------------------------------------------------
|   Container
|--------------------------------------------------------------------------
*/

let _container = null;

function _getContainer() {
    if (_container && document.body.contains(_container)) {
        return _container;
    }
    _container = document.createElement('div');
    _container.className = `ws-toast-container ws-toast-${_toasts._placement}`;
    document.body.appendChild(_container);
    return _container;
}

/*
|--------------------------------------------------------------------------
|   Core
|--------------------------------------------------------------------------
*/

const _toasts = {
    /**
     * Config
     */
    _seq: 0,
    _max: 20,
    _defaultDuration: 5000,
    _placement: 'bottom-end',

    /**
     * State
     */
    _items: [],

    /**
     * Public
     */
    add(options) {
        if (this._items.length >= this._max) {
            this.remove(this._items[0].id);
        }

        const id = ++this._seq;
        const duration = options.duration !== undefined ? options.duration : this._defaultDuration;
        const item = {
            id,
            duration,
            el: null,
            progressBarEl: null,
            timer: null,
        };

        item.el = this._createEl(item, options);
        _getContainer().appendChild(item.el);
        this._items.push(item);

        // Defer adding the visible class to the next frame so the browser paints the initial
        // non-visible state first, allowing the CSS transition to play on entry
        requestAnimationFrame(() => {
            item.el.classList.add('ws-toast-visible');
        });

        if (duration > 0) {
            this._startTimer(item);
        }

        return id;
    },

    remove(id) {
        const index = this._items.findIndex((i) => i.id === id);
        if (index === -1) {
            return;
        }

        const item = this._items[index];
        this._items.splice(index, 1);

        if (item.timer) {
            cancelAnimationFrame(item.timer.raf);
        }

        this._animateOut(item.el, () => item.el.remove());
    },

    pause(id) {
        const item = this._items.find((i) => i.id === id);
        if (!item || !item.timer || item.timer.paused) {
            return;
        }

        cancelAnimationFrame(item.timer.raf);
        item.timer.elapsed += performance.now() - item.timer.startAt;
        item.timer.paused = true;
    },

    resume(id) {
        const item = this._items.find((i) => i.id === id);
        if (!item || !item.timer || !item.timer.paused) {
            return;
        }

        item.timer.paused = false;
        this._runTimer(item);
    },

    /**
     * Private
     */
    _createEl(item, options) {
        const type = options.type || 'primary';

        const el = document.createElement('div');
        el.className = `ws-toast ws-toast-${type}`;
        el.setAttribute('role', 'alert');

        if (options.title) {
            const header = document.createElement('div');
            header.className = 'ws-toast-header';

            const icon = document.createElement('span');
            icon.className = `ws-toast-icon`;
            header.appendChild(icon);

            const title = document.createElement('strong');
            title.className = 'ws-toast-title';
            title.textContent = options.title;
            header.appendChild(title);

            el.appendChild(header);
        }

        const body = document.createElement('div');
        body.className = 'ws-toast-body';
        body.textContent = options.message || '';
        el.appendChild(body);

        if (item.duration > 0) {
            const progress = document.createElement('div');
            progress.className = 'ws-toast-progress';
            const bar = document.createElement('div');
            bar.className = 'ws-toast-progress-bar';
            progress.appendChild(bar);
            el.appendChild(progress);
            item.progressBarEl = bar;
        }

        el.addEventListener('mouseenter', () => this.pause(item.id));
        el.addEventListener('mouseleave', () => this.resume(item.id));
        el.addEventListener('click', () => this.remove(item.id));

        return el;
    },

    _startTimer(item) {
        item.timer = {
            raf: null,
            elapsed: 0,
            startAt: null,
            duration: item.duration,
            paused: false,
        };
        this._runTimer(item);
    },

    _runTimer(item) {
        item.timer.startAt = performance.now();
        const tick = () => {
            const elapsed = item.timer.elapsed + (performance.now() - item.timer.startAt);
            const progress = Math.max(1 - elapsed / item.timer.duration, 0);
            item.progressBarEl.style.width = progress * 100 + '%';
            if (progress > 0) {
                item.timer.raf = requestAnimationFrame(tick);
            } else {
                this.remove(item.id);
            }
        };
        item.timer.raf = requestAnimationFrame(tick);
    },

    _animateOut(el, cb) {
        el.classList.remove('ws-toast-visible');
        el.classList.add('ws-toast-leaving');
        afterTransition(el, cb);
    },
};

/*
|--------------------------------------------------------------------------
|   Public API
|--------------------------------------------------------------------------
*/

export const addToast = (options) => {
    _toasts.add(typeof options === 'string' ? { message: options } : options);
};

globalThis.Wirestrap ??= {};
globalThis.Wirestrap.toast = {
    configure(options) {
        if (options.duration !== undefined) _toasts._defaultDuration = options.duration;
        if (options.max !== undefined) _toasts._max = options.max;
        if (options.placement !== undefined) _toasts._placement = options.placement;
    },

    destroy() {
        _toasts._items.forEach((item) => {
            if (item.timer) cancelAnimationFrame(item.timer.raf);
        });
        _toasts._items = [];

        if (_container && document.body.contains(_container)) {
            _container.remove();
            _container = null;
        }
    },
};

/*
|--------------------------------------------------------------------------
|   Hooks
|--------------------------------------------------------------------------
*/

onNavigate(() => Wirestrap.toast.destroy());
