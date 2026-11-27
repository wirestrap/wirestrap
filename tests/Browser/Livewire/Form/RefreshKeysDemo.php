<?php

namespace Tests\Browser\Livewire\Form;

use Illuminate\View\View;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class RefreshKeysDemo extends Component
{
    public string $suffix = '';
    public ?string $selectWithValue = 'red';
    public ?string $selectEmpty = null;
    public array $autocompleteWithTags = ['red'];
    public ?string $selectOwnIdKey = 'red';
    public ?string $selectUnsubscribed = 'red';

    /** @var array<string, int> */
    public array $calls = [];

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getColors(string $field): array
    {
        $this->calls[$field] = ($this->calls[$field] ?? 0) + 1;

        return [
            ['value' => 'red', 'label' => 'Red'],
            ['value' => 'green', 'label' => 'Green'],
            ['value' => 'blue', 'label' => 'Blue'],
        ];
    }

    public function createColor(): void
    {
        $this->js("\$wirestrap.select.refresh('colors')");
    }

    public function render(): View
    {
        return view('livewire.form.refresh-keys-demo');
    }
}
