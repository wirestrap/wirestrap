<div>
    {{-- Subscribed, with a value: reloads immediately --}}
    <x-wirestrap::select
        id="select-with-value-{{ $suffix }}"
        wire:model="selectWithValue"
        :wire-options="['getColors', 'selectWithValue']"
        refresh-keys="colors"
        label="Subscribed"
    />

    {{-- Subscribed, without a value: reloads on next open --}}
    <x-wirestrap::select
        id="select-empty-{{ $suffix }}"
        wire:model="selectEmpty"
        :wire-options="['getColors', 'selectEmpty']"
        :refresh-keys="['other', 'colors']"
        label="Subscribed empty"
    />

    {{-- Autocomplete subscribed to the same key: select refreshes must not reach it --}}
    <x-wirestrap::autocomplete
        id="autocomplete-with-tags-{{ $suffix }}"
        wire:model="autocompleteWithTags"
        :wire-options="['getColors', 'autocompleteWithTags']"
        refresh-keys="colors"
        label="Autocomplete"
        multiple
    />

    {{-- Subscribed to its own id --}}
    <x-wirestrap::select
        id="select-own-id-key-{{ $suffix }}"
        wire:model="selectOwnIdKey"
        :wire-options="['getColors', 'selectOwnIdKey']"
        refresh-keys="select-own-id-key-{{ $suffix }}"
        label="Self"
    />

    {{-- Not subscribed --}}
    <x-wirestrap::select
        id="select-unsubscribed-{{ $suffix }}"
        wire:model="selectUnsubscribed"
        :wire-options="['getColors', 'selectUnsubscribed']"
        label="Plain"
    />

    <button id="btn-create-{{ $suffix }}" type="button" wire:click="createColor">Create</button>
</div>
