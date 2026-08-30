<div>
    <x-wirestrap::counter id="counter-reactive" count="$wire.total" :duration="200" />
    <button id="btn-increment" type="button" wire:click="increment">+500</button>
    <span id="total-value">{{ $total }}</span>
</div>
