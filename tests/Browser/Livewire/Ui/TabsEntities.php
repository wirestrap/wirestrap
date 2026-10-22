<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TabsEntities extends Component
{
    /** @var array<string, string> */
    public array $entities = [
        'alpha' => 'Alpha',
        'beta' => 'Beta',
        'gamma' => 'Gamma',
    ];

    public function delete(string $key): void
    {
        unset($this->entities[$key]);

        $this->dispatch('entity-deleted', key: array_key_first($this->entities));
    }

    public function render(): View
    {
        return view('livewire.ui.tabs-entities');
    }
}
