<div>
    <button id="btn-php-basic" type="button" wire:click="basic">PHP Basic</button>
    <button id="btn-php-titled" type="button" wire:click="withTitle">PHP Titled</button>
    <button id="btn-confirm" type="button" x-on:click="$wirestrap.alert.confirm({
        type: 'danger',
        title: 'Delete',
        message: 'Are you sure?',
        method: 'delete',
        confirmText: 'Yes',
        cancelText: 'No',
    })">Confirm Delete</button>

    <button id="btn-confirm-shorthand" type="button" x-on:click="$wirestrap.alert.confirm('Delete this record?', 'delete')">Confirm Shorthand</button>

    <button id="btn-confirm-params" type="button" x-on:click="$wirestrap.alert.confirm('Move item?', 'move', 42, 99)">Confirm Params</button>

    <button id="btn-redirect" type="button" x-on:click="$wirestrap.alert.redirect('Redirecting…', '/_ws/test/ui/alert-target', 300)">Redirect</button>

    <button id="btn-redirect-blocking" type="button" x-on:click="$wirestrap.alert.redirect('Redirecting…', '/_ws/test/ui/alert-target', 5000)">Redirect Blocking</button>

    <button id="btn-redirect-continue" type="button" x-on:click="$wirestrap.alert.redirect({
        message: 'Redirecting…',
        url: '/_ws/test/ui/alert-target',
        duration: 5000,
        showDismiss: true,
        dismissText: 'Go now',
    })">Redirect Continue</button>

    <button id="btn-redirect-silent" type="button" x-on:click="$wirestrap.alert.redirect({
        message: 'Redirecting…',
        url: '/_ws/test/ui/alert-target',
        duration: 5000,
        showDismiss: false,
    })">Redirect Silent</button>

    <button id="btn-alert-link" type="button" x-on:click="$wirestrap.alert.show({
        message: 'Record created.',
        url: '/_ws/test/ui/alert-target',
        dismissText: 'View it',
    })">Alert Link</button>

    <button id="btn-php-redirect" type="button" wire:click="goToTarget">PHP Redirect</button>

    @teleport('body')
        <button id="btn-confirm-teleported" type="button"
            x-on:click="$wirestrap.alert.confirm('Delete this record?', 'delete')">Confirm Teleported</button>
    @endteleport

    @if($deleted)
        <span id="deleted-flag">DELETED</span>
    @endif

    @if($moved)
        <span id="moved-flag">{{ $moved }}</span>
    @endif
</div>
