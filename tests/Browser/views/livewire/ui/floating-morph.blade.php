<div>
    <button id="btn-bump" type="button" wire:click="bump">Re-render</button>
    <span id="bump-count">{{ $count }}</span>

    {{-- Teleported flyout without any id --}}
    <x-wirestrap::flyout teleport="body" trigger="click">
        <x-slot:content class="morph-panel">
            <div style="padding: 20px;">
                <button id="morph-dismiss" type="button" data-ws-dismiss="floating">Close</button>
            </div>
        </x-slot:content>
        <button id="morph-trigger" type="button">Open</button>
    </x-wirestrap::flyout>
</div>
