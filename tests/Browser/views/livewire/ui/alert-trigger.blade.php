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

    <button id="btn-confirm-shorthand" type="button"
        x-on:click="$wirestrap.alert.confirm('Delete this record?', 'delete')">Confirm Shorthand</button>

    <button id="btn-confirm-params" type="button"
        x-on:click="$wirestrap.alert.confirm('Move item?', 'move', 42, 99)">Confirm Params</button>

    @if($deleted)
        <span id="deleted-flag">DELETED</span>
    @endif

    @if($moved)
        <span id="moved-flag">{{ $moved }}</span>
    @endif
</div>
