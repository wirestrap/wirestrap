<div>
    {{-- Single selection --}}
    <x-wirestrap::select
        id="sel-single"
        wire:model="single"
        wire-options="getOptions"
        label="Single"
    />

    {{-- Multiple selection --}}
    <x-wirestrap::select
        id="sel-multiple"
        wire:model="multiple"
        wire-options="getOptions"
        label="Multiple"
        multiple
    />

    {{-- With search --}}
    <x-wirestrap::select
        id="sel-search"
        wire:model="search"
        wire-options="getOptions"
        label="Search"
        search
    />

    {{-- Disabled --}}
    <x-wirestrap::select
        id="sel-disabled"
        wire:model="disabled"
        wire-options="getOptions"
        label="Disabled"
        disabled
    />

    {{-- With disabled option --}}
    <x-wirestrap::select
        id="sel-disabled-opt"
        wire:model="withDisabledOpt"
        wire-options="getOptionsWithDisabled"
        label="Disabled Option"
    />

    {{-- Static options --}}
    <x-wirestrap::select
        id="sel-static"
        wire:model="staticSelect"
        :options="['fr' => 'France', 'de' => 'Germany', 'es' => 'Spain']"
        label="Country"
    />

    {{-- Empty value --}}
    <x-wirestrap::select
        id="sel-empty-val"
        wire:model="emptyVal"
        wire-options="getEmptyableOptions"
        label="Filter"
        empty-value="0"
    />

    {{-- Keyboard (no search, to test keyboard on selection) --}}
    <x-wirestrap::select
        id="sel-keyboard"
        wire:model="keyboard"
        wire-options="getOptions"
        label="Keyboard"
    />

    {{-- Preloaded value: wire-options should load immediately --}}
    <x-wirestrap::select
        id="sel-preloaded"
        wire:model="preloaded"
        wire-options="getOptions"
        label="Preloaded"
    />

    {{-- Rich content with html_prefix / html_suffix --}}
    <x-wirestrap::select
        id="sel-rich"
        wire:model="rich"
        wire-options="getRichOptions"
        label="Rich"
    />

    {{-- Search with accented labels --}}
    <x-wirestrap::select
        id="sel-accented"
        wire:model="accented"
        wire-options="getAccentedOptions"
        label="Accented"
        search
    />

    {{-- Optgroups --}}
    <x-wirestrap::select
        id="sel-grouped"
        wire:model="grouped"
        wire-options="getGroupedOptions"
        label="Grouped"
    />

    {{-- Multiple with sort --}}
    <x-wirestrap::select
        id="sel-multi-sort"
        wire:model="multipleSort"
        wire-options="getOptions"
        label="Multi Sort"
        multiple
    />

    {{-- Options with class --}}
    <x-wirestrap::select
        id="sel-classed"
        wire:model="classed"
        wire-options="getClassedOptions"
        label="Classed"
    />

    {{-- XSS label escaping --}}
    <x-wirestrap::select
        id="sel-xss"
        wire:model="xss"
        wire-options="getXssOptions"
        label="XSS"
    />

    {{-- Reactive static options: morph updates data-ws-options --}}
    <x-wirestrap::select
        id="sel-reactive"
        wire:model="reactive"
        :options="$this->getReactiveOptions()"
        label="Reactive"
    />
    <button id="btn-extend" type="button" wire:click="addMoreOptions">Add options</button>
</div>
