<div>
    {{-- Single --}}
    <x-wirestrap::autocomplete
        id="ac-single"
        wire:model="single"
        wire-options="getSuggestions"
        label="Single"
    />

    {{-- Multiple (tags) --}}
    <x-wirestrap::autocomplete
        id="ac-tags"
        wire:model="tags"
        wire-options="getTagSuggestions"
        label="Tags"
        multiple
    />

    {{-- Search / filtering --}}
    <x-wirestrap::autocomplete
        id="ac-search"
        wire:model="search"
        wire-options="getSuggestions"
        label="Search"
    />

    {{-- Disabled --}}
    <x-wirestrap::autocomplete
        id="ac-disabled"
        wire:model="disabled"
        wire-options="getSuggestions"
        label="Disabled"
        disabled
    />

    {{-- Grouped --}}
    <x-wirestrap::autocomplete
        id="ac-grouped"
        wire:model="grouped"
        wire-options="getGroupedSuggestions"
        label="Grouped"
    />

    {{-- Rich content --}}
    <x-wirestrap::autocomplete
        id="ac-rich"
        wire:model="rich"
        wire-options="getRichSuggestions"
        label="Rich"
    />

    {{-- Option class --}}
    <x-wirestrap::autocomplete
        id="ac-classed"
        wire:model="classed"
        wire-options="getClassedSuggestions"
        label="Classed"
    />

    {{-- XSS --}}
    <x-wirestrap::autocomplete
        id="ac-xss"
        wire:model="xss"
        wire-options="getXssSuggestions"
        label="XSS"
    />

    {{-- Accent normalization --}}
    <x-wirestrap::autocomplete
        id="ac-accented"
        wire:model="accented"
        wire-options="getAccentedSuggestions"
        label="Accented"
    />

    {{-- Ghost text --}}
    <x-wirestrap::autocomplete
        id="ac-ghost"
        wire:model="ghost"
        wire-options="getGhostSuggestions"
        label="Ghost"
    />

    {{-- Preloaded tags --}}
    <x-wirestrap::autocomplete
        id="ac-preloaded"
        wire:model="preloaded"
        wire-options="getTagSuggestions"
        label="Preloaded"
        multiple
    />

    {{-- Min chars --}}
    <x-wirestrap::autocomplete
        id="ac-minchars"
        wire:model="minChars"
        wire-options="getSuggestions"
        label="MinChars"
        :min-chars="2"
    />

    {{-- Min chars = 1 --}}
    <x-wirestrap::autocomplete
        id="ac-minchars1"
        wire:model="minChars1"
        wire-options="getSuggestions"
        label="MinChars1"
        :min-chars="1"
    />

    {{-- Wire-options-watch --}}
    <x-wirestrap::autocomplete
        id="ac-watched"
        wire:model="watchedItem"
        :wire-options="['getCategoryItems', ['ws-wire' => 'category']]"
        :wire-options-watch="['category', null]"
        label="Watched"
    />

    {{-- No dropdown --}}
    <x-wirestrap::autocomplete
        id="ac-nodropdown"
        wire:model="noDropdownTags"
        label="NoDropdown"
        multiple
        no-dropdown
    />

    {{-- Validation (per-tag) --}}
    <x-wirestrap::autocomplete
        id="ac-validated"
        wire:model="emails"
        label="Emails"
        multiple
    />
    <button wire:click="validateEmails" id="btn-validate">Validate</button>
</div>
