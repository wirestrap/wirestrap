<x-layouts.app>
    <div class="p-4">

        {{-- Basic flyout with content prop --}}
        <x-wirestrap::flyout id="flyout-basic" content="Flyout content">
            <button id="trigger-basic" type="button">Hover flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Click trigger flyout --}}
        <x-wirestrap::flyout id="flyout-click" content="Click flyout content" trigger="click">
            <button id="trigger-click" type="button">Click flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Interactive flyout (default) --}}
        <x-wirestrap::flyout id="flyout-interactive">
            <x-slot:content>
                <div id="flyout-panel" style="padding: 20px;">Interactive panel content</div>
            </x-slot:content>
            <button id="trigger-interactive" type="button">Interactive flyout</button>
        </x-wirestrap::flyout>

        {{-- Dismiss via data attribute --}}
        <x-wirestrap::flyout id="flyout-dismiss" trigger="click">
            <x-slot:content>
                <div style="padding: 20px;">
                    <button id="btn-dismiss" type="button" data-ws-dismiss="floating">Close</button>
                    <button id="btn-keep" type="button">Keep open</button>
                    <button id="btn-dismiss-stack" type="button" data-ws-dismiss="floating-stack">Close the stack</button>
                </div>
            </x-slot:content>
            <button id="trigger-dismiss" type="button">Dismiss flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Dismiss from a teleported panel --}}
        <x-wirestrap::flyout id="flyout-dismiss-teleport" trigger="click" teleport="body">
            <x-slot:content class="panel-teleport">
                <div style="padding: 20px;">
                    <button id="btn-dismiss-teleported" type="button" data-ws-dismiss="floating">Close</button>
                </div>
            </x-slot:content>
            <button id="trigger-dismiss-teleport" type="button">Dismiss teleported flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Teleported --}}
        <x-wirestrap::flyout teleport="body" trigger="click">
            <x-slot:content class="panel-noattr">
                <div style="padding: 20px;">
                    <button id="teleport-dismiss" type="button" data-ws-dismiss="floating">Close</button>
                </div>
            </x-slot:content>
            <button id="teleport-trigger" type="button">Teleported flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        <livewire:ui.floating-morph lazy />

        {{-- Spacer to allow clicking away --}}
        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here to dismiss</div>
    </div>
</x-layouts.app>
