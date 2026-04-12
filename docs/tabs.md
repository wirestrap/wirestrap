# Tabs

Tab navigation with animated panel transitions. Each tab is a named slot; the slot name serves as the tab key.

## Usage

```blade
<x-wirestrap::tabs id="tabs" default="settings">
    <x-slot:overview label="Overview">
        ...
    </x-slot:overview>

    <x-slot:settings label="Settings" icon="bi-gear">
        ...
    </x-slot:settings>
</x-wirestrap::tabs>
```

```blade
{{-- External control (requires events) --}}
<x-wirestrap::tabs id="form-tabs" invalid-feedback events>
    <x-slot:identity label="Identity">
        <x-wirestrap::input label="First name" wire:model="firstName" />
    </x-slot:identity>

    <x-slot:security label="Security">
        <x-wirestrap::input label="Password" password wire:model="password" />
    </x-slot:security>
</x-wirestrap::tabs>

<button type="button" x-on:click="$wirestrap.tabs.show('form-tabs', 'security')">
    Go to Security
</button>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required in strict mode (on by default). |
| `default` | `string\|null` | `null` | Key of the tab active on render. Defaults to the first tab. |
| `events` | `bool` | `config` | Enables `$wirestrap.tabs` Alpine magic helper. |
| `invalid-feedback` | `bool` | `config` | Highlights tab buttons with validation errors after Livewire morph. |

## Slot attributes

Rich button: use a `label_{key}` slot alongside the tab slot (overrides `label`).

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `label` | `string` | slot key | Tab button label. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |

## $wirestrap.tabs

| Method | Description |
|--------|-------------|
| `$wirestrap.tabs.show(id, key)` | Switch to tab. |
