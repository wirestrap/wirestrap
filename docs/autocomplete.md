# Autocomplete

Text input with suggestion dropdown and inline ghost text. Supports single-value and multi-tag modes; filtering is client-side.

`wire-options` names a Livewire method returning `list<string>`, called once on first focus and cached. `wire-options-watch` invalidates the cache when a Livewire property changes.

## Usage

```blade
{{-- wire-options: calls $this->getCities() on first focus, result cached --}}
<x-wirestrap::autocomplete
    wire:model="city"
    wire-options="getCities"
    label="City"
    placeholder="Type a city..."
    icon="bi-geo-alt"
    floating
/>
```

```blade
{{-- wire-options-watch: invalidates cache when $this->selectedCountry changes --}}
<x-wirestrap::autocomplete
    wire:model="city"
    wire-options="getCities"
    wire-options-watch="selectedCountry"
    label="City"
/>
```

```blade
{{-- Multiple mode: wire:model binds to an array property --}}
<x-wirestrap::autocomplete
    wire:model="tags"
    wire-options="getTechnologies"
    label="Technologies"
    multiple
    live
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Input id. Auto-resolved from wire:model when omitted. |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `wire-options` | `string\|null` | `null` | Livewire method returning `list<string>`. Called once on first focus; cached. |
| `wire-options-watch` | `string\|null` | `null` | Livewire property to watch. Invalidates cache on change; reloads immediately if dropdown is open. Requires `wire-options`. |
| `floating` | `bool` | `false` | Floating label layout. |
| `placeholder` | `string\|null` | `null` | Placeholder text. Forced to a space in floating mode. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `multiple` | `bool` | `false` | Tag mode. wire:model must bind to an array property. |
| `live` | `bool` | `false` | In multiple mode, sync to Livewire on each tag change. |
| `dropdown-offset` | `int` | `config` | Y-axis offset in pixels between the trigger and the dropdown. |
| `disabled` | `bool` | `false` | Disable the component. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |

## JavaScript API

```js
$wirestrap.autocomplete.refresh('my-autocomplete-id')
```

Dispatches `ws:refresh` on the root element: discards cache and re-calls `wire-options` immediately.
