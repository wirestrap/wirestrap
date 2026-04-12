Alpine.data('wsInput', () => ({
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
