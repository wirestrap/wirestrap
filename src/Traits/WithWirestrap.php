<?php

namespace Wirestrap\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait WithWirestrap
{
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

    protected function alert(
        string $type = '',
        string $message = '',
        ?string $title = null,
        ?string $dismissText = null,
        ?bool $showDismiss = null,
        ?bool $backdropDismiss = null,
        ?bool $escapeDismiss = null,
        ?int $duration = null,
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

        $this->js('$wirestrap.alert.show(' . json_encode($options) . ')');
    }

    protected function modalShow(string $id): void
    {
        $this->js('$wirestrap.modal.show(' . json_encode($id) . ')');
    }

    protected function modalHide(string $id): void
    {
        $this->js('$wirestrap.modal.hide(' . json_encode($id) . ')');
    }

    /**
     * @param array<string|int, mixed> $props
     * @param array<string|int, mixed> $modalProps
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

        $hash = is_string($key)
            ? 'ws-' . $key
            : 'ws-' . md5((string) json_encode($payload));

        $payload['event_expires_at'] = now()->addMinute()->timestamp;

        if (config('wirestrap.modal_manager.nonce', true)) {
            $nonce = (string) Str::uuid();
            Cache::put("ws-modal-nonce:{$hash}:{$nonce}", true, 60);
            $payload['nonce'] = $nonce;
        }

        $this->dispatch(
            event: 'ws-modal-manager:show',
            payload: encrypt($payload),
            hash: $hash,
        );
    }

    /**
     * @param string|list<string>|null $component
     * @param string|list<string>|null $key
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
