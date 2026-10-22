<div x-on:entity-deleted.window="$wirestrap.tabs.show('tabs-entities', $event.detail.key)">
    <x-wirestrap::tabs id="tabs-entities" :events="true">
        @foreach ($entities as $key => $label)
            <x-slot :name="$key" :label="$label">
                {{ $label }} content
                <button id="btn-delete-{{ $key }}" type="button" wire:click="delete('{{ $key }}')">Delete</button>
            </x-slot>
        @endforeach
    </x-wirestrap::tabs>
</div>
