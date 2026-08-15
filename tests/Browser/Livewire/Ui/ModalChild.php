<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class ModalChild extends Component
{
    use WithWirestrap;

    #[Locked]
    public string $modalId;

    public string $label = '';

    public function selfClose(): void
    {
        $this->modalHide($this->modalId);
    }

    public function selfDestroy(): void
    {
        $this->modalDestroyManaged(key: $this->modalId);
    }

    public function render(): View
    {
        return view('livewire.ui.modal-child');
    }
}
