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

        {{-- Spacer to allow clicking away --}}
        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here to dismiss</div>
    </div>
</x-layouts.app>
