# Flyout

Generic floating panel built on `@floating-ui/dom`. No semantic meaning — use for forms, navigation, previews, or any floating content.

## Usage

```blade
{{-- Click-triggered flyout with interactive content --}}
<x-wirestrap::flyout trigger="click" placement="bottom-start">
    <button type="button">Sign in</button>

    <x-slot:content>
        <x-wirestrap::input label="Email" type="email" floating />
        <x-wirestrap::input label="Password" password floating />
        <button type="button">Sign in</button>
    </x-slot:content>
</x-wirestrap::flyout>
```

```blade
{{-- Append to body to avoid overflow/z-index issues; external control --}}
<x-wirestrap::flyout id="action-menu" teleport="body" trigger="click">
    <button type="button">Actions</button>
    <x-slot:content>...</x-slot:content>
</x-wirestrap::flyout>

<button type="button" x-on:click="$wirestrap.flyout.toggle('action-menu')">Toggle</button>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required when using teleport or `$wirestrap.flyout`. |
| `content` | `slot\|string` | `''` | Panel content. Slot attributes are forwarded to the panel element. |
| `placement` | `string` | `config` | Preferred placement: top, bottom, left, right, and -start/-end variants. Flips if out of bounds. |
| `trigger` | `string` | `config` | Show trigger: hover or click. |
| `teleport` | `string\|null` | `config` | CSS selector of the element to teleport the panel into (e.g. `"body"`, `"#app"`). Avoids overflow/z-index issues. Requires id. |
| `offset-distance` | `int` | `config` | Distance between trigger and panel in px. |
| `offset-skidding` | `int` | `config` | Lateral offset in px. |
| `position` | `string` | `config` | CSS positioning strategy: absolute or fixed. |

## $wirestrap.flyout

| Method | Description |
|--------|-------------|
| `$wirestrap.flyout.show(id)` | Show flyout. |
| `$wirestrap.flyout.hide(id)` | Hide flyout. |
| `$wirestrap.flyout.toggle(id)` | Toggle flyout. |
