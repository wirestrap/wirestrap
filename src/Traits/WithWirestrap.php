<?php

namespace Wirestrap\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait WithWirestrap
{
    /**
     * Display a toast notification via the Alpine $wirestrap magic.
     */
    protected function toast(
        string $type = '',
        string $message = '',
        ?string $title = null,
        ?int $duration = null,
    ): void {
        $options = ['type' => $type, 'message' => $message];

        if ($title !== null) {
            $options['title'] = $title;
        }

        if ($duration !== null) {
            $options['duration'] = $duration;
        }

        $this->js('$wirestrap.toast(' . json_encode($options) . ')');
    }

    /**
     * Display an alert dialog via the Alpine $wirestrap magic.
     */
    protected function alert(
        string $type = '',
        string $message = '',
        ?string $title = null,
        ?string $dismissText = null,
        ?bool $showDismiss = null,
        ?bool $backdropDismiss = null,
        ?bool $escapeDismiss = null,
        ?int $duration = null,
        ?string $url = null,
    ): void {
        $options = ['type' => $type, 'message' => $message];

        if ($title !== null) {
            $options['title'] = $title;
        }

        if ($dismissText !== null) {
            $options['dismissText'] = $dismissText;
        }

        if ($showDismiss !== null) {
            $options['showDismiss'] = $showDismiss;
        }

        if ($backdropDismiss !== null) {
            $options['backdropDismiss'] = $backdropDismiss;
        }

        if ($escapeDismiss !== null) {
            $options['escapeDismiss'] = $escapeDismiss;
        }

        if ($duration !== null) {
            $options['duration'] = $duration;
        }

        if ($url !== null) {
            $options['url'] = $url;
        }

        $this->js('$wirestrap.alert.show(' . json_encode($options) . ')');
    }

    /**
     * Display an alert that sends the browser to $url once its countdown ends.
     */
    protected function alertRedirect(
        string $url,
        string $message = '',
        string $type = '',
        ?string $title = null,
        ?int $duration = null,
        ?bool $showDismiss = null,
        ?string $dismissText = null,
    ): void {
        $options = ['type' => $type, 'message' => $message, 'url' => $url];

        if ($title !== null) {
            $options['title'] = $title;
        }

        if ($duration !== null) {
            $options['duration'] = $duration;
        }

        if ($showDismiss !== null) {
            $options['showDismiss'] = $showDismiss;
        }

        if ($dismissText !== null) {
            $options['dismissText'] = $dismissText;
        }

        $this->js('$wirestrap.alert.redirect(' . json_encode($options) . ')');
    }

    /**
     * Show a standalone modal by its DOM id.
     */
    protected function modalShow(string $id): void
    {
        $this->js('$wirestrap.modal.show(' . json_encode($id) . ')');
    }

    /**
     * Hide a standalone modal by its DOM id.
     */
    protected function modalHide(string $id): void
    {
        $this->js('$wirestrap.modal.hide(' . json_encode($id) . ')');
    }

    /**
     * Open a managed modal by injecting a Livewire component into the ModalManager.
     *
     * The payload is encrypted and dispatched as a Livewire event. The ModalManager
     * decrypts it, validates expiry and nonce, then renders the component inside a modal.
     *
     * The child component receives a `$modalId` prop that can be used to close or show itself
     * (e.g. `$this->modalHide($this->modalId)` after a form submission).
     *
     * @param string $component Livewire component name in dot notation (e.g. 'users.edit-form').
     * @param array<string|int, mixed> $props Props passed to the child Livewire component.
     * @param array<string|int, mixed> $modalProps Props passed to the modal wrapper (title, size, draggable, etc.).
     * @param string|null $key Optional id for deduplication and targeted destruction via modalDestroyManaged().
     */
    protected function modalShowManaged(
        string $component,
        array $props,
        array $modalProps = [],
        ?string $key = null,
    ): void {
        $payload = [
            'component' => $component,
            'props' => $props,
            'modal_props' => $modalProps,
        ];

        // Use the user-provided key as modal id, or fall back to a content-based hash
        // so that identical payloads reuse the same modal instead of opening duplicates.
        $id = $key ?? md5((string) json_encode($payload));

        // Short TTL to reject stale or replayed events.
        $payload['event_expires_at'] = now()->addMinute()->timestamp;

        // Single-use nonce stored in cache to prevent event replay.
        if (config('wirestrap.modal_manager.nonce', true)) {
            $nonce = (string) Str::uuid();
            Cache::put("ws-modal-nonce:{$id}:{$nonce}", true, 60);
            $payload['nonce'] = $nonce;
        }

        // Payload is encrypted so the component name and props cannot be tampered with client-side.
        $this->dispatch(
            event: 'ws-modal-manager:show',
            payload: encrypt($payload),
            id: $id,
        );
    }

    /**
     * Destroy managed modals by component name, key, or all.
     *
     * @param string|list<string>|null $component Component name(s) to destroy all modals for.
     * @param string|list<string>|null $key Key(s) originally passed to modalShowManaged().
     */
    protected function modalDestroyManaged(
        string|array|null $component = null,
        string|array|null $key = null,
    ): void {
        $this->dispatch(
            event: 'ws-modal-manager:destroy',
            components: $component,
            key: $key,
        );
    }
}
