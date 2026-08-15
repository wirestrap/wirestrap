<?php

namespace Tests\Browser\Livewire\Form;

use Illuminate\View\View;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class SelectDemo extends Component
{
    public ?string $single = null;
    public array $multiple = [];
    public ?string $search = null;
    public ?string $disabled = null;
    public ?string $withDisabledOpt = null;
    public ?string $staticSelect = null;
    public ?string $emptyVal = '0';
    public ?string $keyboard = null;
    public ?string $preloaded = 'green';
    public ?string $rich = null;
    public ?string $reactive = null;
    public ?string $accented = null;
    public ?string $grouped = null;
    public array $multipleSort = [];
    public ?string $classed = null;
    public ?string $xss = null;
    public bool $extendOptions = false;

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getOptions(): array
    {
        return [
            ['value' => 'red', 'label' => 'Red'],
            ['value' => 'green', 'label' => 'Green'],
            ['value' => 'blue', 'label' => 'Blue'],
        ];
    }

    /** @return list<array{value: string, label: string, disabled?: bool}> */
    #[Renderless]
    public function getOptionsWithDisabled(): array
    {
        return [
            ['value' => 'red', 'label' => 'Red'],
            ['value' => 'green', 'label' => 'Green', 'disabled' => true],
            ['value' => 'blue', 'label' => 'Blue'],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getEmptyableOptions(): array
    {
        return [
            ['value' => '0', 'label' => 'All'],
            ['value' => 'red', 'label' => 'Red'],
            ['value' => 'blue', 'label' => 'Blue'],
        ];
    }

    /** @return list<array{value: string, label: string, html_prefix?: string, html_suffix?: string}> */
    #[Renderless]
    public function getRichOptions(): array
    {
        return [
            ['value' => 'alice', 'label' => 'Alice', 'html_prefix' => '<i class="bi bi-person"></i>', 'html_suffix' => '<span class="ws-test-badge">Admin</span>'],
            ['value' => 'bob', 'label' => 'Bob', 'html_prefix' => '<i class="bi bi-person"></i>'],
            ['value' => 'charlie', 'label' => 'Charlie'],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getAccentedOptions(): array
    {
        return [
            ['value' => 'fr', 'label' => 'François'],
            ['value' => 'de', 'label' => 'Günter'],
            ['value' => 'es', 'label' => 'José'],
            ['value' => 'en', 'label' => 'John'],
        ];
    }

    /** @return list<array{value: string, label: string, optgroup?: string}> */
    #[Renderless]
    public function getGroupedOptions(): array
    {
        return [
            ['value' => 'water', 'label' => 'Water'],
            ['value' => 'apple', 'label' => 'Apple', 'optgroup' => 'Fruits'],
            ['value' => 'banana', 'label' => 'Banana', 'optgroup' => 'Fruits'],
            ['value' => 'carrot', 'label' => 'Carrot', 'optgroup' => 'Vegetables'],
        ];
    }

    /** @return list<array{value: string, label: string, class?: string}> */
    #[Renderless]
    public function getClassedOptions(): array
    {
        return [
            ['value' => 'info', 'label' => 'Info', 'class' => 'text-info'],
            ['value' => 'danger', 'label' => 'Danger', 'class' => 'text-danger'],
            ['value' => 'plain', 'label' => 'Plain'],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    #[Renderless]
    public function getXssOptions(): array
    {
        return [
            ['value' => 'xss', 'label' => '<script>alert("xss")</script>'],
            ['value' => 'safe', 'label' => 'Safe'],
        ];
    }

    /** @return array<string, string> */
    public function getReactiveOptions(): array
    {
        $options = ['fr' => 'France', 'de' => 'Germany'];

        if ($this->extendOptions) {
            $options['es'] = 'Spain';
            $options['it'] = 'Italy';
        }

        return $options;
    }

    public function addMoreOptions(): void
    {
        $this->extendOptions = true;
    }

    public function render(): View
    {
        return view('livewire.form.select-demo');
    }
}
