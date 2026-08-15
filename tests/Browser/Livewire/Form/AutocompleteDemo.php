<?php

namespace Tests\Browser\Livewire\Form;

use Illuminate\View\View;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class AutocompleteDemo extends Component
{
    public ?string $single = null;
    public array $tags = [];
    public ?string $search = null;
    public ?string $disabled = null;
    public ?string $grouped = null;
    public ?string $rich = null;
    public ?string $classed = null;
    public ?string $xss = null;
    public ?string $accented = null;
    public ?string $ghost = null;
    public array $preloaded = ['php', 'js'];
    public ?string $minChars = null;
    public ?string $minChars1 = null;
    public ?string $category = 'fruits';
    public ?string $watchedItem = null;
    public array $noDropdownTags = [];
    public array $emails = [];

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getSuggestions(): array
    {
        return [
            ['value' => 'red', 'label' => 'Red'],
            ['value' => 'green', 'label' => 'Green'],
            ['value' => 'blue', 'label' => 'Blue'],
        ];
    }

    /** @return list<array{value: string, label: string, optgroup?: string}> */
    #[Renderless]
    public function getGroupedSuggestions(): array
    {
        return [
            ['value' => 'water', 'label' => 'Water'],
            ['value' => 'apple', 'label' => 'Apple', 'optgroup' => 'Fruits'],
            ['value' => 'banana', 'label' => 'Banana', 'optgroup' => 'Fruits'],
            ['value' => 'carrot', 'label' => 'Carrot', 'optgroup' => 'Vegetables'],
        ];
    }

    /** @return list<array{value: string, label: string, html_prefix?: string, html_suffix?: string}> */
    #[Renderless]
    public function getRichSuggestions(): array
    {
        return [
            ['value' => 'alice', 'label' => 'Alice', 'html_prefix' => '<i class="bi bi-person"></i>', 'html_suffix' => '<span class="ws-test-badge">Admin</span>'],
            ['value' => 'bob', 'label' => 'Bob', 'html_prefix' => '<i class="bi bi-person"></i>'],
            ['value' => 'charlie', 'label' => 'Charlie'],
        ];
    }

    /** @return list<array{value: string, label: string, class?: string}> */
    #[Renderless]
    public function getClassedSuggestions(): array
    {
        return [
            ['value' => 'info', 'label' => 'Info', 'class' => 'text-info'],
            ['value' => 'danger', 'label' => 'Danger', 'class' => 'text-danger'],
            ['value' => 'plain', 'label' => 'Plain'],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getXssSuggestions(): array
    {
        return [
            ['value' => 'xss', 'label' => '<script>alert("xss")</script>'],
            ['value' => 'safe', 'label' => 'Safe'],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getAccentedSuggestions(): array
    {
        return [
            ['value' => 'francois', 'label' => 'François'],
            ['value' => 'gunter', 'label' => 'Günter'],
            ['value' => 'jose', 'label' => 'José'],
            ['value' => 'john', 'label' => 'John'],
        ];
    }

    /** @return list<string> */
    #[Renderless]
    public function getGhostSuggestions(): array
    {
        return ['apple', 'application', 'banana', 'blueberry'];
    }

    /** @return list<string> */
    #[Renderless]
    public function getTagSuggestions(): array
    {
        return ['php', 'js', 'python', 'rust', 'go'];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getCategoryItems(string $category): array
    {
        return match ($category) {
            'fruits' => [
                ['value' => 'apple', 'label' => 'Apple'],
                ['value' => 'banana', 'label' => 'Banana'],
            ],
            'vegetables' => [
                ['value' => 'carrot', 'label' => 'Carrot'],
                ['value' => 'potato', 'label' => 'Potato'],
            ],
            default => [],
        };
    }

    public function validateEmails(): void
    {
        $this->validate([
            'emails.*' => 'email',
        ]);
    }

    public function render(): View
    {
        return view('livewire.form.autocomplete-demo');
    }
}
