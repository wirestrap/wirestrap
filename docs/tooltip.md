# Tooltip

Floating tooltip built on `@floating-ui/dom`. Wraps any trigger element. Focusing an element inside the slot keeps the tooltip open.

## Usage

```blade
<x-wirestrap::tooltip content="Save your changes">
    <button type="button">Save</button>
</x-wirestrap::tooltip>
```

```blade
{{-- Rich content via slot; click trigger --}}
<x-wirestrap::tooltip id="info-tip" placement="right" trigger="click">
    <span>?</span>

    <x-slot:content>
        <strong>Required.</strong> Must be unique across the workspace.
    </x-slot:content>
</x-wirestrap::tooltip>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required for `$wirestrap.tooltip` control. |
| `content` | `slot\|string` | `''` | Tooltip text. Use the content slot for HTML. |
| `placement` | `string` | `config` | Preferred placement: top, bottom, left, right, and -start/-end variants. Flips if out of bounds. |
| `trigger` | `string` | `config` | Show trigger: hover or click. |
| `arrow` | `bool` | `true` | Show directional arrow. |
| `interactive` | `bool` | `false` | When true, hovering the tooltip panel keeps it visible. |
| `teleport` | `string\|null` | `config` | CSS selector of the element to teleport the tooltip into (e.g. `"body"`, `"#app"`). Avoids overflow/z-index issues. |
| `offset-distance` | `int` | `config` | Distance between trigger and tooltip in px. |
| `offset-skidding` | `int` | `config` | Lateral offset in px. |
| `position` | `string` | `config` | CSS positioning strategy: absolute or fixed. |

## Dismiss

Any element carrying `data-ws-dismiss="floating"` inside the tooltip closes it when clicked. No id required — the owning element is resolved from the DOM, teleported tooltips included.

Use `data-ws-dismiss="floating-stack"` instead to also close every floating element enclosing it.

## $wirestrap.tooltip

| Method | Description |
|--------|-------------|
| `$wirestrap.tooltip.show(id)` | Show tooltip. |
| `$wirestrap.tooltip.hide(id)` | Hide tooltip. |
| `$wirestrap.tooltip.toggle(id)` | Toggle tooltip. |
