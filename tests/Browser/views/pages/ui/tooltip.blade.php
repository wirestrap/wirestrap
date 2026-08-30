<x-layouts.app>
    <div class="p-4" style="padding-top: 100px;">

        {{-- Basic tooltip with content prop --}}
        <x-wirestrap::tooltip id="tip-basic" content="Basic tooltip text">
            <button id="trigger-basic" type="button">Hover me</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Tooltip with content slot --}}
        <x-wirestrap::tooltip id="tip-slot">
            <x-slot:content>Slot tooltip text</x-slot:content>
            <button id="trigger-slot" type="button">Slot trigger</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Tooltip without arrow --}}
        <x-wirestrap::tooltip id="tip-no-arrow" content="No arrow tooltip" :arrow="false">
            <button id="trigger-no-arrow" type="button">No arrow</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Click trigger tooltip --}}
        <x-wirestrap::tooltip id="tip-click" content="Click tooltip text" trigger="click">
            <button id="trigger-click" type="button">Click me</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Non-interactive tooltip (default) --}}
        <x-wirestrap::tooltip id="tip-noninteractive" content="Non-interactive tooltip">
            <button id="trigger-noninteractive" type="button">Non-interactive</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Interactive tooltip --}}
        <x-wirestrap::tooltip id="tip-interactive" content="Interactive tooltip" :interactive="true">
            <button id="trigger-interactive" type="button">Interactive</button>
        </x-wirestrap::tooltip>

        <br><br>

        {{-- Focus behavior: tooltip wrapping an input --}}
        <x-wirestrap::tooltip id="tip-focus" placement="right">
            <input id="trigger-focus" type="text" placeholder="Focus me">
            <x-slot:content>Focus tooltip content</x-slot:content>
        </x-wirestrap::tooltip>

        {{-- Spacer to allow clicking away --}}
        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here to dismiss</div>
    </div>
</x-layouts.app>
