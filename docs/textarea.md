# Textarea

Textarea with label, floating label, icon, and Livewire validation. Auto-resize is on by default — height adjusts on keystroke and Livewire model updates.

## Usage

```blade
<x-wirestrap::textarea wire:model="description" label="Description" />
<x-wirestrap::textarea wire:model="bio" label="Bio" floating icon="bi-chat" />
```

```blade
{{-- Fixed height, no auto-resize --}}
<x-wirestrap::textarea wire:model="notes" label="Notes" :autosize="false" :rows="5" />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Textarea id. Auto-resolved from wire:model when omitted. |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `floating` | `bool` | `false` | Floating label layout. Placeholder auto-set to a space. |
| `placeholder` | `string\|null` | `null` | Placeholder text. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `rows` | `int\|null` | `config` | Initial row count. |
| `autosize` | `bool` | `config` | Auto-resize height to fit content. Also reacts to Livewire model updates. |
| `disabled` | `bool` | `false` | Disable the textarea. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the textarea element. |
