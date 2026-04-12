# Menu

Navigation container with collapsible groups. Items are free HTML as named slots; groups use the `menu.accordion` sub-component.

## Usage

```blade
<x-wirestrap::menu id="nav" single events>
    <x-slot:dashboard>
        <a href="#">Dashboard</a>
    </x-slot:dashboard>

    <x-slot:settings_group>
        <x-wirestrap::menu.accordion id="settings" label="Settings" open>
            <x-slot:general><a href="#">General</a></x-slot:general>
            <x-slot:security><a href="#">Security</a></x-slot:security>
        </x-wirestrap::menu.accordion>
    </x-slot:settings_group>
</x-wirestrap::menu>
```

```blade
{{-- Rich trigger (icon + label + badge) --}}
<x-wirestrap::menu.accordion id="reports">
    <x-slot:trigger>
        <i class="bi bi-bar-chart"></i>
        <span>Reports</span>
        <span class="badge ms-auto">3</span>
    </x-slot:trigger>

    <x-slot:monthly><a href="#">Monthly</a></x-slot:monthly>
    <x-slot:annual><a href="#">Annual</a></x-slot:annual>
</x-wirestrap::menu.accordion>
```

## Props

### x-wirestrap::menu

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required in strict mode (on by default). |
| `single` | `bool` | `config` | Opening a group closes all others. |
| `events` | `bool` | `config` | Enables `$wirestrap.menu` Alpine magic helper. |

### x-wirestrap::menu.accordion

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Required. Group key for state and external control. |
| `label` | `string\|null` | `null` | Trigger button text. Falls back to id. Overridden by trigger slot. |
| `open` | `bool` | `false` | Group expanded on initial render. |

## $wirestrap.menu

| Method | Description |
|--------|-------------|
| `$wirestrap.menu.show(id, key)` | Open group. |
| `$wirestrap.menu.hide(id, key)` | Close group. |
| `$wirestrap.menu.toggle(id, key)` | Toggle group. |
