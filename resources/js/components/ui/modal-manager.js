/**
 * Alpine component for WireStrap modal manager.
 *
 * Listens for "ws-modal-manager:open" from ModalManager.php
 * and opens the modal via its hash ID.
 *
 * Ensures the modal is in the DOM before displaying it.
 */
Alpine.data('wsModalManager', () => ({
    /**
     * Binding
     */
    modalManager: {
        ['x-on:ws-modal-manager:open.window'](e) {
            this.$nextTick(() => this.$wirestrap.modal.show('modal_' + e.detail.hash));
        },
    },
}));
