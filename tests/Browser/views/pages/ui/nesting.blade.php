<x-layouts.app>
    <div class="p-4">

        {{-- Flyout > Tooltip --}}
        <x-wirestrap::flyout id="parent-ft">
            <x-slot:content>
                <div style="padding: 20px;">
                    <x-wirestrap::tooltip id="child-ft" content="Nested tooltip text">
                        <button id="child-ft-trigger" type="button">Hover for tooltip</button>
                    </x-wirestrap::tooltip>
                </div>
            </x-slot:content>
            <button id="parent-ft-trigger" type="button">Flyout > Tooltip</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Flyout > Flyout --}}
        <x-wirestrap::flyout id="parent-ff">
            <x-slot:content>
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="child-ff">
                        <x-slot:content>
                            <div id="child-ff-panel" style="padding: 20px;">Nested flyout content</div>
                        </x-slot:content>
                        <button id="child-ff-trigger" type="button">Hover for nested flyout</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="parent-ff-trigger" type="button">Flyout > Flyout</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Flyout > Popover --}}
        <x-wirestrap::flyout id="parent-fp">
            <x-slot:content>
                <div style="padding: 20px;">
                    <x-wirestrap::popover id="child-fp">
                        <x-slot:content>
                            <div id="child-fp-panel" style="padding: 20px;">Nested popover body</div>
                        </x-slot:content>
                        <button id="child-fp-trigger" type="button">Hover for popover</button>
                    </x-wirestrap::popover>
                </div>
            </x-slot:content>
            <button id="parent-fp-trigger" type="button">Flyout > Popover</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Popover > Tooltip --}}
        <x-wirestrap::popover id="parent-pt">
            <x-slot:content>
                <x-wirestrap::tooltip id="child-pt" content="Nested tooltip in popover">
                    <button id="child-pt-trigger" type="button">Hover for tooltip</button>
                </x-wirestrap::tooltip>
            </x-slot:content>
            <button id="parent-pt-trigger" type="button">Popover > Tooltip</button>
        </x-wirestrap::popover>

        {{-- Teleported Flyout > Tooltip --}}
        <x-wirestrap::flyout id="parent-tp" teleport="body">
            <x-slot:content class="panel-tp">
                <div style="padding: 20px;">
                    <x-wirestrap::tooltip id="child-tp" content="Tooltip inside teleported flyout">
                        <button id="child-tp-trigger" type="button">Hover for tooltip</button>
                    </x-wirestrap::tooltip>
                </div>
            </x-slot:content>
            <button id="parent-tp-trigger" type="button">Teleported Flyout > Tooltip</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Teleported Flyout > Flyout --}}
        <x-wirestrap::flyout id="parent-tpf" teleport="body">
            <x-slot:content class="panel-tpf">
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="child-tpf">
                        <x-slot:content>
                            <div id="child-tpf-panel" style="padding: 20px;">Nested flyout in teleported parent</div>
                        </x-slot:content>
                        <button id="child-tpf-trigger" type="button">Hover for nested flyout</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="parent-tpf-trigger" type="button">Teleported Flyout > Flyout</button>
        </x-wirestrap::flyout>

        {{-- Dismiss in nested flyouts, click-triggered to keep the panels stable --}}
        <x-wirestrap::flyout id="parent-dismiss" trigger="click">
            <x-slot:content>
                <div style="padding: 20px;">
                    <button id="parent-dismiss-btn" type="button" data-ws-dismiss="floating">Close parent</button>

                    <x-wirestrap::flyout id="child-dismiss" trigger="click">
                        <x-slot:content>
                            <div style="padding: 20px;">
                                <button id="child-dismiss-btn" type="button" data-ws-dismiss="floating">Close child</button>
                                <button id="child-stack-btn" type="button" data-ws-dismiss="floating-stack">Close the whole stack</button>
                            </div>
                        </x-slot:content>
                        <button id="child-dismiss-trigger" type="button">Open child</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="parent-dismiss-trigger" type="button">Open parent</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Dismiss from a teleported child inside a parent --}}
        <x-wirestrap::flyout id="parent-tdismiss" trigger="click">
            <x-slot:content>
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="child-tdismiss" trigger="click" teleport="body">
                        <x-slot:content class="panel-tdismiss">
                            <div style="padding: 20px;">
                                <button id="child-tdismiss-btn" type="button" data-ws-dismiss="floating">Close teleported child</button>
                            </div>
                        </x-slot:content>
                        <button id="child-tdismiss-trigger" type="button">Open teleported child</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="parent-tdismiss-trigger" type="button">Open parent (teleported child)</button>
        </x-wirestrap::flyout>

        {{-- Teleported parent > inline child: the parent contains the click, so only floating-stack closes it --}}
        <x-wirestrap::flyout id="parent-tstack" trigger="click" teleport="body">
            <x-slot:content class="panel-tstack">
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="child-tstack" trigger="click">
                        <x-slot:content>
                            <div style="padding: 20px;">
                                <button id="child-tstack-btn" type="button" data-ws-dismiss="floating-stack">Close the whole stack</button>
                            </div>
                        </x-slot:content>
                        <button id="child-tstack-trigger" type="button">Open child</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="parent-tstack-trigger" type="button">Open teleported parent</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Teleported child inside a parent: interacting with the child must not close the parent --}}
        <x-wirestrap::flyout id="nest-hover-parent">
            <x-slot:content class="panel-hover-parent">
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="nest-hover-child" teleport="body">
                        <x-slot:content class="panel-hover-child">
                            <div style="padding: 20px;">
                                <span id="nest-hover-inner">Child content</span>
                            </div>
                        </x-slot:content>
                        <button id="nest-hover-child-trigger" type="button">Hover child</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="nest-hover-parent-trigger" type="button">Hover parent</button>
        </x-wirestrap::flyout>

        <br><br>

        <x-wirestrap::flyout id="nest-click-parent" trigger="click">
            <x-slot:content class="panel-click-parent">
                <div style="padding: 20px;">
                    <x-wirestrap::flyout id="nest-click-child" trigger="click" teleport="body">
                        <x-slot:content class="panel-click-child">
                            <div style="padding: 20px;">
                                <button id="nest-click-inner" type="button">Child button</button>
                            </div>
                        </x-slot:content>
                        <button id="nest-click-child-trigger" type="button">Open child</button>
                    </x-wirestrap::flyout>
                </div>
            </x-slot:content>
            <button id="nest-click-parent-trigger" type="button">Open parent</button>
        </x-wirestrap::flyout>

        <br><br>

        {{-- Flyout inside a modal: data-ws-dismiss must not cross subsystems --}}
        <button id="modal-nest-trigger" type="button" x-on:click="$wirestrap.modal.show('modal-nest')">Open modal with flyout</button>

        <x-wirestrap::modal id="modal-nest" title="Modal with flyout">
            <x-wirestrap::flyout id="flyout-in-modal" trigger="click">
                <x-slot:content>
                    <div style="padding: 20px;">
                        <button id="flyout-in-modal-dismiss" type="button" data-ws-dismiss="floating">Close the flyout</button>
                    </div>
                </x-slot:content>
                <button id="flyout-in-modal-trigger" type="button">Open flyout</button>
            </x-wirestrap::flyout>
        </x-wirestrap::modal>

        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here to dismiss</div>
    </div>
</x-layouts.app>
