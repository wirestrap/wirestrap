# Alert

Programmatic centered alert dialogs, queued one at a time. Trigger from Alpine via `$wirestrap.alert`, via `Wirestrap.alert` in vanilla JS, or from Livewire PHP via `WithWirestrap::alert()`.

Three modes share the same dialog: a plain alert, `confirm` (calls a Livewire method), and `redirect` (sends the browser to a url once its countdown ends).

## Usage

**Alpine**

```js
$wirestrap.alert.show('Changes saved.')
$wirestrap.alert.show({ type: 'danger', title: 'Error', message: 'Something went wrong.' })
```

**Vanilla JS**

```js
Wirestrap.alert.show('Changes saved.')
Wirestrap.alert.show({ type: 'danger', title: 'Error', message: 'Something went wrong.' })
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

```blade
{{-- Redirect: blocking alert, navigates when the countdown ends --}}
<button type="button" x-on:click="$wirestrap.alert.redirect('Deleted, redirecting…', '/records', 2000)">
    Delete
</button>

{{-- Proposed redirect: the dismiss button becomes a link, the user chooses --}}
<button type="button" x-on:click="$wirestrap.alert.show({
    type: 'success',
    message: 'Record created.',
    url: '/records/42',
    dismissText: 'View it',
})">Save</button>
```

**Vanilla JS — confirm** (pass `wire` explicitly, no auto-resolution)

```js
Wirestrap.alert.confirm.show({
    type: 'danger',
    title: 'Delete record',
    message: 'This action cannot be undone.',
    wire: Livewire.find(wireId),
    method: 'delete',
    params: [recordId],
    confirmText: 'Yes, delete',
})
```

## $wirestrap.alert.show() / Wirestrap.alert.show()

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
| `url` | `string` | `null` | Turns the dismiss button into a link to this url. Clicking it dismisses the alert and lets the browser navigate. |

## $wirestrap.alert.confirm() / Wirestrap.alert.confirm.show()

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

## $wirestrap.alert.redirect() / Wirestrap.alert.redirect.show()

Shows a blocking alert, then navigates to `url` when the countdown ends. Use it to let the user read a message before the page changes, instead of redirecting straight away.

Shorthand: `$wirestrap.alert.redirect(message, url, duration)`.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `message` | `string` | `''` | Alert body text. |
| `url` | `string` | — | Destination. |
| `duration` | `number` | `2000` | Countdown before navigating, in ms. The progress bar shows the time left. |
| `type` | `string` | `'success'` | Visual variant. |
| `title` | `string` | `null` | Header title. |
| `showDismiss` | `bool` | `true` | Show a link that navigates immediately, skipping the countdown. |
| `dismissText` | `string` | `'Continue'` | Label of that link. |
| `backdropDismiss` | `bool` | `false` | Dismiss on backdrop click. Dismissing cancels the navigation. |
| `escapeDismiss` | `bool` | `false` | Dismiss on Escape. Dismissing cancels the navigation. |

The alert carries a link to `url` by default, so an impatient user can skip the countdown — clicking it triggers the same navigation, just earlier. Set `showDismiss: false` for a redirect with nothing to click at all.

Navigation is a native full page load, so any url works, same origin or not. The alert stays on screen while the destination loads. With `duration: 0` there is no countdown and the link is the only way out, so do not turn both off.

## Global defaults

`Wirestrap.alert.configure()` — sets defaults for alerts. Per-call options take precedence. Accepts: `duration`, `dismissText`, `showDismiss`, `backdropDismiss`, `escapeDismiss`.

`Wirestrap.alert.confirm.configure()` — sets confirm defaults independently. Accepts: `type`, `title`, `duration`, `confirmText`, `cancelText`, `backdropDismiss`, `escapeDismiss`.

`Wirestrap.alert.redirect.configure()` — sets redirect defaults independently. Accepts: `type`, `title`, `duration`, `showDismiss`, `dismissText`, `backdropDismiss`, `escapeDismiss`.

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

        // Alert with a link instead of a plain dismiss button
        $this->alert('success', 'Record created.', url: route('records.show', $record), dismissText: 'View it');
    }

    public function destroy(): void
    {
        $record->delete();

        // Informs, then navigates after 2s instead of redirecting straight away
        $this->alertRedirect(route('records.index'), 'Record deleted, redirecting…', 'success');
    }
}
```
