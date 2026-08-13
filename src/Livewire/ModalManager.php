<?php

namespace Wirestrap\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class ModalManager extends Component
{
    use WithWirestrap;

    /**
     * Registered modals indexed by component name, each holding a list of internal ids
     * e.g. ['users.edit-form' => ['edit-user-42', 'edit-user-7']]
     *
     * @var array<string, list<string>>
     */
    #[Locked]
    public array $modals = [];

    /**
     * Livewire component props indexed by internal id
     * e.g. ['edit-user-42' => ['userId' => 42]]
     *
     * @var array<string, array<string|int, mixed>>
     */
    #[Locked]
    public array $props = [];

    /**
     * Modal wrapper props (title, size, draggable, etc.) indexed by internal id
     * e.g. ['edit-user-42' => ['title' => 'Edit user', 'draggable' => true]]
     *
     * @var array<string, array<string|int, mixed>>
     */
    #[Locked]
    public array $modalProps = [];

    public function render(): View
    {
        return view('wirestrap::livewire.modal-manager');
    }

    /**
     * Receive an encrypted modal payload, validate it, and register the modal for rendering.
     * If a modal with the same id already exists, it is re-shown instead of duplicated.
     */
    #[On('ws-modal-manager:show')]
    public function show(string $payload, string $id): void
    {
        $payload = decrypt($payload);
        $component = $payload['component'] ?? null;
        $props = $payload['props'] ?? [];
        $modalProps = $payload['modal_props'] ?? [];
        $expiresAt = $payload['event_expires_at'] ?? null;

        if (!$component) {
            return;
        }

        // Reject expired events
        if (!$expiresAt || now()->timestamp > $expiresAt) {
            return;
        }

        // Consume the single-use nonce to prevent replay
        if (config('wirestrap.modal_manager.nonce', true)) {
            $nonce = $payload['nonce'] ?? null;

            if (!$nonce || !Cache::pull("ws-modal-nonce:{$id}:{$nonce}")) {
                return;
            }
        }

        // If the modal already exists, just re-show it instead of creating a duplicate
        if (in_array($id, $this->modals[$component] ?? [], true)) {
            $this->modalShow('modal_' . $id);

            return;
        }

        $this->modals[$component][] = $id;
        $this->props[$id] = $props;
        $this->modalProps[$id] = $modalProps;
    }

    /**
     * Destroy managed modals by component name, key, or all.
     * Passing neither parameter destroys all managed modals.
     *
     * @param string|list<string>|null $components
     * @param string|list<string>|null $key
     */
    #[On('ws-modal-manager:destroy')]
    public function destroy(string|array|null $components = null, string|array|null $key = null): void
    {
        // Destroy all modals for the given component name(s)
        if ($components !== null) {
            foreach ((array) $components as $comp) {
                foreach (($this->modals[$comp] ?? []) as $id) {
                    unset($this->props[$id]);
                    unset($this->modalProps[$id]);
                }

                unset($this->modals[$comp]);
            }
        }

        // Destroy specific modals by key, regardless of which component they belong to
        if ($key !== null) {
            $keys = (array) $key; // Normalize string|array

            foreach ($this->modals as $comp => $compIds) {
                // Keep only ids that are not in the destroy list
                $filtered = array_values(array_filter($compIds, fn(string $id): bool => !in_array($id, $keys, true)));

                // No match in this component
                if (count($filtered) === count($compIds)) {
                    continue;
                }

                // All modals removed for this component: clean up the entry entirely
                if ($filtered === []) {
                    unset($this->modals[$comp]);
                } else {
                    $this->modals[$comp] = $filtered;
                }
            }

            foreach ($keys as $id) {
                unset($this->props[$id]);
                unset($this->modalProps[$id]);
            }
        }

        // No component or key specified: destroy all managed modals
        if ($components === null && $key === null) {
            $this->modals = [];
            $this->props = [];
            $this->modalProps = [];
        }
    }
}
