<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, components that require an explicit `id` to function
    | correctly (for wire:key binding, Alpine state isolation, etc.) will throw
    | an exception if none is provided. Disable at your own risk: components
    | will silently misbehave in Livewire re-renders without a stable id.
    |
    */
    'strict_mode' => true,

    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    |
    | Wirestrap renders icons via <x-wirestrap::ui.icon>, used internally
    | by components such as inputs, selects, and buttons.
    |
    | The `icon` prop accepted by those components can be passed as:
    |   - a string : used as the `class` attribute on the <i> element
    |   - an array : spread as attributes on the <i> element
    |
    | You may provide your own icon component path.
    |
    */
    'icon' => [
        'component_path' => null,
        'class' => 'bi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Classes applied on form elements when validation fails.
    |
    */
    'validation' => [
        'invalid' => 'ws-form-invalid',
        'feedback_invalid' => 'ws-form-feedback-invalid',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Components
    |--------------------------------------------------------------------------
    |
    | `class` adds an extra class on the component root element globally.
    | It can be overridden per-instance via the `class` attribute bag.
    |
    */
    'tooltip' => [
        'placement' => 'top',
        'append' => null,
        'trigger' => 'hover',
        'offset_skidding' => 0,
        'offset_distance' => 8,
        'position' => 'absolute',
        'arrow' => true,
        'class' => '',
    ],

    'popover' => [
        'placement' => 'top',
        'append' => null,
        'trigger' => 'hover',
        'offset_skidding' => 0,
        'offset_distance' => 8,
        'position' => 'absolute',
        'arrow' => true,
        'class' => '',
    ],

    'flyout' => [
        'events' => false,
        'placement' => 'bottom-start',
        'append' => null,
        'trigger' => 'hover',
        'offset_skidding' => 0,
        'offset_distance' => 0,
        'position' => 'absolute',
        'class' => '',
    ],

    'counter' => [
        'duration' => 1000,
        'decimals' => 0,
        'class' => '',
    ],

    'menu' => [
        'events' => false,
        'single' => false,
        'class' => '',
    ],

    'slider' => [
        'columns' => 3,
        'columns_sm' => null,
        'columns_md' => null,
        'columns_lg' => null,
        'columns_xl' => null,
        'scroll_by' => 1,
        'scroll_page' => false,
        'arrows' => true,
        'events' => false,
        'class' => '',
    ],

    'accordion' => [
        'events' => false,
        'single' => false,
        'invalid_feedback' => false,
        'class' => '',
    ],

    'tabs' => [
        'events' => false,
        'invalid_feedback' => false,
        'class' => '',
    ],

    'table' => [
        'responsive' => true,
        'animate' => false,
        'empty' => 'No results found',
        'class' => '',
    ],

    'modal' => [
        'size' => null,
        'dismissible' => true,
        'static' => false,
        'draggable' => false,
        'minimizable' => false,
        'expandable' => false,
        'resizable' => false,
        'backdrop' => false,
        'class' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Components
    |--------------------------------------------------------------------------
    */
    'input' => [
        'has_validation' => true,
        'has_validation_message' => true,
        'password_show_icon' => 'bi-eye',
        'password_hide_icon' => 'bi-eye-slash',
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'textarea' => [
        'has_validation' => true,
        'has_validation_message' => true,
        'rows' => 3,
        'autosize' => true,
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'radio' => [
        'has_validation' => true,
        'has_validation_message' => true,
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'switch' => [
        'has_validation' => true,
        'has_validation_message' => true,
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'checkbox' => [
        'has_validation' => true,
        'has_validation_message' => true,
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'select' => [
        'multiple' => false,
        'search' => false,
        'live' => false,
        'placeholder' => 'Select an option',
        'search_placeholder' => 'Search ...',
        'empty_value' => null,
        'dropdown_offset' => 0,
        'has_validation' => true,
        'has_validation_message' => true,
        'default_attributes' => [
            // 'wire:loading.attr' => 'disabled',
        ],
        'class' => '',
    ],

    'autocomplete' => [
        'dropdown_offset' => 0,
    ],
];
