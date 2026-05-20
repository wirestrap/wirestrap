@props([
    'id' => null,
    'title' => null,    /** @var \Illuminate\View\ComponentSlot|string|null */
    'footer' => null,   /** @var \Illuminate\View\ComponentSlot|string|null */
    'size' => config('wirestrap.modal.size', null),
    'draggable' => config('wirestrap.modal.draggable', false),
    'minimizable' => config('wirestrap.modal.minimizable', false),
    'expandable' => config('wirestrap.modal.expandable', false),
    'resizable' => config('wirestrap.modal.resizable', false),
    'static' => config('wirestrap.modal.static', false),
    'backdrop' => config('wirestrap.modal.backdrop', false),
    'dismissible' => config('wirestrap.modal.dismissible', true),
    'autoShow' => false,
])

@php
    \Wirestrap\Wirestrap::handleStrictMode($id, 'modal');

    $isFooterSlot = $footer instanceof \Illuminate\View\ComponentSlot;
    $modalId = $id;

    // Build modal-dialog class list
    $dialogClasses = 'ws-modal-dialog';
    if ($size) {
        $dialogClasses .= ' ws-modal-' . $size;
    }
@endphp

<div
    x-data="wsModal"
    data-ws-draggable="{{ $draggable ? 'true' : 'false' }}"
    data-ws-static="{{ $static ? 'true' : 'false' }}"
    data-ws-resizable="{{ $resizable ? 'true' : 'false' }}"
    data-ws-backdrop="{{ $backdrop ? 'true' : 'false' }}"
    @if ($autoShow) data-ws-auto-show="true" @endif
    @if ($minimizable)
        data-ws-label="{{ $title ?: $modalId }}"
        data-ws-btn-class="ws-modal-minimized-btn"
    @endif
>
    <div
        class="ws-modal {{ $attributes->get('class', config('wirestrap.modal.class', '')) }}"
        id="{{ $modalId }}"
        role="dialog"
        tabindex="-1"
        x-bind="modalEl"
        aria-hidden="true"
        @if ($title) aria-labelledby="{{ $modalId }}-title" @endif
        wire:ignore.self
    >
        <div class="{{ $dialogClasses }}" wire:ignore.self>
            <div wire:ignore.self class="ws-modal-content">
                <div class="ws-modal-header{{ $draggable ? ' ws-drag-handle' : '' }}">
                    @if ($title)
                        <h5 id="{{ $modalId }}-title" class="ws-modal-title">
                            {{ $title }}
                        </h5>
                    @endif

                    @if ($minimizable)
                        <button
                            x-bind="minimizeBtn"
                            type="button"
                            class="ws-modal-minimize-btn"
                            aria-label="{{ __('Minimize') }}"
                        ></button>
                    @endif

                    @if ($expandable)
                        <button
                            x-bind="expandBtn"
                            type="button"
                            class="ws-modal-expand-btn"
                            aria-label="{{ __('Toggle fullscreen') }}"
                            aria-pressed="false"
                        ></button>
                    @endif

                    @if ($dismissible)
                        <button
                            type="button"
                            class="ws-modal-close"
                            x-bind="closeBtn"
                            aria-label="{{ __('Close') }}"
                        ></button>
                    @endif
                </div>

                <div class="ws-modal-body">
                    {{ $slot }}
                </div>

                @if ($isFooterSlot ? $footer->hasActualContent() : $footer)
                    <div class="ws-modal-footer{{ $isFooterSlot ? ' ' . $footer->attributes->get('class', '') : '' }}">
                        {{ $footer }}
                    </div>
                @endif

                @if ($resizable)
                    <div class="ws-modal-resize-handle ws-modal-resize-e" data-ws-resize="e" aria-hidden="true"></div>
                    <div class="ws-modal-resize-handle ws-modal-resize-w" data-ws-resize="w" aria-hidden="true"></div>
                    <div class="ws-modal-resize-handle ws-modal-resize-s" data-ws-resize="s" aria-hidden="true"></div>
                    <div class="ws-modal-resize-handle ws-modal-resize-se" data-ws-resize="se" aria-hidden="true"></div>
                    <div class="ws-modal-resize-handle ws-modal-resize-sw" data-ws-resize="sw" aria-hidden="true"></div>
                @endif
            </div>
        </div>
    </div>
</div>
