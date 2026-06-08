(function () {
    'use strict';

    function formatNumber(value) {
        if (value === null || value === undefined || value === '' || Number.isNaN(Number(value))) {
            return '-';
        }

        return new Intl.NumberFormat('ro-RO').format(Number(value));
    }

    function formatPercent(value, fractionDigits = 2) {
        if (value === null || value === undefined || value === '' || Number.isNaN(Number(value))) {
            return '-';
        }

        return new Intl.NumberFormat('ro-RO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: fractionDigits,
        }).format(Number(value)) + '%';
    }

    function createOption(value, label, selected = false) {
        const option = document.createElement('option');
        option.value = value === null || value === undefined ? '' : String(value);
        option.textContent = label === null || label === undefined ? '' : String(label);

        if (selected) {
            option.selected = true;
        }

        return option;
    }

    function clearElement(element) {
        if (!element) {
            return;
        }

        element.innerHTML = '';
    }

    function safeText(value, fallback = '-') {
        if (value === null || value === undefined) {
            return fallback;
        }

        const text = String(value).trim();
        return text === '' ? fallback : text;
    }

    function setText(element, value, fallback = '-') {
        if (!element) {
            return;
        }

        element.textContent = safeText(value, fallback);
    }

    function renderEmptyState(container, message = 'Nu există date disponibile.') {
        if (!container) {
            return;
        }

        container.innerHTML = '';

        const state = document.createElement('div');
        state.className = 'dashboard-empty-state';
        state.textContent = message;

        container.appendChild(state);
    }

    function renderErrorState(container, message = 'A apărut o eroare la încărcarea datelor.') {
        if (!container) {
            return;
        }

        container.innerHTML = '';

        const state = document.createElement('div');
        state.className = 'dashboard-empty-state';
        state.textContent = message;

        container.appendChild(state);
    }

    function renderLoadingState(container, message = 'Se încarcă datele...') {
        if (!container) {
            return;
        }

        container.innerHTML = '';

        const state = document.createElement('div');
        state.className = 'dashboard-loading';
        state.textContent = message;

        container.appendChild(state);
    }

    function debounce(callback, delay = 300) {
        let timeoutId = null;

        return function debouncedFunction(...args) {
            window.clearTimeout(timeoutId);

            timeoutId = window.setTimeout(() => {
                callback.apply(this, args);
            }, delay);
        };
    }

    function buildQueryString(params = {}) {
        const searchParams = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') {
                return;
            }

            searchParams.set(key, String(value));
        });

        const queryString = searchParams.toString();
        return queryString ? `?${queryString}` : '';
    }

    function updateUrlQuery(params = {}) {
        const queryString = buildQueryString(params);
        const newUrl = `${window.location.pathname}${queryString}`;
        window.history.replaceState({}, '', newUrl);
    }

    function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};

        for (const [key, value] of params.entries()) {
            result[key] = value;
        }

        return result;
    }

    function populateSelect(selectElement, items, options = {}) {
        if (!selectElement) {
            return;
        }

        const {
            includeDefault = true,
            defaultValue = '',
            defaultLabel = 'Toate opțiunile',
            valueKey = 'value',
            labelKey = 'label',
            selectedValue = null,
        } = options;

        clearElement(selectElement);

        if (includeDefault) {
            selectElement.appendChild(createOption(defaultValue, defaultLabel, selectedValue === defaultValue));
        }

        if (!Array.isArray(items)) {
            return;
        }

        items.forEach((item) => {
            let value;
            let label;

            if (item && typeof item === 'object') {
                value = item[valueKey];
                label = item[labelKey];
            } else {
                value = item;
                label = item;
            }

            const option = createOption(value, label, String(selectedValue) === String(value));
            selectElement.appendChild(option);
        });
    }

    function getFormValues(formElement) {
        if (!formElement) {
            return {};
        }

        const formData = new FormData(formElement);
        const values = {};

        for (const [key, value] of formData.entries()) {
            values[key] = typeof value === 'string' ? value.trim() : value;
        }

        return values;
    }

    function normalizeFilters(filters = {}) {
        const normalized = {};

        Object.entries(filters).forEach(([key, value]) => {
            if (value === null || value === undefined) {
                normalized[key] = '';
                return;
            }

            normalized[key] = typeof value === 'string' ? value.trim() : value;
        });

        return normalized;
    }

    window.appUtils = {
        formatNumber,
        formatPercent,
        createOption,
        clearElement,
        safeText,
        setText,
        renderEmptyState,
        renderErrorState,
        renderLoadingState,
        debounce,
        buildQueryString,
        updateUrlQuery,
        getQueryParams,
        populateSelect,
        getFormValues,
        normalizeFilters,
    };
})();