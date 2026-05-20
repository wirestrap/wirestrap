<div>
    <div wire:loading class="ws-modal-manager-loading"></div>
    <div x-data="wsModalManager" x-bind="modalManager">
        @foreach ($modals as $subComponent => $modalList)
            <div wire:key="ws-component-{{ md5($subComponent) }}">
                @foreach ($modalList as $hash)
                    <div wire:key="ws-modal-{{ $hash }}">
                        <x-wirestrap::modal
                            :id="'modal_' . $hash"
                            :attributes="new Illuminate\View\ComponentAttributeBag($modalProps[$hash])"
                        >
                            @livewire(
                                $subComponent,
                                $props[$hash] + ['modalId' => 'modal_' . $hash],
                                'wire_' . $hash
                            )
                        </x-wirestrap::modal>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
