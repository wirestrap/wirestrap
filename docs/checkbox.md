# Checkbox

Checkbox with label, inline/reverse layout, and Livewire validation feedback. `id` is auto-resolved from `wire:model` and `value`.

## Usage

```blade
<x-wirestrap::checkbox wire:model="agreeToTerms" label="I agree to the terms" />

{{-- Rich label via default slot --}}
<x-wirestrap::checkbox wire:model="terms">
    I agree to the <a href="/terms">terms of service</a>
</x-wirestrap::checkbox>
```

```blade
{{-- Inline group --}}
<x-wirestrap::checkbox wire:model="interests" value="music" label="Music" inline />
<x-wirestrap::checkbox wire:model="interests" value="sports" label="Sports" inline />
<x-wirestrap::checkbox wire:model="interests" value="tech" label="Tech" inline />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Checkbox id. Auto-resolved from wire:model, with value appended when set. |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `reverse` | `bool` | `false` | Place checkbox on the right of the label. |
| `inline` | `bool` | `false` | Inline layout for side-by-side checkboxes. |
| `disabled` | `bool` | `false` | Disable the checkbox. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |
