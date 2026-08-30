<x-layouts.app>
    <div class="p-4">

        {{-- Livewire table: bulk selection + animate --}}
        <livewire:ui.table-demo lazy />

        {{-- Static table with custom tooltip (always visible) --}}
        <x-wirestrap::table id="table-custom-tip" :columns="[['label' => 'Short', 'tooltip' => 'This is a custom tooltip']]">
            <tr><td>Data</td></tr>
        </x-wirestrap::table>

        {{-- Truncation tooltip (long label, no data-ws-tip-always) --}}
        <x-wirestrap::table id="table-truncate" :columns="['
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
        ']">
            <tr><td>Data</td></tr>
        </x-wirestrap::table>

    </div>
</x-layouts.app>
