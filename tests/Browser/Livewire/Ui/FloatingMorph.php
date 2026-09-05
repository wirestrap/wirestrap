<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class FloatingMorph extends Component
{
    public int $count = 0;

    public function bump(): void
    {
        $this->count++;
    }

    public function render(): View
    {
        return view('livewire.ui.floating-morph');
    }
}
