# Accordion

Collapsible content panels as named slots with animated height transitions.

## Usage

```blade
<x-wirestrap::accordion id="faq" single>
    <x-slot:shipping label="Shipping" icon="bi-truck" open>
        We ship to over 50 countries...
    </x-slot:shipping>

    <x-slot:returns label="Returns" icon="bi-arrow-return-left">
        Items can be returned within 30 days...
    </x-slot:returns>
</x-wirestrap::accordion>
```

```blade
{{-- External control via $wirestrap.accordion (requires events) --}}
<x-wirestrap::accordion id="main" events>
    <x-slot:details label="Details">...</x-slot:details>
</x-wirestrap::accordion>

<button type="button" x-on:click="$wirestrap.accordion.toggle('main', 'details')">
    Toggle details
</button>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required in strict mode (on by default). |
| `single` | `bool` | `config` | Opening an item closes all others. |
| `events` | `bool` | `config` | Enables `$wirestrap.accordion` Alpine magic helper. |
| `invalid-feedback` | `bool` | `config` | Highlights item buttons with validation errors after Livewire morph. |

## Slot attributes

Rich trigger: use a `label_{key}` slot alongside the item slot (overrides `label`).

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `label` | `string` | slot key | Button label text. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `open` | `bool` | `false` | Item expanded on initial render. |

## $wirestrap.accordion

| Method | Description |
|--------|-------------|
| `$wirestrap.accordion.show(id, key)` | Open item. |
| `$wirestrap.accordion.hide(id, key)` | Close item. |
| `$wirestrap.accordion.toggle(id, key)` | Toggle item. |
