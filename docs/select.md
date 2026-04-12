# Select

Custom select dropdown with single and multiple selection and optional search. Options can be passed directly as an array via `options`, or loaded from a Livewire method via `wire-options`. `id` is required.

The list method must return an indexed array of option objects:

```php
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
| `html_prefix` | `string\|null` | Raw HTML rendered before the label. Combined with `label` and `html_suffix` as a single `innerHTML` — opening tags wrap the label naturally. |
| `html_suffix` | `string\|null` | Raw HTML rendered after the label. See `html_prefix`. |

## Usage

```blade
{{-- options: short syntax (value => label) --}}
<x-wirestrap::select
    id="status-select"
    wire:model="status"
    label="Status"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
```

```blade
{{-- options: full syntax (supports disabled, optgroup, class) --}}
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    :options="[
        ['value' => 'fr', 'label' => 'France', 'optgroup' => 'Europe'],
        ['value' => 'us', 'label' => 'United States', 'optgroup' => 'Americas'],
        ['value' => 'xx', 'label' => 'Unavailable', 'disabled' => true],
    ]"
/>
```

```blade
{{-- Basic single select with Livewire method --}}
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    wire-options="getCountries"
    search
/>
```

```blade
{{-- wire-options-watch: invalidates cache when $this->continent changes --}}
<x-wirestrap::select
    id="country-select"
    wire:model="country"
    label="Country"
    wire-options="getCountries"
    wire-options-watch="continent"
/>
```

```blade
{{-- html_prefix / html_suffix: raw HTML wrapping or decorating the label --}}
<x-wirestrap::select
    id="status-select"
    wire:model="status"
    label="Status"
    wire-options="getStatuses"
/>
```

```php
// In the Livewire component — html_prefix wraps the label, html_suffix closes it
public function getStatuses(): array
{
    return [
        ['value' => 'active',   'label' => 'Active',   'html_prefix' => '<strong class="text-success">', 'html_suffix' => '</strong>'],
        ['value' => 'inactive', 'label' => 'Inactive', 'html_prefix' => '<span class="text-muted">',     'html_suffix' => '</span>'],
    ];
}
```

```blade
{{-- Multiple selection: wire:model must bind to an array property --}}
<x-wirestrap::select
    id="tags-select"
    wire:model="tags"
    label="Tags"
    wire-options="getTags"
    multiple
    live
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Unique identifier. Required (strict mode on by default). |
| `label` | `slot\|string\|null` | `null` | Label text or slot content. |
| `options` | `array\|null` | `null` | Options passed as an array. Short form: `['value' => 'Label']`. Full form: `[['value' => ..., 'label' => ..., 'disabled' => ..., 'optgroup' => ..., 'class' => ..., 'html_prefix' => ..., 'html_suffix' => ...]]`. Reacts to Livewire re-renders automatically. Mutually exclusive with `wire-options`. |
| `wire-options` | `string\|null` | `null` | Livewire method returning the options array. Called once on first open; cached. Called immediately on load if a value is already selected. |
| `wire-options-watch` | `string\|null` | `null` | Livewire property to watch. Invalidates cache on change; reloads immediately if dropdown is open. Requires `wire-options`. |
| `floating` | `bool` | `false` | Floating label layout. |
| `placeholder` | `string` | `config` | Text shown when no option is selected. |
| `search-placeholder` | `string` | `config` | Placeholder for the search input. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. Always placed at start. |
| `multiple` | `bool` | `config` | Multiple selection. `wire:model` must bind to an array. |
| `search` | `bool` | `config` | Search input inside dropdown. Filtering is client-side. |
| `live` | `bool` | `config` | Trigger server round-trip on each selection change. |
| `dropdown-offset` | `int` | `config` | Y-axis offset in pixels between the trigger and the dropdown. |
| `empty-value` | `mixed` | `config` | Value treated as "no selection" (shows placeholder). |
| `has-validation` | `bool` | `config` | Adds invalid class when wire:model has an error. |
| `has-validation-message` | `bool` | `config` | Renders error message below. |
| `default-attributes` | `array` | `config` | Attributes merged onto the root element. |

## JavaScript API

```js
$wirestrap.select.refresh('my-select-id')
```

Dispatches `ws:refresh` on the root element: discards cache and re-calls `wire-options` immediately. Only applies when `wire-options` is set.
