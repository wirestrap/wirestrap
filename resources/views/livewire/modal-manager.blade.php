<div>
    <div wire:loading class="ws-modal-manager-loading"></div>
    @foreach ($modals as $subComponent => $modalList)
        <div wire:key="ws-component-{{ md5($subComponent) }}">
            @foreach ($modalList as $id)
                <div wire:key="ws-modal-{{ $id }}">
                    <x-wirestrap::modal
                        :id="$id"
                        :auto-show="true"
                        :attributes="new Illuminate\View\ComponentAttributeBag($modalProps[$id])"
                    >
                        @livewire(
                            $subComponent,
                            $props[$id] + ['modalId' => $id],
                            key('wire_' . $id)
                        )
                    </x-wirestrap::modal>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
