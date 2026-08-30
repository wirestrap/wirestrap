<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CounterReactive extends Component
{
    public int $total = 100;

    public function increment(): void
    {
        $this->total += 500;
    }

    public function render(): View
    {
        return view('livewire.ui.counter-reactive');
    }
}
