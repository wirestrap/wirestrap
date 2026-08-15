<?php

namespace Tests\Browser\Livewire\Ui;

use Illuminate\View\View;
use Livewire\Component;

class TableDemo extends Component
{
    public array $selectedIds = [];

    public array $rows = [
        ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
        ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
        ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com'],
    ];

    public function addRow(): void
    {
        $this->rows[] = ['id' => 4, 'name' => 'Diana', 'email' => 'diana@example.com'];
    }

    public function removeRow(): void
    {
        $this->rows = array_values(array_filter($this->rows, fn ($r) => $r['id'] !== 2));
    }

    public function reorderRows(): void
    {
        $this->rows = array_reverse($this->rows);
    }

    public function deleteSelected(): void
    {
        $this->rows = array_values(array_filter(
            $this->rows,
            fn ($r) => !in_array((string) $r['id'], $this->selectedIds),
        ));
        $this->selectedIds = [];
    }

    public function render(): View
    {
        return view('livewire.ui.table-demo');
    }
}
