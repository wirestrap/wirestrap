<div>
    <x-wirestrap::tabs id="tabs-valid" :invalid-feedback="true">
        <x-slot:profile label="Profile">
            <x-wirestrap::input label="Name" wire:model="name" />
        </x-slot:profile>
        <x-slot:other label="Other">
            <p>No fields here</p>
        </x-slot:other>
    </x-wirestrap::tabs>

    <button id="btn-tabs-save" type="button" wire:click="save">Save</button>
</div>
