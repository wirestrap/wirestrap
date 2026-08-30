<div>
    <x-wirestrap::table
        :columns="['Name', 'Email']"
        wire-model-bulk="selectedIds"
        :bulk-actions="[
            ['label' => 'Delete', 'wire:click' => 'deleteSelected'],
        ]"
        animate
    >
        @foreach ($rows as $row)
            <tr wire:key="row-{{ $row['id'] }}">
                <x-wirestrap::table.check :value="$row['id']" :disabled="$row['id'] === 3" />
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['email'] }}</td>
            </tr>
        @endforeach
    </x-wirestrap::table>

    <button id="btn-add-row" type="button" wire:click="addRow">Add row</button>
    <button id="btn-remove-row" type="button" wire:click="removeRow">Remove row</button>
    <button id="btn-reorder" type="button" wire:click="reorderRows">Reorder</button>
    <span id="selected-count">{{ count($selectedIds) }}</span>
</div>
