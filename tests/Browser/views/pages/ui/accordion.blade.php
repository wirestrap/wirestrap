<x-layouts.app>
    <div class="p-4">

        {{-- Multiple mode (default) --}}
        <x-wirestrap::accordion id="acc-multi">
            <x-slot:first label="First panel">First content</x-slot:first>
            <x-slot:second label="Second panel">Second content</x-slot:second>
            <x-slot:third label="Third panel" open>Third content (open)</x-slot:third>
        </x-wirestrap::accordion>

        <br>

        {{-- Single mode --}}
        <x-wirestrap::accordion id="acc-single" :single="true">
            <x-slot:alpha label="Alpha panel">Alpha content</x-slot:alpha>
            <x-slot:beta label="Beta panel" open>Beta content (open)</x-slot:beta>
            <x-slot:gamma label="Gamma panel">Gamma content</x-slot:gamma>
        </x-wirestrap::accordion>

        <br>

        {{-- Label slot --}}
        <x-wirestrap::accordion id="acc-label-slot">
            <x-slot:item label="Attr label">Attr label content</x-slot:item>
            <x-slot:slotted>Slot label content</x-slot:slotted>
            <x-slot:label_slotted><strong>Rich label</strong></x-slot:label_slotted>
        </x-wirestrap::accordion>

        <br>

        {{-- Icon --}}
        <x-wirestrap::accordion id="acc-icon">
            <x-slot:start label="Start icon" icon="bi-star">Start content</x-slot:start>
            <x-slot:end label="End icon" icon="bi-gear" icon-placement="end">End content</x-slot:end>
            <x-slot:none label="No icon">No icon content</x-slot:none>
        </x-wirestrap::accordion>

        <br>

        {{-- With events for programmatic control --}}
        <x-wirestrap::accordion id="acc-events" :events="true">
            <x-slot:one label="One">One content</x-slot:one>
            <x-slot:two label="Two">Two content</x-slot:two>
        </x-wirestrap::accordion>

        <br>

        {{-- Invalid feedback (Livewire) --}}
        <livewire:ui.accordion-validation lazy />
    </div>
</x-layouts.app>
