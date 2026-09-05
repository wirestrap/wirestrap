# Popover

Floating panel with header and body, built on `@floating-ui/dom`. Flips and shifts to stay within the viewport.

## Usage

```blade
<x-wirestrap::popover>
    <button type="button">More info</button>

    <x-slot:header>Details</x-slot:header>
    <x-slot:content>Here is some additional context.</x-slot:content>
</x-wirestrap::popover>
```

```blade
{{-- Click trigger, teleport to body --}}
<x-wirestrap::popover id="user-card" trigger="click" placement="bottom" teleport="body">
    <span>Target</span>

    <x-slot:header>Status</x-slot:header>
    <x-slot:content>Last updated: <strong>today at 14:32</strong></x-slot:content>
</x-wirestrap::popover>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required when using teleport or `$wirestrap.popover`. |
| `header` | `slot\|string` | `''` | Header text. Use the header slot for HTML. |
| `content` | `slot\|string` | `''` | Body text. Use the content slot for HTML. |
| `placement` | `string` | `config` | Preferred placement: top, bottom, left, right, and -start/-end variants. Flips if out of bounds. |
| `trigger` | `string` | `config` | Show trigger: hover or click. |
| `arrow` | `bool` | `true` | Show directional arrow. |
| `interactive` | `bool` | `true` | When true, hovering the popover panel keeps it visible. |
| `teleport` | `string\|null` | `config` | CSS selector of the element to teleport the popover into (e.g. `"body"`, `"#app"`). Avoids overflow/z-index issues. Requires id. |
| `offset-distance` | `int` | `config` | Distance between trigger and popover in px. |
| `offset-skidding` | `int` | `config` | Lateral offset in px. |
| `position` | `string` | `config` | CSS positioning strategy: absolute or fixed. |

## Dismiss

Any element carrying `data-ws-dismiss="floating"` inside the panel closes it when clicked. No id required — the owning element is resolved from the DOM, teleported panels included. See [flyout](flyout.md#dismiss) for an example.

## $wirestrap.popover

| Method | Description |
|--------|-------------|
| `$wirestrap.popover.show(id)` | Show popover. |
| `$wirestrap.popover.hide(id)` | Hide popover. |
| `$wirestrap.popover.toggle(id)` | Toggle popover. |
