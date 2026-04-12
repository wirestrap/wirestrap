# Table

Table with auto-truncating column headers (tooltip reveals full text on hover) and Livewire bulk row selection.

## Usage

```blade
<x-wirestrap::table
    :columns="[
        ['label' => 'Name', 'icon' => 'bi-person', 'icon-placement' => 'start'],
        'Email Address',
        ['label' => 'Actions', 'class' => 'text-end', 'truncate' => false],
    ]"
>
    @foreach ($users as $user)
        <tr wire:key="user-{{ $user->id }}">
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-end">...</td>
        </tr>
    @endforeach
</x-wirestrap::table>
```

```blade
{{-- Bulk selection with actions bar --}}
<x-wirestrap::table
    bulk="selectedIds"
    :bulk-actions="[
        ['label' => 'Delete', 'class' => 'btn btn-danger', 'wire:click' => 'deleteSelected'],
        ['label' => 'Export', 'class' => 'btn btn-secondary', 'wire:click' => 'export'],
    ]"
    :columns="['Name', 'Email']"
>
    @foreach ($users as $user)
        <tr wire:key="user-{{ $user->id }}">
            <x-wirestrap::table.check :value="$user->id" />
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
        </tr>
    @endforeach
</x-wirestrap::table>
```

## Props

### x-wirestrap::table

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `columns` | `array` | `[]` | Column definitions. Each item is a string (label only) or an array — see Column definition below. |
| `bulk` | `string\|null` | `null` | Livewire property name for bulk selection. Enables select-all checkbox and table.check. |
| `bulk-actions` | `slot\|array\|null` | `null` | Bulk actions bar. Array of button definitions or slot for custom content. Requires bulk. |
| `caption` | `slot\|string\|null` | `null` | Table caption. |
| `head` | `slot\|null` | `null` | Custom thead. Used when columns is not set. |
| `foot` | `slot\|null` | `null` | tfoot content. |
| `responsive` | `bool` | `config` | Wrap table in a responsive container. |
| `animate` | `bool` | `config` | Enables FLIP animations on row add/reorder and fade+collapse on row removal. Requires `wire:key` on rows. |
| `empty` | `string\|null` | `config` | Message shown when no rows. Null or empty string to disable. |

### Column definition

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `label` | `string` | `''` | Column header text. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `class` | `string` | `''` | Extra classes on the label. |
| `truncate` | `bool` | `true` | Truncates header and shows tooltip on hover. |
| `tooltip` | `string` | `null` | Custom tooltip text. Always shown when set. |
| `html` | `string` | `null` | Raw HTML appended inside th after the label. |
| `...` | `mixed` | `...` | Any other key is spread as an HTML attribute on th. |

### Bulk actions definition

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `label` | `string` | `null` | Button label. |
| `icon` | `string\|array` | `null` | Icon forwarded to the configured icon component. |
| `icon-placement` | `string` | `'start'` | Icon side: start or end. |
| `...` | `mixed` | `...` | Any other key is spread as an HTML attribute on button. |

### x-wirestrap::table.check

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `class-td` | `string` | `config` | Classes on the wrapping td. |
| `...` | `mixed` | `...` | All other attributes passed to the checkbox input. |
