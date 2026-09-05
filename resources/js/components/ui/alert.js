/**
 * Alert & confirm modal system.
 *
 * Provides Wirestrap.alert.show() / Wirestrap.alert.confirm.show() and global configs.
 * Only one alert is shown at a time; others are queued.
 *
 * Alert:
 *   Builds backdrop + dialog, handles Escape/backdrop clicks,
 *   and optional auto-dismiss with progress bar.
 *
 * Confirm:
 *   Extends alert with Cancel/Confirm actions and calls Livewire on confirm.
 *
 * Redirect:
 *   Blocking alert that sends the browser to a url once its countdown ends,
 *   leaving time to read the message before the page changes.
 *
 * On Livewire navigate, alerts are cleared and queue reset.
 *
 * Global configuration (Wirestrap.alert.configure):
 *   duration        - auto-dismiss duration in ms (0 = persistent).
 *   dismissText     - label for the dismiss button.
 *   showDismiss     - whether to show the dismiss button.
 *   backdropDismiss - whether a click on the backdrop dismisses the alert.
 *   escapeDismiss   - whether Escape dismisses the alert.
 *
 * Alert options (passed to alertShow / $wirestrap.alert.show):
 *   message, title, type, duration, showDismiss, dismissText,
 *   backdropDismiss, escapeDismiss, url.
 *
 * Confirm options (passed to alertShowConfirm / $wirestrap.alert.confirm):
 *   All alert options plus: wire, method, params, confirmText, cancelText.
 *
 * Redirect options (passed to $wirestrap.alert.redirect):
 *   All alert options plus: url. Navigation is a native full page load.
 */
import { afterTransition } from '../../utils/transition';
import { onNavigate } from '../../utils/navigate';

/*
|--------------------------------------------------------------------------
|   State
|--------------------------------------------------------------------------
*/

let _current = null;
const _queue = [];

/*
|--------------------------------------------------------------------------
|   Defaults
|--------------------------------------------------------------------------
*/

const _confirmDefaults = {
    type: 'primary',
    title: null,
    duration: 0,
    backdropDismiss: true,
    escapeDismiss: true,
    confirmText: 'Confirm',
    cancelText: 'Cancel',
};

const _redirectDefaults = {
    type: 'success',
    title: null,
    duration: 2000,
    showDismiss: true,
    dismissText: 'Continue',
    backdropDismiss: false,
    escapeDismiss: false,
};

/*
|--------------------------------------------------------------------------
|   Helpers
|--------------------------------------------------------------------------
*/

/**
 * Builds an alert action element. With a url it is a real link, so modified
 * clicks (new tab) and the status bar preview keep working; without one it is
 * a plain button. Both carry the same class and are styled identically.
 */
const _createActionEl = (className, label, url) => {
    const el = document.createElement(url ? 'a' : 'button');
    el.className = className;
    el.textContent = label;

    if (url) {
        el.href = url;
    } else {
        el.type = 'button';
    }

    return el;
};

/**
 * Inserts a footer above the progress bar so the countdown stays at the bottom.
 */
const _insertFooter = (item, footer) => {
    const progressEl = item.alertEl.querySelector('.ws-alert-progress');

    if (progressEl) {
        item.alertEl.insertBefore(footer, progressEl);
    } else {
        item.alertEl.appendChild(footer);
    }
};

/*
|--------------------------------------------------------------------------
|   Core
|--------------------------------------------------------------------------
*/

const _alert = {
    /**
     * Config
     */
    _defaultDuration: 0,
    _dismissText: 'OK',
    _showDismiss: true,
    _backdropDismiss: true,
    _escapeDismiss: true,

    /**
     * Public
     */
    show(options) {
        this._showOrQueue('alert', options);
    },

    showConfirm(options) {
        this._showOrQueue('confirm', options);
    },

    showRedirect(options) {
        this._showOrQueue('redirect', options);
    },

    /**
     * Private
     */
    _showOrQueue(kind, options) {
        if (_current) {
            _queue.push({ ...options, _kind: kind });
            return;
        }
        _current = this._buildFor(kind, options);
    },

    _buildFor(kind, options) {
        if (kind === 'confirm') {
            return this._buildConfirm(options);
        }
        if (kind === 'redirect') {
            return this._buildRedirect(options);
        }
        return this._build(options);
    },

    _next() {
        _current = null;
        if (_queue.length > 0) {
            const next = _queue.shift();
            const kind = next._kind;
            delete next._kind;
            _current = this._buildFor(kind, next);
        }
    },

    _build(options, onExpire = null) {
        const type = options.type || 'primary';
        const duration = options.duration !== undefined ? options.duration : this._defaultDuration;

        const item = {
            backdropEl: null,
            alertEl: null,
            progressBarEl: null,
            timer: null,
            shakeTimeout: null,
            keyHandler: null,
            backdropHandler: null,
            buttonHandlers: [],
            onExpire,
            _dismissed: false,
        };

        item.backdropEl = document.createElement('div');
        item.backdropEl.className = 'ws-alert-backdrop';

        item.alertEl = document.createElement('div');
        item.alertEl.className = `ws-alert ws-alert-${type}`;
        item.alertEl.setAttribute('role', 'alertdialog');
        item.alertEl.setAttribute('aria-modal', 'true');
        item.alertEl.setAttribute('tabindex', '-1');

        if (options.title) {
            const header = document.createElement('div');
            header.className = 'ws-alert-header';

            const icon = document.createElement('span');
            icon.className = `ws-alert-icon`;
            header.appendChild(icon);

            const title = document.createElement('strong');
            title.className = 'ws-alert-title';
            title.textContent = options.title;
            header.appendChild(title);

            item.alertEl.appendChild(header);
        }

        const body = document.createElement('div');
        body.className = 'ws-alert-body';
        body.textContent = options.message || '';
        item.alertEl.appendChild(body);

        const showDismiss = options.showDismiss !== undefined ? options.showDismiss : this._showDismiss;
        if (showDismiss) {
            const footer = document.createElement('div');
            footer.className = 'ws-alert-footer';

            // With a url the button becomes a link: the click dismisses the alert and the
            // browser performs the navigation itself.
            const btn = _createActionEl('ws-alert-dismiss', options.dismissText || this._dismissText, options.url);
            const dismissHandler = () => this._dismiss(item);
            btn.addEventListener('click', dismissHandler, { once: true });
            item.buttonHandlers.push({ el: btn, handler: dismissHandler });
            footer.appendChild(btn);
            item.alertEl.appendChild(footer);
        }

        if (duration > 0) {
            const progress = document.createElement('div');
            progress.className = 'ws-alert-progress';
            const bar = document.createElement('div');
            bar.className = 'ws-alert-progress-bar';
            progress.appendChild(bar);
            item.alertEl.appendChild(progress);
            item.progressBarEl = bar;
        }

        const backdropDismiss = options.backdropDismiss !== undefined ? options.backdropDismiss : this._backdropDismiss;
        item.backdropHandler = (e) => {
            if (e.target !== item.backdropEl) {
                return;
            }
            if (backdropDismiss) {
                this._dismiss(item);
            } else {
                this._shake(item);
            }
        };
        item.backdropEl.addEventListener('click', item.backdropHandler);

        item.backdropEl.appendChild(item.alertEl);
        document.body.appendChild(item.backdropEl);

        const escapeDismiss = options.escapeDismiss !== undefined ? options.escapeDismiss : this._escapeDismiss;
        item.keyHandler = (e) => {
            if (e.key !== 'Escape') {
                return;
            }
            if (escapeDismiss) {
                this._dismiss(item);
            } else {
                this._shake(item);
            }
        };
        document.addEventListener('keydown', item.keyHandler);

        // Double rAF: ensures the initial state is painted before adding the visible classes
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                item.backdropEl.classList.add('ws-alert-visible');
                item.alertEl.classList.add('ws-alert-visible');
                item.alertEl.focus();
            });
        });

        if (duration > 0) {
            this._startTimer(item, duration);
        }

        return item;
    },

    _buildConfirm(options) {
        const resolved = {
            type: options.type !== undefined ? options.type : _confirmDefaults.type,
            title: options.title !== undefined ? options.title : _confirmDefaults.title,
            duration: options.duration !== undefined ? options.duration : _confirmDefaults.duration,
            backdropDismiss: options.backdropDismiss !== undefined ? options.backdropDismiss : _confirmDefaults.backdropDismiss,
            escapeDismiss: options.escapeDismiss !== undefined ? options.escapeDismiss : _confirmDefaults.escapeDismiss,
        };

        const item = this._build({ ...options, ...resolved, showDismiss: false });

        const footer = document.createElement('div');
        footer.className = 'ws-alert-footer';

        const cancelBtn = _createActionEl('ws-alert-cancel', options.cancelText || _confirmDefaults.cancelText);
        const cancelHandler = () => this._dismiss(item);
        cancelBtn.addEventListener('click', cancelHandler, { once: true });
        item.buttonHandlers.push({ el: cancelBtn, handler: cancelHandler });
        footer.appendChild(cancelBtn);

        const confirmBtn = _createActionEl('ws-alert-dismiss', options.confirmText || _confirmDefaults.confirmText);
        const confirmHandler = () => {
            confirmBtn.disabled = true;
            this._dismiss(item);
            document.activeElement.blur();
            options.wire.$call(options.method, ...(options.params || []));
        };
        confirmBtn.addEventListener('click', confirmHandler, { once: true });
        item.buttonHandlers.push({ el: confirmBtn, handler: confirmHandler });
        footer.appendChild(confirmBtn);

        _insertFooter(item, footer);

        return item;
    },

    _buildRedirect(options) {
        const resolved = {
            type: options.type !== undefined ? options.type : _redirectDefaults.type,
            title: options.title !== undefined ? options.title : _redirectDefaults.title,
            duration: options.duration !== undefined ? options.duration : _redirectDefaults.duration,
            backdropDismiss: options.backdropDismiss !== undefined ? options.backdropDismiss : _redirectDefaults.backdropDismiss,
            escapeDismiss: options.escapeDismiss !== undefined ? options.escapeDismiss : _redirectDefaults.escapeDismiss,
        };

        // Native full page load, so any target works whether or not Livewire is on the page.
        // The alert is left on screen: dismissing it first would flash the underlying view
        // while the browser loads the destination.
        const item = this._build({ ...options, ...resolved, showDismiss: false }, () => window.location.assign(options.url));

        const showDismiss = options.showDismiss !== undefined ? options.showDismiss : _redirectDefaults.showDismiss;
        if (showDismiss) {
            const footer = document.createElement('div');
            footer.className = 'ws-alert-footer';

            // Plain link with no handler: clicking it just performs the navigation the countdown
            // would have performed, ahead of time.
            const btn = _createActionEl('ws-alert-dismiss', options.dismissText || _redirectDefaults.dismissText, options.url);
            footer.appendChild(btn);

            _insertFooter(item, footer);
        }

        return item;
    },

    _shake(item) {
        clearTimeout(item.shakeTimeout);
        item.alertEl.classList.remove('ws-alert-shaking');
        item.alertEl.offsetHeight;
        item.alertEl.classList.add('ws-alert-shaking');
        item.shakeTimeout = setTimeout(() => {
            item.shakeTimeout = null;
            item.alertEl.classList.remove('ws-alert-shaking');
        }, 300);
    },

    _dismiss(item) {
        if (item._dismissed) {
            return;
        }
        item._dismissed = true;

        Alpine.destroyTree(item.backdropEl);

        if (item.keyHandler) {
            document.removeEventListener('keydown', item.keyHandler);
        }

        if (item.backdropHandler) {
            item.backdropEl.removeEventListener('click', item.backdropHandler);
        }

        for (const { el, handler } of item.buttonHandlers) {
            el.removeEventListener('click', handler);
        }

        if (item.shakeTimeout) {
            clearTimeout(item.shakeTimeout);
            item.shakeTimeout = null;
        }

        if (item.timer) {
            cancelAnimationFrame(item.timer.raf);
        }

        item.backdropEl.classList.remove('ws-alert-visible');
        item.alertEl.classList.remove('ws-alert-visible');
        item.backdropEl.classList.add('ws-alert-leaving');
        item.alertEl.classList.add('ws-alert-leaving');

        afterTransition(
            item.alertEl,
            () => {
                item.backdropEl.remove();
                this._next();
            },
            { fallback: 200 },
        );
    },

    _startTimer(item, duration) {
        item.timer = {
            raf: null,
            elapsed: 0,
            startAt: null,
            duration,
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
            } else if (item.onExpire) {
                item.onExpire();
            } else {
                this._dismiss(item);
            }
        };
        item.timer.raf = requestAnimationFrame(tick);
    },
};

/*
|--------------------------------------------------------------------------
|   Public API
|--------------------------------------------------------------------------
*/

globalThis.Wirestrap ??= {};
globalThis.Wirestrap.alert = {
    show: (options) => {
        _alert.show(typeof options === 'string' ? { message: options } : options);
    },

    configure(options) {
        if (options.duration !== undefined) _alert._defaultDuration = options.duration;
        if (options.dismissText !== undefined) _alert._dismissText = options.dismissText;
        if (options.showDismiss !== undefined) _alert._showDismiss = options.showDismiss;
        if (options.backdropDismiss !== undefined) _alert._backdropDismiss = options.backdropDismiss;
        if (options.escapeDismiss !== undefined) _alert._escapeDismiss = options.escapeDismiss;
    },

    redirect: {
        show: (options) => {
            _alert.showRedirect(options);
        },

        configure(options) {
            if (options.type !== undefined) _redirectDefaults.type = options.type;
            if (options.title !== undefined) _redirectDefaults.title = options.title;
            if (options.duration !== undefined) _redirectDefaults.duration = options.duration;
            if (options.showDismiss !== undefined) _redirectDefaults.showDismiss = options.showDismiss;
            if (options.dismissText !== undefined) _redirectDefaults.dismissText = options.dismissText;
            if (options.backdropDismiss !== undefined) _redirectDefaults.backdropDismiss = options.backdropDismiss;
            if (options.escapeDismiss !== undefined) _redirectDefaults.escapeDismiss = options.escapeDismiss;
        },
    },

    confirm: {
        show: (options) => {
            _alert.showConfirm(options);
        },

        configure(options) {
            if (options.type !== undefined) _confirmDefaults.type = options.type;
            if (options.title !== undefined) _confirmDefaults.title = options.title;
            if (options.duration !== undefined) _confirmDefaults.duration = options.duration;
            if (options.backdropDismiss !== undefined) _confirmDefaults.backdropDismiss = options.backdropDismiss;
            if (options.escapeDismiss !== undefined) _confirmDefaults.escapeDismiss = options.escapeDismiss;
            if (options.confirmText !== undefined) _confirmDefaults.confirmText = options.confirmText;
            if (options.cancelText !== undefined) _confirmDefaults.cancelText = options.cancelText;
        },
    },
};

/*
|--------------------------------------------------------------------------
|   Hooks
|--------------------------------------------------------------------------
*/

onNavigate(() => {
    if (_current) {
        if (_current.keyHandler) {
            document.removeEventListener('keydown', _current.keyHandler);
        }
        if (_current.backdropHandler) {
            _current.backdropEl.removeEventListener('click', _current.backdropHandler);
        }
        for (const { el, handler } of _current.buttonHandlers) {
            el.removeEventListener('click', handler);
        }
        if (_current.timer) {
            cancelAnimationFrame(_current.timer.raf);
        }
        if (_current.shakeTimeout) {
            clearTimeout(_current.shakeTimeout);
        }
        _current.backdropEl?.remove();
        _current = null;
    }
    _queue.length = 0;
});
