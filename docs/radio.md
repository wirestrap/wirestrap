# Radio

Radio button, always used in groups. Each option shares the same `wire:model` and carries its own `value`. `id` is auto-resolved as `{model}-{value}` (e.g. `plan-free`).

## Usage

```blade
<x-wirestrap::radio wire:model="plan" value="free" label="Free" />
<x-wirestrap::radio wire:model="plan" value="pro" label="Pro" />
<x-wirestrap::radio wire:model="plan" value="enterprise" label="Enterprise" />
```

```blade
{{-- Show error message only on the last option to avoid repetition --}}
<x-wirestrap::radio wire:model="plan" value="free" label="Free" :has-validation-message="false" />
<x-wirestrap::radio wire:model="plan" value="pro" label="Pro" :has-validation-message="false" />
<x-wirestrap::radio wire:model="plan" value="enterprise" label="Enterprise" />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Radio id. Auto-resolved from wire:model and value (e.g. plan-free). |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `reverse` | `bool` | `false` | Place radio on the right of the label. |
| `inline` | `bool` | `false` | Inline layout for side-by-side options. |
| `disabled` | `bool` | `false` | Disable this option. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. Typically false on all but the last option in a group. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |
