<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class ToastTrigger extends Component
{
    use WithWirestrap;

    public function basic(): void
    {
        $this->toast('success', 'PHP toast message');
    }

    public function withTitle(): void
    {
        $this->toast(type: 'info', message: 'PHP titled toast', title: 'Info');
    }

    public function persistent(): void
    {
        $this->toast(type: 'danger', message: 'PHP persistent', duration: 0);
    }

    public function render(): View
    {
        return view('livewire.ui.toast-trigger');
    }
}
