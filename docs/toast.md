# Toast

Programmatic toast notifications. Multiple toasts stack on screen, auto-dismiss after 5s, pause on hover, close on click.

## Usage

**Alpine**

```js
$wirestrap.toast('File saved successfully.')
$wirestrap.toast({ type: 'success', title: 'Saved', message: 'Your changes have been saved.' })
$wirestrap.toast({ type: 'danger', title: 'Connection lost', message: 'Reconnecting…', duration: 0 })
```

**Vanilla JS**

```js
Wirestrap.toast.add('File saved successfully.')
Wirestrap.toast.add({ type: 'success', title: 'Saved', message: 'Your changes have been saved.' })
Wirestrap.toast.add({ type: 'danger', title: 'Connection lost', message: 'Reconnecting…', duration: 0 })
```

```php
use Wirestrap\Traits\WithWirestrap;

class MyComponent extends Component
{
    use WithWirestrap;

    public function save(): void
    {
        $this->toast('success', 'Changes saved.');
        $this->toast(type: 'success', message: 'User created.', title: 'Done');
        $this->toast(type: 'danger', message: 'Connection lost.', duration: 0);
    }
}
```

## $wirestrap.toast() / Wirestrap.toast.add()

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `message` | `string` | `''` | Toast body text. |
| `type` | `string` | `'primary'` | Visual variant: primary, success, info, warning, danger. |
| `title` | `string` | `undefined` | Header title. Shows a colored icon when set. |
| `duration` | `number` | `5000` | Auto-dismiss delay in ms. 0 = persistent. |

## Wirestrap.toast.configure()

Call once at boot to set defaults globally. Per-call options take precedence.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `duration` | `number` | `5000` | Default auto-dismiss delay in ms. |
| `max` | `number` | `20` | Max visible toasts. Oldest removed when limit reached. |
| `placement` | `string` | `'bottom-end'` | Container position: bottom-end, bottom-start, top-end, top-start. |
