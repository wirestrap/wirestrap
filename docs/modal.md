# Modal

Modal dialog built on Alpine. Open/close via `$wirestrap.modal` or from Livewire via `WithWirestrap`. Supports dragging, minimizing, fullscreen, resize, static mode, backdrop, and z-index stacking for multiple open modals.

## Usage

```blade
<button type="button" x-on:click="$wirestrap.modal.show('contact-modal')">Add contact</button>

<x-wirestrap::modal id="contact-modal" title="Add contact" backdrop>
    <x-wirestrap::input label="Name" wire:model="name" />

    <x-slot:footer>
        <button type="button" data-ws-dismiss="modal">Cancel</button>
        <button type="button" wire:click="save">Save</button>
    </x-slot:footer>
</x-wirestrap::modal>
```

```blade
{{-- ModalManager: injects a Livewire component into a modal at runtime --}}
{{-- Place once in your layout: --}}
<livewire:wirestrap.modal-manager />

{{-- Trigger from any Livewire component: --}}
$this->modalShowManaged(
    component: 'users.edit-form',
    props: ['userId' => $userId],
    modalProps: ['title' => 'Edit user', 'draggable' => true],
);
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | `string\|null` | `null` | Element id. Required in strict mode (on by default). |
| `title` | `slot\|string\|null` | `null` | Header title. |
| `footer` | `slot\|string\|null` | `null` | Footer content. |
| `size` | `string\|null` | `config` | Dialog width via `ws-modal-{size}`. Values 300–1900 in steps of 100. Capped at viewport width. |
| `dismissible` | `bool` | `config` | Show close button in header. |
| `static` | `bool` | `config` | Shake instead of close on outside click or Escape. |
| `backdrop` | `bool` | `config` | Show backdrop overlay. |
| `draggable` | `bool` | `config` | Drag by the header. |
| `minimizable` | `bool` | `config` | Minimize to taskbar button. |
| `expandable` | `bool` | `config` | Fullscreen toggle button. |
| `resizable` | `bool` | `config` | Resize handles on east, west, south, south-east, south-west edges. |
| `destroy-on-dismiss` | `bool` | `config` | Dispatch `ws-modal-manager:destroy` when the modal is dismissed. For managed modals that should be destroyed on close. |

## $wirestrap.modal

| Method | Description |
|--------|-------------|
| `$wirestrap.modal.show(id)` | Show modal. |
| `$wirestrap.modal.hide(id)` | Hide modal. |

## modalShowManaged (WithWirestrap)

| Parameter | Type | Description |
|-----------|------|-------------|
| `$component` | `string` | Livewire component name in dot notation (e.g. `'users.edit-form'`). |
| `$props` | `array` | Props passed to the child Livewire component. |
| `$modalProps` | `array` | Props passed to the modal wrapper (e.g. title, size, draggable). |
| `$key` | `string\|null` | Optional key used as the modal id. Defaults to a hash of the payload. |

The child component receives a `$modalId` prop equal to the key. Use `$this->modalHide($this->modalId)` to close from PHP, or `$this->modalDestroyManaged(key: $this->modalId)` to destroy itself. Calls with identical component + props + modalProps re-show the already-open modal.

## modalDestroyManaged (WithWirestrap)

| Parameter | Type | Description |
|-----------|------|-------------|
| `$component` | `string\|array\|null` | Component name(s) in dot notation. Destroys all modals for each given component. |
| `$key` | `string\|array\|null` | Key(s) passed to `modalShowManaged`. Destroys each matching modal. |

Passing neither parameter destroys all managed modals. Passing an array allows destroying multiple modals in a single dispatch.
