<?php

namespace Wirestrap\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class ModalManager extends Component
{
    use WithWirestrap;

    /**
     * [component => list<hash>]
     *
     * @var array<string, list<string>>
     */
    #[Locked]
    public array $modals = [];

    /**
     * [hash => props]
     *
     * @var array<string, array<string|int, mixed>>
     */
    #[Locked]
    public array $props = [];

    /**
     * [hash => modalProps]
     *
     * @var array<string, array<string|int, mixed>>
     */
    #[Locked]
    public array $modalProps = [];

    public function render(): View
    {
        return view('wirestrap::livewire.modal-manager');
    }

    #[On('ws-modal-manager:show')]
    public function show(string $payload, string $hash): void
    {
        $payload = decrypt($payload);
        $component = $payload['component'] ?? null;
        $props = $payload['props'] ?? [];
        $modalProps = $payload['modal_props'] ?? [];

        if (!$component) {
            return;
        }

        if (in_array($hash, $this->modals[$component] ?? [], true)) {
            $this->modalShow('modal_' . $hash);

            return;
        }

        $this->modals[$component][] = $hash;
        $this->props[$hash] = $props;
        $this->modalProps[$hash] = $modalProps;
    }

    /**
     * @param string|list<string>|null $components
     * @param string|list<string>|null $key
     */
    #[On('ws-modal-manager:destroy')]
    public function destroy(string|array|null $components = null, string|array|null $key = null): void
    {
        if ($components !== null) {
            foreach ((array) $components as $comp) {
                foreach (($this->modals[$comp] ?? []) as $hash) {
                    unset($this->props[$hash]);
                    unset($this->modalProps[$hash]);
                }

                unset($this->modals[$comp]);
            }

            return;
        }

        if ($key !== null) {
            $hashes = array_map(fn(string $k): string => 'ws-' . $k, (array) $key);

            foreach ($this->modals as $comp => $compHashes) {
                $filtered = array_values(array_filter($compHashes, fn(string $h): bool => !in_array($h, $hashes, true)));

                if (count($filtered) === count($compHashes)) {
                    continue;
                }

                if ($filtered === []) {
                    unset($this->modals[$comp]);
                } else {
                    $this->modals[$comp] = $filtered;
                }
            }

            foreach ($hashes as $hash) {
                unset($this->props[$hash]);
                unset($this->modalProps[$hash]);
            }

            return;
        }

        $this->modals = [];
        $this->props = [];
        $this->modalProps = [];
    }
}
