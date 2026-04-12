# Switch

Toggle switch for boolean on/off state. Identical API to checkbox, with `role="switch"` set automatically.

## Usage

```blade
<x-wirestrap::switch wire:model="notifications" label="Enable notifications" />

{{-- Rich label via default slot --}}
<x-wirestrap::switch wire:model="marketing">
    Receive <a href="/privacy">marketing emails</a>
</x-wirestrap::switch>
```

```blade
<x-wirestrap::switch wire:model="darkMode" label="Dark mode" reverse />

<x-wirestrap::switch wire:model="emails" label="Emails" inline />
<x-wirestrap::switch wire:model="sms"    label="SMS"    inline />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Switch id. Auto-resolved from wire:model, with value appended when set. |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `reverse` | `bool` | `false` | Place switch on the right of the label. |
| `inline` | `bool` | `false` | Inline layout for side-by-side switches. |
| `disabled` | `bool` | `false` | Disable the switch. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |
