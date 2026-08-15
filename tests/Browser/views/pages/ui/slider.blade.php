<x-layouts.app>
    <div class="p-4">

        {{-- Basic slider: 3 columns, 6 slides, arrows --}}
        <x-wirestrap::slider id="slider-basic" :columns="3">
            <x-slot:s1><div class="slide-content">Slide 1</div></x-slot:s1>
            <x-slot:s2><div class="slide-content">Slide 2</div></x-slot:s2>
            <x-slot:s3><div class="slide-content">Slide 3</div></x-slot:s3>
            <x-slot:s4><div class="slide-content">Slide 4</div></x-slot:s4>
            <x-slot:s5><div class="slide-content">Slide 5</div></x-slot:s5>
            <x-slot:s6><div class="slide-content">Slide 6</div></x-slot:s6>
        </x-wirestrap::slider>

        {{-- Scroll by 2 --}}
        <x-wirestrap::slider id="slider-scroll-by" :columns="2" :scroll-by="2">
            <x-slot:a><div>A</div></x-slot:a>
            <x-slot:b><div>B</div></x-slot:b>
            <x-slot:c><div>C</div></x-slot:c>
            <x-slot:d><div>D</div></x-slot:d>
            <x-slot:e><div>E</div></x-slot:e>
            <x-slot:f><div>F</div></x-slot:f>
        </x-wirestrap::slider>

        {{-- Scroll page --}}
        <x-wirestrap::slider id="slider-scroll-page" :columns="2" scroll-page>
            <x-slot:a><div>A</div></x-slot:a>
            <x-slot:b><div>B</div></x-slot:b>
            <x-slot:c><div>C</div></x-slot:c>
            <x-slot:d><div>D</div></x-slot:d>
            <x-slot:e><div>E</div></x-slot:e>
            <x-slot:f><div>F</div></x-slot:f>
        </x-wirestrap::slider>

        {{-- External control with events --}}
        <div x-data="{ offset: 0, canPrev: false, canNext: true }"
             x-on:ws-slider-change="offset = $event.detail.offset; canPrev = $event.detail.canScrollPrev; canNext = $event.detail.canScrollNext">
            <x-wirestrap::slider id="slider-events" :columns="1" :arrows="false" events>
                <x-slot:a><div>A</div></x-slot:a>
                <x-slot:b><div>B</div></x-slot:b>
                <x-slot:c><div>C</div></x-slot:c>
            </x-wirestrap::slider>
            <span id="event-offset" x-text="offset"></span>
            <span id="event-can-prev" x-text="canPrev"></span>
            <span id="event-can-next" x-text="canNext"></span>
            <button id="btn-ev-prev" type="button" x-on:click="$wirestrap.slider.prev('slider-events')">Prev</button>
            <button id="btn-ev-next" type="button" x-on:click="$wirestrap.slider.next('slider-events')">Next</button>
            <button id="btn-ev-goto1" type="button" x-on:click="$wirestrap.slider.goTo('slider-events', 1)">Go 1</button>
            <button id="btn-ev-first" type="button" x-on:click="$wirestrap.slider.first('slider-events')">First</button>
            <button id="btn-ev-last" type="button" x-on:click="$wirestrap.slider.last('slider-events')">Last</button>
        </div>

        {{-- Scroll page + scroll-by: scrollPage overrides scrollBy --}}
        <x-wirestrap::slider id="slider-page-override" :columns="2" :scroll-by="1" scroll-page events>
            <x-slot:a><div>A</div></x-slot:a>
            <x-slot:b><div>B</div></x-slot:b>
            <x-slot:c><div>C</div></x-slot:c>
            <x-slot:d><div>D</div></x-slot:d>
            <x-slot:e><div>E</div></x-slot:e>
            <x-slot:f><div>F</div></x-slot:f>
        </x-wirestrap::slider>

        {{-- Exact fit: slides === columns, no scroll possible --}}
        <x-wirestrap::slider id="slider-exact" :columns="3">
            <x-slot:a><div>A</div></x-slot:a>
            <x-slot:b><div>B</div></x-slot:b>
            <x-slot:c><div>C</div></x-slot:c>
        </x-wirestrap::slider>

        {{-- Responsive columns --}}
        <x-wirestrap::slider id="slider-responsive" :columns="1" :columns-md="2" :columns-lg="3">
            <x-slot:a><div>A</div></x-slot:a>
            <x-slot:b><div>B</div></x-slot:b>
            <x-slot:c><div>C</div></x-slot:c>
            <x-slot:d><div>D</div></x-slot:d>
        </x-wirestrap::slider>

    </div>
</x-layouts.app>
