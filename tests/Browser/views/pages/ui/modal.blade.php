<x-layouts.app>
    <div class="p-4">

        {{-- Triggers --}}
        <button id="btn-basic" type="button" x-on:click="$wirestrap.modal.show('modal-basic')">Open basic</button>
        <button id="btn-titled" type="button" x-on:click="$wirestrap.modal.show('modal-titled')">Open titled</button>
        <button id="btn-footer" type="button" x-on:click="$wirestrap.modal.show('modal-footer')">Open footer</button>
        <button id="btn-sized" type="button" x-on:click="$wirestrap.modal.show('modal-sized')">Open sized</button>
        <button id="btn-static" type="button" x-on:click="$wirestrap.modal.show('modal-static')">Open static</button>
        <button id="btn-backdrop" type="button" x-on:click="$wirestrap.modal.show('modal-backdrop')">Open backdrop</button>
        <button id="btn-no-dismiss" type="button" x-on:click="$wirestrap.modal.show('modal-no-dismiss')">Open no dismiss</button>
        <button id="btn-expandable" type="button" x-on:click="$wirestrap.modal.show('modal-expandable')">Open expandable</button>
        <button id="btn-minimizable" type="button" x-on:click="$wirestrap.modal.show('modal-minimizable')">Open minimizable</button>
        <button id="btn-resizable" type="button" x-on:click="$wirestrap.modal.show('modal-resizable')">Open resizable</button>
        <button id="btn-draggable" type="button" x-on:click="$wirestrap.modal.show('modal-draggable')">Open draggable</button>
        <button id="btn-stack-a" type="button" x-on:click="$wirestrap.modal.show('modal-stack-a')">Open stack A</button>
        <button id="btn-stack-b" type="button" x-on:click="$wirestrap.modal.show('modal-stack-b')">Open stack B</button>
        <button id="btn-ws-dismiss" type="button" x-on:click="$wirestrap.modal.show('modal-ws-dismiss')">Open ws-dismiss</button>
        <button id="btn-focus" type="button" x-on:click="$wirestrap.modal.show('modal-focus')">Open focus</button>
        <button id="btn-focus-no-focusable" type="button" x-on:click="$wirestrap.modal.show('modal-focus-no-focusable')">Open focus no focusable</button>
        <button id="btn-combo" type="button" x-on:click="$wirestrap.modal.show('modal-combo')">Open combo</button>
        <button id="btn-backdrop2" type="button" x-on:click="$wirestrap.modal.show('modal-backdrop2')">Open backdrop2</button>
        <button id="btn-scroll-a" type="button" x-on:click="$wirestrap.modal.show('modal-scroll-a')">Open scroll A</button>
        <button id="btn-scroll-b" type="button" x-on:click="$wirestrap.modal.show('modal-scroll-b')">Open scroll B</button>

        <br><br>

        {{-- Basic modal (no title, no footer) --}}
        <x-wirestrap::modal id="modal-basic">
            <p id="modal-basic-body">Basic modal content</p>
        </x-wirestrap::modal>

        {{-- With title --}}
        <x-wirestrap::modal id="modal-titled" title="Modal Title">
            <p>Titled modal content</p>
        </x-wirestrap::modal>

        {{-- With footer --}}
        <x-wirestrap::modal id="modal-footer" title="Footer Modal">
            <p>Footer modal content</p>
            <x-slot:footer class="custom-footer-class">
                <button id="footer-action" type="button">Action</button>
            </x-slot:footer>
        </x-wirestrap::modal>

        {{-- Sized --}}
        <x-wirestrap::modal id="modal-sized" title="Sized" size="800">
            <p>Sized modal content</p>
        </x-wirestrap::modal>

        {{-- Static mode --}}
        <x-wirestrap::modal id="modal-static" title="Static Modal" static backdrop>
            <p>Static modal content</p>
        </x-wirestrap::modal>

        {{-- Backdrop --}}
        <x-wirestrap::modal id="modal-backdrop" title="Backdrop Modal" backdrop>
            <p>Backdrop modal content</p>
        </x-wirestrap::modal>

        {{-- Not dismissible --}}
        <x-wirestrap::modal id="modal-no-dismiss" title="No Dismiss" :dismissible="false">
            <p>No dismiss button</p>
            <button id="btn-close-no-dismiss" type="button" data-ws-dismiss="modal">Close via data attr</button>
        </x-wirestrap::modal>

        {{-- Expandable --}}
        <x-wirestrap::modal id="modal-expandable" title="Expandable" expandable>
            <p>Expandable modal</p>
        </x-wirestrap::modal>

        {{-- Minimizable --}}
        <x-wirestrap::modal id="modal-minimizable" title="Minimizable" minimizable draggable>
            <p>Minimizable modal</p>
        </x-wirestrap::modal>

        {{-- Resizable --}}
        <x-wirestrap::modal id="modal-resizable" title="Resizable" resizable>
            <p>Resizable modal</p>
        </x-wirestrap::modal>

        {{-- Draggable --}}
        <x-wirestrap::modal id="modal-draggable" title="Draggable" draggable>
            <p>Draggable modal</p>
        </x-wirestrap::modal>

        {{-- Stacking A --}}
        <x-wirestrap::modal id="modal-stack-a" title="Stack A" draggable>
            <p>Stack A content</p>
        </x-wirestrap::modal>

        {{-- Stacking B --}}
        <x-wirestrap::modal id="modal-stack-b" title="Stack B" draggable>
            <p>Stack B content</p>
        </x-wirestrap::modal>

        {{-- data-ws-dismiss="modal" --}}
        <x-wirestrap::modal id="modal-ws-dismiss" title="WS Dismiss">
            <p>Content</p>
            <button id="dismiss-via-data" type="button" data-ws-dismiss="modal">Dismiss me</button>
        </x-wirestrap::modal>

        {{-- Focus test --}}
        <x-wirestrap::modal id="modal-focus" title="Focus Test">
            <input id="modal-focus-input" type="text" placeholder="Should receive focus">
        </x-wirestrap::modal>

        {{-- Focus test (no focusable element) --}}
        <x-wirestrap::modal id="modal-focus-no-focusable" title="Focus No Focusable" :dismissible="false">
            <p>No focusable elements here</p>
        </x-wirestrap::modal>

        {{-- Combo: draggable + expandable + resizable --}}
        <x-wirestrap::modal id="modal-combo" title="Combo" draggable expandable resizable>
            <p>Combo modal</p>
        </x-wirestrap::modal>

        {{-- Second backdrop for reference counting --}}
        <x-wirestrap::modal id="modal-backdrop2" title="Backdrop 2" backdrop>
            <p>Second backdrop modal</p>
        </x-wirestrap::modal>

        {{-- Scroll lock reference counting (non-draggable) --}}
        <x-wirestrap::modal id="modal-scroll-a" title="Scroll A">
            <p>Scroll A</p>
        </x-wirestrap::modal>

        <x-wirestrap::modal id="modal-scroll-b" title="Scroll B">
            <p>Scroll B</p>
        </x-wirestrap::modal>

        {{-- Livewire managed modals --}}
        <livewire:wirestrap.modal-manager />
        <livewire:ui.modal-trigger lazy />
    </div>
</x-layouts.app>
