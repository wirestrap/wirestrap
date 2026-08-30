<?php

$components = [
    'checkbox' => '<x-wirestrap::checkbox />',
    'radio' => '<x-wirestrap::radio />',
    'switch' => '<x-wirestrap::switch />',
];

dataset('check components', $components);

// --- Input type ---

test('checkbox renders type checkbox', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::checkbox />')
        ->assertSee('type="checkbox"', false);
});

test('radio renders type radio', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::radio />')
        ->assertSee('type="radio"', false);
});

test('switch renders type checkbox with role switch', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::switch />')
        ->assertSee('type="checkbox"', false)
        ->assertSee('role="switch"', false);
});

// --- Wrapper class ---

test('renders ws-form-check wrapper', function (string $blade) {
    $html = $this->withViewErrors([])
        ->blade($blade)
        ->__toString();

    // Token match: a substring assertion would also match ws-form-check-input
    // and ws-form-check-label carried by the input and the label.
    expect(htmlHasClass($html, 'ws-form-check'))->toBeTrue();
})->with('check components');

test('switch has ws-form-switch class', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::switch />')
        ->assertSee('ws-form-switch', false);
});

// --- Label ---

test('renders label from prop', function (string $blade) {
    $blade = str_replace('/>', 'label="Accept" />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('ws-form-check-label', false)
        ->assertSee('Accept');
})->with('check components');

test('renders label from default slot', function (string $blade) {
    $blade = str_replace(
        '/>',
        '>Rich <strong>label</strong></x-wirestrap::' . explode('::', explode(' ', $blade)[0])[1] . '>',
        $blade
    );

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('<strong>label</strong>', false);
})->with('check components');

test('no label element when label is absent', function (string $blade) {
    $this->withViewErrors([])
        ->blade($blade)
        ->assertDontSee('ws-form-check-label', false);
})->with('check components');

test('label for attribute matches resolved id', function (string $blade) {
    $blade = str_replace('/>', 'id="my-check" label="Test" />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('for="my-check"', false);
})->with('check components');

// --- Label priority ---

test('slot content takes priority over label prop', function (string $blade) {
    $component = explode('::', explode(' ', $blade)[0])[1];
    $blade = str_replace('/>', 'label="Prop label">Slot label</x-wirestrap::' . $component . '>', $blade);

    $html = $this->withViewErrors([])
        ->blade($blade)
        ->__toString();

    expect($html)
        ->toContain('Slot label')
        ->not->toContain('Prop label');
})->with('check components');

// --- Layout variants ---

test('inline adds inline class', function (string $blade) {
    $blade = str_replace('/>', 'inline />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('ws-form-check-inline', false);
})->with('check components');

test('no inline class by default', function (string $blade) {
    $this->withViewErrors([])
        ->blade($blade)
        ->assertDontSee('ws-form-check-inline', false);
})->with('check components');

test('reverse adds reverse class', function (string $blade) {
    $blade = str_replace('/>', 'reverse />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('ws-form-check-reverse', false);
})->with('check components');

test('no reverse class by default', function (string $blade) {
    $this->withViewErrors([])
        ->blade($blade)
        ->assertDontSee('ws-form-check-reverse', false);
})->with('check components');

// --- Disabled ---

test('disabled attribute is rendered on input', function (string $blade) {
    $blade = str_replace('/>', 'disabled />', $blade);

    $html = $this->withViewErrors([])
        ->blade($blade)
        ->__toString();

    expect($html)->toHaveAttributeOn('ws-form-check-input', 'disabled');
})->with('check components');

// --- Validation ---

test('invalid class applied when wire:model has error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors(['field' => 'Required'])
        ->blade($blade)
        ->assertSee('ws-form-invalid', false);
})->with('check components');

test('no invalid class when no error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertDontSee('ws-form-invalid', false);
})->with('check components');

test('validation message rendered when has error', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" />', $blade);

    $this->withViewErrors(['field' => 'Required'])
        ->blade($blade)
        ->assertSee('ws-form-feedback-invalid', false)
        ->assertSee('Required');
})->with('check components');

test('no validation message when has-validation-message is false', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" :has-validation-message="false" />', $blade);

    $this->withViewErrors(['field' => 'Required'])
        ->blade($blade)
        ->assertDontSee('ws-form-feedback-invalid', false);
})->with('check components');

test('no invalid class when has-validation is false', function (string $blade) {
    $blade = str_replace('/>', 'wire:model="field" :has-validation="false" />', $blade);

    $this->withViewErrors(['field' => 'Required'])
        ->blade($blade)
        ->assertDontSee('ws-form-invalid', false);
})->with('check components');

// --- Radio value + auto-ID ---

test('radio renders value attribute', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::radio wire:model="plan" value="free" label="Free" />')
        ->assertSee('value="free"', false);
});

test('radio auto-resolves id from wire:model and value', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::radio wire:model="plan" value="free" label="Free" />')
        ->assertSee('id="plan-free"', false)
        ->assertSee('for="plan-free"', false);
});

test('checkbox auto-resolves id from wire:model and value', function () {
    $this->withViewErrors([])
        ->blade('<x-wirestrap::checkbox wire:model="interests" value="music" label="Music" />')
        ->assertSee('id="interests-music"', false)
        ->assertSee('for="interests-music"', false);
});

// --- Wrapper class forwarding ---

test('custom class is added to wrapper', function (string $blade) {
    $blade = str_replace('/>', 'class="my-custom" />', $blade);

    $html = $this->withViewErrors([])
        ->blade($blade)
        ->__toString();

    expect(htmlContainsInside($html, null, 'my-custom'))->toBeTrue();
    expect(htmlContainsInside($html, 'ws-form-check-input', 'my-custom'))->toBeFalse();
})->with('check components');

// --- Default attributes ---

test('default-attributes are merged onto input', function (string $blade) {
    $blade = str_replace('/>', ':default-attributes="[\'data-custom\' => \'val\']" />', $blade);

    $this->withViewErrors([])
        ->blade($blade)
        ->assertSee('data-custom="val"', false);
})->with('check components');
