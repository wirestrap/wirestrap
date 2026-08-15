<div>
    <x-wirestrap::accordion id="acc-valid" :invalid-feedback="true">
        <x-slot:profile label="Profile">
            <x-wirestrap::input label="Name" wire:model="name" />
        </x-slot:profile>
        <x-slot:settings label="Settings">
            <p>No fields here</p>
        </x-slot:settings>
    </x-wirestrap::accordion>

    <button id="btn-save" type="button" wire:click="save">Save</button>
</div>
