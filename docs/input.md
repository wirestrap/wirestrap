# Input

Text input with label, floating label, icon, password toggle, and Livewire validation. `id` is auto-resolved from `wire:model`.

## Usage

```blade
<x-wirestrap::input wire:model="email" label="Email" type="email" />
<x-wirestrap::input wire:model="username" label="Username" floating />
<x-wirestrap::input wire:model="search" icon="bi-search" label="Search" floating />
```

```blade
{{-- Password toggle; type is ignored when password is set --}}
<x-wirestrap::input wire:model="password" label="Password" password />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Input id. Auto-resolved from wire:model (dots → dashes). |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `floating` | `bool` | `false` | Floating label layout. Placeholder auto-set to a space. |
| `type` | `string` | `'text'` | Input type. Ignored when password is set. |
| `placeholder` | `string\|null` | `null` | Placeholder text. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `disabled` | `bool` | `false` | Disable the input. |
| `password` | `bool` | `false` | Add show/hide password toggle. Overrides type. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |
