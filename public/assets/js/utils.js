window.appUtils = {
    formatNumber(value) {
        if (value === null || value === undefined || Number.isNaN(value)) {
            return '-';
        }

        return new Intl.NumberFormat('ro-RO').format(Number(value));
    },

    createOption(value, label) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    },
};
