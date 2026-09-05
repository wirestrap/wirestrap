<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class AlertTrigger extends Component
{
    use WithWirestrap;

    public bool $deleted = false;
    public string $moved = '';

    public function basic(): void
    {
        $this->alert('success', 'PHP alert message');
    }

    public function withTitle(): void
    {
        $this->alert(type: 'info', message: 'PHP titled alert', title: 'Info');
    }

    public function delete(): void
    {
        $this->deleted = true;
    }

    public function move(int $itemId, int $targetId): void
    {
        $this->moved = "{$itemId}->{$targetId}";
    }

    public function goToTarget(): void
    {
        $this->alertRedirect('/_ws/test/ui/alert-target', 'Redirecting…', duration: 300);
    }

    public function render(): View
    {
        return view('livewire.ui.alert-trigger');
    }
}
