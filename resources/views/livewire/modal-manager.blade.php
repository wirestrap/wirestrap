<div>
    <div wire:loading class="ws-modal-manager-loading"></div>
    @foreach ($modals as $subComponent => $modalList)
        <div wire:key="ws-component-{{ md5($subComponent) }}">
            @foreach ($modalList as $id)
                <div wire:key="ws-modal-{{ $id }}">
                    <x-wirestrap::modal
                        :id="'modal_' . $id"
                        :auto-show="true"
                        :attributes="new Illuminate\View\ComponentAttributeBag($modalProps[$id])"
                    >
                        @livewire(
                            $subComponent,
                            $props[$id] + ['modalId' => 'modal_' . $id],
                            'wire_' . $id
                        )
                    </x-wirestrap::modal>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
