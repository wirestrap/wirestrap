Alpine.data('wsInput', () => ({
    input: {
        ['x-bind:type']() {
            return this.showPassword ? 'text' : 'password';
        },
    },

    passwordToggle: {
        ['x-on:click']() {
            this.toggle();
        },
        ['x-bind:aria-label']() {
            return this.showPassword
                ? this.$root.getAttribute('data-ws-label-hide')
                : this.$root.getAttribute('data-ws-label-show');
        },
        ['x-bind:class']() {
            return { 'ws-password-shown': this.showPassword };
        },
    },

    /**
     * State
     */
    showPassword: false,

    /**
     * Password
     */
    toggle() {
        this.showPassword = !this.showPassword;
    },
}));
