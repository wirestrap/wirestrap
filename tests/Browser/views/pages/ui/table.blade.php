<x-layouts.app>
    <div class="p-4">

        {{-- Livewire table: bulk selection + animate --}}
        <livewire:ui.table-demo lazy />

        {{-- Static table with custom tooltip (always visible) --}}
        <x-wirestrap::table id="table-custom-tip" :columns="[['label' => 'Short', 'tooltip' => 'This is a custom tooltip']]">
            <tr><td>Data</td></tr>
        </x-wirestrap::table>

    </div>
</x-layouts.app>
