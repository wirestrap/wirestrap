# Slider

Horizontal carousel with arrow navigation and a fixed column layout. Slides are named slots.

**Important:** Uses `@push('styles')` for per-instance column styles — add `@stack('styles')` inside `<head>` in your layout.

## Usage

```blade
<x-wirestrap::slider id="products" :columns="1" :columns-md="2" :columns-lg="3">
    <x-slot:item_1><div class="card">...</div></x-slot:item_1>
    <x-slot:item_2><div class="card">...</div></x-slot:item_2>
    <x-slot:item_3><div class="card">...</div></x-slot:item_3>
</x-wirestrap::slider>
```

```blade
{{-- External control: custom dots, no built-in arrows --}}
<div x-data="{ offset: 0 }" x-on:ws-slider-change="offset = $event.detail.offset">
    <x-wirestrap::slider id="hero" :columns="1" :arrows="false" events>
        <x-slot:slide_1>...</x-slot:slide_1>
        <x-slot:slide_2>...</x-slot:slide_2>
        <x-slot:slide_3>...</x-slot:slide_3>
    </x-wirestrap::slider>

    <button :class="offset === 0 ? 'active' : ''" x-on:click="$wirestrap.slider.goTo('hero', 0)">●</button>
    <button :class="offset === 1 ? 'active' : ''" x-on:click="$wirestrap.slider.goTo('hero', 1)">●</button>
    <button :class="offset === 2 ? 'active' : ''" x-on:click="$wirestrap.slider.goTo('hero', 2)">●</button>
</div>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required in strict mode (on by default). |
| `columns` | `int` | `config` | Visible columns at base breakpoint. |
| `columns-sm` | `int\|null` | `config` | Column override at ≥576px. |
| `columns-md` | `int\|null` | `config` | Column override at ≥768px. |
| `columns-lg` | `int\|null` | `config` | Column override at ≥992px. |
| `columns-xl` | `int\|null` | `config` | Column override at ≥1200px. |
| `scroll-by` | `int` | `config` | Slides advanced per arrow click. Ignored when scroll-page is enabled. |
| `scroll-page` | `bool` | `config` | Advance by full visible columns per arrow click. |
| `arrows` | `bool` | `config` | Show built-in prev/next arrows. |
| `events` | `bool` | `config` | Enables `$wirestrap.slider` magic helper and `ws-slider-change` events (detail: offset, canScrollPrev, canScrollNext). |

## $wirestrap.slider

| Method | Description |
|--------|-------------|
| `$wirestrap.slider.prev(id[, step])` | Navigate back by step slides (default: configured scroll-by). |
| `$wirestrap.slider.next(id[, step])` | Navigate forward by step slides (default: configured scroll-by). |
| `$wirestrap.slider.goTo(id, index)` | Jump to slide offset (0-based). |
| `$wirestrap.slider.first(id)` | Jump to first slide. |
| `$wirestrap.slider.last(id)` | Jump to last slide. |
