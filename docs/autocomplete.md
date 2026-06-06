# Autocomplete

Text input with suggestion dropdown and inline ghost text. Supports single-value and multi-tag modes; filtering is client-side.

`wire-options` names a Livewire method returning `list<string>`, called once on first focus and cached. `wire-options-watch` invalidates the cache when a Livewire property changes.

## Usage

```blade
{{-- wire-options: string = method name; array = [method, ...params] --}}
<x-wirestrap::autocomplete
    wire:model="city"
    wire-options="getCities"
    label="City"
    placeholder="Type a city..."
    icon="bi-geo-alt"
    floating
/>

{{-- Cascading: pass $this->selectedCountry at call time, reset city on country change --}}
<x-wirestrap::autocomplete
    wire:model="city"
    :wire-options="['getCities', ['ws-wire' => 'selectedCountry']]"
    :wire-options-watch="['selectedCountry', null]"
    label="City"
/>
{{-- wire-options-watch variants:
     'prop'              → cache invalidation only
     ['prop', val]       → + reset model to val on change
     ['prop', val, true] → + trigger a server round-trip on reset --}}
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

```blade
{{-- teleport: moves the dropdown to body, escaping overflow and stacking contexts --}}
<x-wirestrap::autocomplete
    id="city-input"
    wire:model="city"
    wire-options="getCities"
    label="City"
    teleport="body"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Input id. Auto-resolved from wire:model when omitted. |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `wire-options` | `string\|list<mixed>\|null` | `null` | Livewire method returning `list<string>`. String: method name. Array: `[method, ...params]` — remaining elements are passed as positional arguments; use `['ws-wire' => 'dotPath']` to resolve a Livewire property at call time. Called once on first focus; cached. |
| `wire-options-watch` | `string\|list<mixed>\|null` | `null` | Livewire property to watch. Invalidates cache on change; reloads immediately if dropdown is open. Array form `[property, resetValue]` also resets the model on change; third element `true` triggers a server round-trip on reset. Requires `wire-options`. |
| `floating` | `bool` | `false` | Floating label layout. |
| `placeholder` | `string\|null` | `null` | Placeholder text. Forced to a space in floating mode. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `multiple` | `bool` | `false` | Tag mode. wire:model must bind to an array property. |
| `live` | `bool` | `false` | In multiple mode, sync to Livewire on each tag change. |
| `dropdown-offset` | `int` | `config` | Y-axis offset in pixels between the trigger and the dropdown. |
| `position` | `string` | `config` | Floating positioning strategy: `absolute` or `fixed`. |
| `teleport` | `string\|null` | `config` | CSS selector of the teleport target (e.g. `body`). Moves the dropdown outside the component's DOM tree, escaping all stacking contexts. Requires `id`. Prefer over `position="fixed"` when the dropdown is clipped by a parent. |
| `disabled` | `bool` | `false` | Disable the component. |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the input element. |

## JavaScript API

```js
$wirestrap.autocomplete.refresh('my-autocomplete-id')
```

Dispatches `ws:refresh` on the root element: discards cache and re-calls `wire-options` immediately.
