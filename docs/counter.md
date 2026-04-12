# Counter

Animated numeric display that transitions to a target value using `requestAnimationFrame`.

## Usage

```blade
{{-- Static: animates from 0 to target on mount --}}
<x-wirestrap::counter :count="142300" />
<x-wirestrap::counter :count="4.87" :decimals="2" />
```

```blade
{{-- Reactive: re-animates on every change to $wire.total --}}
<x-wirestrap::counter count="$wire.total" />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `count` | `int\|float\|string` | `0` | Target value. Use `:count` for a PHP value (static) or omit `:` for a JS expression like `$wire.total` (reactive). |
| `decimals` | `int` | `config` | Decimal places shown during and after animation. |
| `duration` | `int` | `config` | Animation duration in ms. |
