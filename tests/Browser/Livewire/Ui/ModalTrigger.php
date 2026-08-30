<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\View\View;
use Livewire\Component;
use Wirestrap\Traits\WithWirestrap;

class ModalTrigger extends Component
{
    use WithWirestrap;

    public function openManaged(): void
    {
        $this->modalShowManaged(
            component: 'ui.modal-child',
            props: ['label' => 'managed-content'],
            modalProps: ['title' => 'Managed Modal', 'size' => '600'],
            key: 'managed-1',
        );
    }

    public function openManagedDuplicate(): void
    {
        $this->modalShowManaged(
            component: 'ui.modal-child',
            props: ['label' => 'managed-content'],
            modalProps: ['title' => 'Managed Modal', 'size' => '600'],
            key: 'managed-1',
        );
    }

    public function openManagedDestroy(): void
    {
        $this->modalShowManaged(
            component: 'ui.modal-child',
            props: ['label' => 'destroy-content'],
            modalProps: ['title' => 'Destroy On Dismiss', 'destroyOnDismiss' => true],
            key: 'managed-destroy',
        );
    }

    public function openManagedMinDestroyDismiss(): void
    {
        $this->modalShowManaged(
            component: 'ui.modal-child',
            props: ['label' => 'min-destroy-content'],
            modalProps: [
                'title' => 'Min Destroy',
                'minimizable' => true,
                'draggable' => true,
                'destroyOnDismiss' => true,
            ],
            key: 'managed-min-destroy',
        );
    }

    public function destroyByKey(): void
    {
        $this->modalDestroyManaged(key: 'managed-1');
    }

    public function destroyByComponent(): void
    {
        $this->modalDestroyManaged(component: 'ui.modal-child');
    }

    public function destroyAll(): void
    {
        $this->modalDestroyManaged();
    }

    public function phpShow(): void
    {
        $this->modalShow('modal-basic');
    }

    public function phpHide(): void
    {
        $this->modalHide('modal-basic');
    }

    public function render(): View
    {
        return view('livewire.ui.modal-trigger');
    }
}
