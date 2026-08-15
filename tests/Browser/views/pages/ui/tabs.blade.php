<x-layouts.app>
    <div class="p-4">

        {{-- Basic (first tab active by default) --}}
        <x-wirestrap::tabs id="tabs-basic">
            <x-slot:first label="First tab">First content</x-slot:first>
            <x-slot:second label="Second tab">Second content</x-slot:second>
            <x-slot:third label="Third tab">Third content</x-slot:third>
        </x-wirestrap::tabs>

        <br>

        {{-- Default prop --}}
        <x-wirestrap::tabs id="tabs-default" default="beta">
            <x-slot:alpha label="Alpha">Alpha content</x-slot:alpha>
            <x-slot:beta label="Beta">Beta content</x-slot:beta>
        </x-wirestrap::tabs>

        <br>

        {{-- Label slot --}}
        <x-wirestrap::tabs id="tabs-label">
            <x-slot:plain label="Plain label">Plain content</x-slot:plain>
            <x-slot:rich>Rich content</x-slot:rich>
            <x-slot:label_rich><strong>Rich label</strong></x-slot:label_rich>
        </x-wirestrap::tabs>

        <br>

        {{-- Icon --}}
        <x-wirestrap::tabs id="tabs-icon">
            <x-slot:starred label="Starred" icon="bi-star">Starred content</x-slot:starred>
            <x-slot:settings label="Settings" icon="bi-gear" icon-placement="end">Settings content</x-slot:settings>
            <x-slot:noicon label="No icon">No icon content</x-slot:noicon>
        </x-wirestrap::tabs>

        <br>

        {{-- Actions slot --}}
        <x-wirestrap::tabs id="tabs-actions">
            <x-slot:overview label="Overview">Overview content</x-slot:overview>
            <x-slot:history label="History">History content</x-slot:history>
            <x-slot:actions>
                <button id="tabs-action-btn" type="button">Export</button>
            </x-slot:actions>
        </x-wirestrap::tabs>

        <br>

        {{-- Events for programmatic control --}}
        <x-wirestrap::tabs id="tabs-events" :events="true">
            <x-slot:one label="One">One content</x-slot:one>
            <x-slot:two label="Two">Two content</x-slot:two>
        </x-wirestrap::tabs>

        <br>

        {{-- Invalid feedback (Livewire) --}}
        <livewire:ui.tabs-validation lazy />

        <div id="elsewhere" style="margin-top: 100px; padding: 20px;">Click here</div>
    </div>
</x-layouts.app>
