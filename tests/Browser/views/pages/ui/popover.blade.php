<x-layouts.app>
    <div class="p-4" style="padding-top: 100px;">

        {{-- Basic popover with header and content --}}
        <x-wirestrap::popover id="pop-full" header="Popover title" content="Popover body text">
            <button id="trigger-full" type="button">Full popover</button>
        </x-wirestrap::popover>

        <br><br>

        {{-- Popover without header --}}
        <x-wirestrap::popover id="pop-no-header" content="Body only text">
            <button id="trigger-no-header" type="button">No header</button>
        </x-wirestrap::popover>

        <br><br>

        {{-- Popover without arrow --}}
        <x-wirestrap::popover id="pop-no-arrow" content="No arrow popover" :arrow="false">
            <button id="trigger-no-arrow" type="button">No arrow</button>
        </x-wirestrap::popover>

        <br><br>

        {{-- Click trigger popover --}}
        <x-wirestrap::popover id="pop-click" header="Click popover" content="Click body" trigger="click">
            <button id="trigger-click" type="button">Click popover</button>
        </x-wirestrap::popover>

        <br><br>

        {{-- Interactive popover (default) --}}
        <x-wirestrap::popover id="pop-interactive" content="Interactive body">
            <x-slot:content>
                <div id="popover-panel" style="padding: 10px;">Interactive popover content</div>
            </x-slot:content>
            <button id="trigger-interactive" type="button">Interactive popover</button>
        </x-wirestrap::popover>

        <br><br>

        {{-- Dismiss via data attribute --}}
        <x-wirestrap::popover id="pop-dismiss" trigger="click">
            <x-slot:content>
                <div style="padding: 10px;">
                    <button id="btn-dismiss" type="button" data-ws-dismiss="floating">Close</button>
                </div>
            </x-slot:content>
            <button id="trigger-dismiss" type="button">Dismiss popover</button>
        </x-wirestrap::popover>

        {{-- Spacer to allow clicking away --}}
        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here to dismiss</div>
    </div>
</x-layouts.app>
