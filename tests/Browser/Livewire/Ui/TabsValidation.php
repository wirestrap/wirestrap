<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TabsValidation extends Component
{
    public string $name = '';

    public function save(): void
    {
        $this->validate([
            'name' => 'required',
        ]);
    }

    public function render(): View
    {
        return view('livewire.ui.tabs-validation');
    }
}
