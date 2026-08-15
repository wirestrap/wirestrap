<x-layouts.app>
    <div class="p-4">
        <x-wirestrap::counter id="counter-int" :count="500" :duration="200" />
        <x-wirestrap::counter id="counter-dec" :count="4.87" :decimals="2" :duration="200" />
        <x-wirestrap::counter id="counter-zero" :count="0" />
        <x-wirestrap::counter id="counter-zero-dec" :count="0" :decimals="2" />

        <br>

        <livewire:ui.counter-reactive lazy />
    </div>
</x-layouts.app>
