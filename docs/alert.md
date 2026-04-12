# Alert

Programmatic centered alert dialogs, queued one at a time. Trigger from Alpine via `$wirestrap.alert` or from Livewire PHP via `WithWirestrap::alert()`.

## Usage

```js
$wirestrap.alert.show('Changes saved.')
$wirestrap.alert.show({ type: 'danger', title: 'Error', message: 'Something went wrong.' })
```

```blade
{{-- Confirmation with Livewire method call --}}
<button type="button" x-on:click="$wirestrap.alert.confirm({
    type: 'danger',
    title: 'Delete record',
    message: 'This action cannot be undone.',
    method: 'delete',
    params: [{{ $record->id }}],
    confirmText: 'Yes, delete',
})">Delete</button>

{{-- Shorthand: message, method, then optional params as individual arguments --}}
<button type="button" x-on:click="$wirestrap.alert.confirm('Are you sure?', 'delete', recordId)">
    Delete
</button>
```

## $wirestrap.alert.show()

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `message` | `string` | `''` | Alert body text. |
| `type` | `string` | `'primary'` | Visual variant: primary, success, info, warning, danger. |
| `title` | `string` | `null` | Header title. Shows a colored icon when set. |
| `duration` | `number` | `0` | Auto-dismiss delay in ms. 0 = persistent. |
| `dismissText` | `string` | `'OK'` | Dismiss button label. |
| `showDismiss` | `bool` | `true` | Show the dismiss button. |
| `backdropDismiss` | `bool` | `true` | Dismiss on backdrop click. Alert shakes if disabled and attempted. |
| `escapeDismiss` | `bool` | `true` | Dismiss on Escape. Alert shakes if disabled and attempted. |

## $wirestrap.alert.confirm()

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `message` | `string` | `''` | Confirmation body text. |
| `method` | `string` | — | Livewire method to call on confirm. |
| `params` | `array` | `[]` | Parameters passed to the method. |
| `type` | `string` | `'primary'` | Visual variant. |
| `title` | `string` | `null` | Header title. |
| `duration` | `number` | `0` | Auto-dismiss delay in ms. |
| `confirmText` | `string` | `'Confirm'` | Confirm button label. |
| `cancelText` | `string` | `'Cancel'` | Cancel button label. |
| `backdropDismiss` | `bool` | `true` | Dismiss on backdrop click (counts as cancel). |
| `escapeDismiss` | `bool` | `true` | Dismiss on Escape (counts as cancel). |

## Global defaults

`Wirestrap.alert.configure()` — sets defaults for alerts. Per-call options take precedence. Accepts: `duration`, `dismissText`, `showDismiss`, `backdropDismiss`, `escapeDismiss`.

`Wirestrap.alert.confirm.configure()` — sets confirm defaults independently. Accepts: `type`, `title`, `duration`, `confirmText`, `cancelText`, `backdropDismiss`, `escapeDismiss`.

## Triggering from Livewire (PHP)

```php
use Wirestrap\Traits\WithWirestrap;

class MyComponent extends Component
{
    use WithWirestrap;

    public function save(): void
    {
        $this->alert('success', 'Changes saved.');
        $this->alert(type: 'danger', message: 'Something went wrong.', title: 'Error');
    }
}
```
