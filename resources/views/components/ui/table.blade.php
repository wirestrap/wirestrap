@props([
    'columns' => [],        /** @var list<string|array{label: string, icon?: string, icon-placement?: string, html?: string, ...extra: mixed}> */
    'caption' => null,      /** @var \Illuminate\View\ComponentSlot|string|null */
    'head' => null,         /** @var \Illuminate\View\ComponentSlot|null Fallback when columns is not used */
    'foot' => null,         /** @var \Illuminate\View\ComponentSlot|null */
    'bulkActions' => null,  /** @var \Illuminate\View\ComponentSlot|list<array{label: string, icon?: string, icon-placement?: string}>|null */
    'wireModelBulk' => null, /** @var string|null Livewire property name to bind bulk selection to */
    'responsive' => config('wirestrap.table.responsive', true),
    'animate' => config('wirestrap.table.animate', false),
    'emptyLabel' => config('wirestrap.table.empty_label', 'No results'), /** @var string|null Set to null or empty string to disable */
])

@php
    $bulkActionsSlot = $bulkActions instanceof \Illuminate\View\ComponentSlot ? $bulkActions : null;
    $bulkActionItems = is_array($bulkActions) ? $bulkActions : null;
    $emptyColspan = ($count = count($columns) + ($wireModelBulk ? 1 : 0)) > 0 ? $count : 100;
    $wrapperClass = $attributes->get('class', config('wirestrap.table.class', ''));
@endphp

<div
    x-data="wsTable"
    @if ($responsive)
        class="ws-table-responsive{{ $wrapperClass ? ' ' . $wrapperClass : '' }}"
    @elseif ($wrapperClass)
        class="{{ $wrapperClass }}"
    @endif
    @if ($wireModelBulk) data-ws-model="{{ $wireModelBulk }}" @endif
    @if (!$animate) data-ws-animate="false" @endif
    data-ws-tip="ws-tooltip"
    data-ws-tip-arrow="ws-tooltip-arrow"
    data-ws-tip-inner="ws-tooltip-content"
    {{
        $attributes->except([
            'x-data',
            'class',
            'data-ws-model',
            'data-ws-animate',
            'data-ws-tip',
            'data-ws-tip-arrow',
            'data-ws-tip-inner',
        ])
    }}
>
    <table class="ws-table">
        @if ($caption)
            <caption>{{ $caption }}</caption>
        @endif

        @if ($columns)
            <thead>
                <tr>
                    @if ($wireModelBulk)
                        <th scope="col">
                            <input type="checkbox" class="ws-form-check-input" x-bind="wsBulkSelectAll" aria-label="{{ __('Select all') }}" />
                        </th>
                    @endif

                    @foreach ($columns as $column)
                        @php
                            $col = is_string($column) ? ['label' => $column] : $column;
                            $placement = $col['icon-placement'] ?? 'start';
                            $truncate = $col['truncate'] ?? true;
                            $colAttrs = Arr::except($col, ['label', 'icon', 'icon-placement', 'html', 'truncate', 'class', 'tooltip']);
                            $colClass = $truncate ? ' ws-th-truncate' : '';
                        @endphp

                        <th
                            scope="col"
                            @if ($colClass) class="{{ $colClass }}" @endif
                            @foreach ($colAttrs as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                        >
                            <span
                                @if (!empty($col['tooltip']))
                                    data-ws-label="{{ $col['tooltip'] }}"
                                    data-ws-tip-always
                                @elseif ($truncate && !empty($col['label']))
                                    data-ws-label="{{ $col['label'] }}"
                                @endif
                                @if (($col['class'] ?? ''))
                                    class="{{ ($col['class'] ?? '') }}"
                                @endif
                            >
                                @if (isset($col['icon']) && $placement === 'start')
                                    <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$col['icon']" />
                                @endif

                                {{ $col['label'] ?? '' }}

                                @if (isset($col['icon']) && $placement === 'end')
                                    <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$col['icon']" />
                                @endif

                                @if (!empty($col['html']))
                                    {!! $col['html'] !!}
                                @endif
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>
        @elseif ($head)
            <thead>{{ $head }}</thead>
        @endif

        <tbody>
            {{ $slot }}

            <tr data-ws-empty>
                <td colspan="{{ $emptyColspan }}" class="ws-table-empty">
                    <div>{{ __($emptyLabel) }}</div>
                </td>
            </tr>
        </tbody>

        @if ($foot)
            <tfoot>{{ $foot }}</tfoot>
        @endif
    </table>

    @if ($wireModelBulk && $bulkActions)
        <div data-ws-bulk-container style="display: none" wire:ignore.self>
            @if ($bulkActionsSlot)
                {{ $bulkActionsSlot }}
            @else
                <div class="ws-table-bulk-actions">
                    @foreach ($bulkActionItems as $action)
                        @php
                            $label = $action['label'] ?? '';
                            $icon = $action['icon'] ?? null;
                            $iconPlacement = $action['icon-placement'] ?? 'start';
                            $btnAttrs = Arr::except($action, ['label', 'icon', 'icon-placement']);
                        @endphp
                        <button
                            @foreach ($btnAttrs as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                        >
                            @if ($icon && $iconPlacement === 'start')
                                <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$icon" />
                            @endif

                            {{ $label }}

                            @if ($icon && $iconPlacement === 'end')
                                <x-dynamic-component :component="\Wirestrap\Wirestrap::iconComponent()" :icon="$icon" />
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
