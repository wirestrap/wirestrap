# Select

Custom select dropdown with single and multiple selection and optional search. Options can be passed directly as an array via `options`, or loaded from a Livewire renderless method via `wire-options`. `id` is required.

The list method must return an indexed array of option objects:

```php
#[Renderless]
public function getCountries(): array
{
    return [
        ['value' => 'fr', 'label' => 'France'],
        ['value' => 'de', 'label' => 'Germany', 'optgroup' => 'Europe'],
        ['value' => 'us', 'label' => 'United States', 'optgroup' => 'Americas', 'disabled' => true],
    ];
}
```

| Field | Type | Description |
|-------|------|-------------|
| `value` | `mixed` | Option value, cast to string internally. |
| `label` | `string` | Displayed text. Always rendered as plain text (XSS-safe). |
| `disabled` | `bool` | Prevents selection. Default `false`. |
| `optgroup` | `string\|null` | Group label. Options sharing the same value are grouped client-side. |
| `class` | `string\|null` | CSS class on the option element and selection item. |
| `html_prefix` | `string\|null` | Raw HTML rendered before the label. Combined with `label` and `html_suffix` as a single `innerHTML` — opening tags wrap the label naturally. **Never pass user-generated content without escaping it first.** |
| `html_suffix` | `string\|null` | Raw HTML rendered after the label. See `html_prefix`. **Same XSS warning applies.** |

## Usage

```blade
{{-- Static options: short form (value => label) or full form --}}
<x-wirestrap::select
    id="status-select"
    wire:model="status"
    label="Status"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    :options="[
        ['value' => 'fr', 'label' => 'France', 'optgroup' => 'Europe'],
        ['value' => 'xx', 'label' => 'Unavailable', 'disabled' => true],
    ]"
/>
```

```blade
{{-- wire-options: string = method name; array = [method, ...params] --}}
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    wire-options="getCountries"
    search
/>

{{-- Cascading select: resolve $this->selectedCountry at call time, reset city on change --}}
<x-wirestrap::select
    id="city-select"
    wire:model="city"
    label="City"
    :wire-options="['getCities', ['ws-wire' => 'selectedCountry'], 'active']"
    :wire-options-watch="['selectedCountry', null]"
/>
{{--
    wire-options-watch variants:
    'prop'              → cache invalidation only
    ['prop', val]       → + reset model to val on change
    ['prop', val, true] → + trigger a server round-trip on reset
--}}
```

```php
// html_prefix / html_suffix: raw HTML rendered around the label
#[Renderless]
public function getStatuses(): array
{
    return [
        ['value' => 'active',   'label' => 'Active',   'html_prefix' => '<strong class="text-success">', 'html_suffix' => '</strong>'],
        ['value' => 'inactive', 'label' => 'Inactive', 'html_prefix' => '<span class="text-muted">',     'html_suffix' => '</span>'],
    ];
}
```

```blade
{{-- Multiple selection (wire:model binds to an array) --}}
<x-wirestrap::select
    id="tags-select"
    wire:model="tags"
    label="Tags"
    wire-options="getTags"
    multiple
    live
/>

{{-- Teleport: escape overflow/stacking contexts --}}
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    wire-options="getCountries"
    teleport="body"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Unique identifier. Required (strict mode on by default). |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `options` | `array\|null` | `null` | Options passed as an array. Short form: `['value' => 'Label']`. Full form: `[['value' => ..., 'label' => ..., 'disabled' => ..., 'optgroup' => ..., 'class' => ..., 'html_prefix' => ..., 'html_suffix' => ...]]`. Reacts to Livewire re-renders automatically. Mutually exclusive with `wire-options`. |
| `wire-options` | `string\|list<mixed>\|null` | `null` | Livewire method returning the options array. String: method name. Array: `[method, ...params]` — remaining elements are passed as positional arguments. To pass a Livewire property resolved at call time, use `['ws-wire' => 'dotPath']` as a param. Called once on first open; cached. Called immediately on load if a value is already selected. |
| `wire-options-watch` | `string\|list<mixed>\|null` | `null` | Livewire property to watch. Invalidates options cache on change; reloads immediately if dropdown is open. Array form: `[property, resetValue]` or `[property, resetValue, liveReset]` — sets the model to `resetValue` when the watched property changes; `liveReset` (default `false`) triggers a server round-trip on reset. Requires `wire-options`. |
| `floating` | `bool` | `false` | Floating label layout. |
| `placeholder` | `string` | `config` | Text shown when no option is selected. |
| `search-placeholder` | `string` | `config` | Placeholder for the search input. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. Always placed at start. |
| `multiple` | `bool` | `config` | Multiple selection. `wire:model` must bind to an array. |
| `search` | `bool` | `config` | Search input inside dropdown. Filtering is client-side. |
| `live` | `bool` | `config` | Trigger server round-trip on each selection change. |
| `dropdown-offset` | `int` | `config` | Y-axis offset in pixels between the trigger and the dropdown. |
| `position` | `string` | `config` | Floating positioning strategy: `absolute` or `fixed`. |
| `teleport` | `string\|null` | `config` | CSS selector of the teleport target (e.g. `body`). Moves the dropdown outside the component's DOM tree, escaping all stacking contexts. Requires `id`. Prefer over `position="fixed"` when the dropdown is clipped by a parent. |
| `empty-value` | `mixed` | `config` | Value treated as "no selection" (shows placeholder). |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the root element. |

## JavaScript API

```js
$wirestrap.select.refresh('my-select-id')
```

Dispatches `ws:refresh` on the root element: discards cache and re-calls `wire-options` immediately. Only applies when `wire-options` is set.
