@props([
    'icon' => '',   /** @var string|array<string, mixed> */
])

@php
    $attributes = new Illuminate\View\ComponentAttributeBag(
        is_array($icon) ? $icon : ['class' => $icon]
    );
    $attributes = $attributes->merge([
        'class' => config('wirestrap.icon.class'),
    ]);
@endphp

<i {{ $attributes }}></i>