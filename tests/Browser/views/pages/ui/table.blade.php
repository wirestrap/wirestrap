<x-layouts.app>
    <div class="p-4">

        {{-- Livewire table: bulk selection + animate --}}
        <livewire:ui.table-demo lazy />

        {{-- Static table with custom tooltip (always visible) --}}
        <x-wirestrap::table id="table-custom-tip" :columns="[['label' => 'Short', 'tooltip' => 'This is a custom tooltip']]">
            <tr><td>Data</td></tr>
        </x-wirestrap::table>

        {{-- Truncation tooltip (long label, no data-ws-tip-always), "Short" column never overflows --}}
        <x-wirestrap::table id="table-truncate" :columns="['
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
            ', 'Short']">
            <tr><td>Data</td></tr>
        </x-wirestrap::table>

        {{-- Truncation tooltip, inside a minimizable modal (data-ws-label on its root) --}}
        <button id="btn-table-modal" type="button" x-on:click="$wirestrap.modal.show('modal-table-truncate')">Open table modal</button>

        <x-wirestrap::modal id="modal-table-truncate" title="Table" minimizable>
            <x-wirestrap::table id="table-truncate-modal" :columns="['
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro, blanditiis. Commodi vitae modi, voluptatem, voluptatibus repudiandae dolore porro cumque delectus iusto nostrum tenetur ea asperiores maxime dignissimos veniam inventore expedita.
                ']">
                <tr><td>Data</td></tr>
            </x-wirestrap::table>
        </x-wirestrap::modal>

    </div>
</x-layouts.app>
