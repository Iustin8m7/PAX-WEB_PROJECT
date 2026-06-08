(function () {
    'use strict';

    const FILTERS_ENDPOINT = 'filters.php';

    function ensureUtils() {
        if (!window.appUtils) {
            throw new Error('appUtils nu este disponibil.');
        }
    }

    function ensureApi() {
        if (!window.appApi) {
            throw new Error('appApi nu este disponibil.');
        }
    }

    function resetSelect(element, defaultLabel = 'Toate opțiunile') {
        ensureUtils();

        if (!element) {
            return;
        }

        window.appUtils.clearElement(element);
        element.appendChild(window.appUtils.createOption('', defaultLabel, true));
    }

    function populateSelect(element, items, options = {}) {
        ensureUtils();

        if (!element) {
            return;
        }

        const {
            includeDefault = false,
            defaultValue = '',
            defaultLabel = 'Toate opțiunile',
            valueKey = 'value',
            labelKey = 'label',
            selectedValue = null,
            plainArray = false,
        } = options;

        window.appUtils.clearElement(element);

        if (includeDefault) {
            element.appendChild(
                window.appUtils.createOption(
                    defaultValue,
                    defaultLabel,
                    String(selectedValue) === String(defaultValue)
                )
            );
        }

        if (!Array.isArray(items)) {
            return;
        }

        items.forEach((item) => {
            let value;
            let label;

            if (plainArray) {
                value = item;
                label = item;
            } else if (item && typeof item === 'object') {
                value = item[valueKey];
                label = item[labelKey];
            } else {
                value = item;
                label = item;
            }

            element.appendChild(
                window.appUtils.createOption(
                    value,
                    label,
                    String(selectedValue) === String(value)
                )
            );
        });
    }

    function getDefaultLabelByType(type) {
        const labels = {
            years: 'Toți anii disponibili',
            counties: 'Toate județele',
            nationalCategories: 'Toate categoriile',
            communityCategories: 'Toate categoriile comunitare',
            fuelTypes: 'Toate tipurile de combustibil',
            brands: 'Toate mărcile',
        };

        return labels[type] || 'Toate opțiunile';
    }

    function normalizeFilterPayload(data) {
        return {
            years: Array.isArray(data?.years) ? data.years : [],
            counties: Array.isArray(data?.counties) ? data.counties : [],
            nationalCategories: Array.isArray(data?.nationalCategories) ? data.nationalCategories : [],
            communityCategories: Array.isArray(data?.communityCategories) ? data.communityCategories : [],
            fuelTypes: Array.isArray(data?.fuelTypes) ? data.fuelTypes : [],
            brands: Array.isArray(data?.brands) ? data.brands : [],
        };
    }

    async function loadFilters() {
        ensureApi();

        const data = await window.appApi.getJson(FILTERS_ENDPOINT);
        return normalizeFilterPayload(data);
    }

    function fillYears(selectElement, years, selectedValue = null, defaultLabel = null) {
        populateSelect(selectElement, years, {
            includeDefault: true,
            defaultLabel: defaultLabel || getDefaultLabelByType('years'),
            selectedValue,
            plainArray: true,
        });
    }

    function fillCounties(selectElement, counties, selectedValue = null, defaultLabel = null) {
        populateSelect(selectElement, counties, {
            includeDefault: true,
            defaultLabel: defaultLabel || getDefaultLabelByType('counties'),
            selectedValue,
            valueKey: 'code',
            labelKey: 'name',
        });
    }

    function fillNamedOptions(selectElement, items, selectedValue = null, defaultLabel = 'Toate opțiunile') {
        populateSelect(selectElement, items, {
            includeDefault: true,
            defaultLabel,
            selectedValue,
            valueKey: 'name',
            labelKey: 'name',
        });
    }

    function applyFiltersToForm(formElement, filtersData, options = {}) {
        if (!formElement || !filtersData) {
            return;
        }

        const selectedValues = options.selectedValues || {};

        const yearSelect = formElement.querySelector('select[name="year"]');
        const countySelect = formElement.querySelector('select[name="county_code"]');
        const nationalCategorySelect = formElement.querySelector('select[name="national_category"]');
        const communityCategorySelect = formElement.querySelector('select[name="community_category"]');
        const fuelTypeSelect = formElement.querySelector('select[name="fuel_type"]');
        const brandSelect = formElement.querySelector('select[name="brand"]');

        if (yearSelect) {
            fillYears(
                yearSelect,
                filtersData.years,
                selectedValues.year ?? '',
                options.yearDefaultLabel || getDefaultLabelByType('years')
            );
        }

        if (countySelect) {
            fillCounties(
                countySelect,
                filtersData.counties,
                selectedValues.county_code ?? '',
                options.countyDefaultLabel || getDefaultLabelByType('counties')
            );
        }

        if (nationalCategorySelect) {
            fillNamedOptions(
                nationalCategorySelect,
                filtersData.nationalCategories,
                selectedValues.national_category ?? '',
                options.nationalCategoryDefaultLabel || getDefaultLabelByType('nationalCategories')
            );
        }

        if (communityCategorySelect) {
            fillNamedOptions(
                communityCategorySelect,
                filtersData.communityCategories,
                selectedValues.community_category ?? '',
                options.communityCategoryDefaultLabel || getDefaultLabelByType('communityCategories')
            );
        }

        if (fuelTypeSelect) {
            fillNamedOptions(
                fuelTypeSelect,
                filtersData.fuelTypes,
                selectedValues.fuel_type ?? '',
                options.fuelTypeDefaultLabel || getDefaultLabelByType('fuelTypes')
            );
        }

        if (brandSelect) {
            fillNamedOptions(
                brandSelect,
                filtersData.brands,
                selectedValues.brand ?? '',
                options.brandDefaultLabel || getDefaultLabelByType('brands')
            );
        }
    }

    function resetFormFilters(formElement) {
        if (!formElement) {
            return;
        }

        formElement.reset();

        const selects = formElement.querySelectorAll('select');
        selects.forEach((select) => {
            if (select.options.length > 0) {
                select.selectedIndex = 0;
            }
        });
    }

    function getSelectedFilterSummary(filters = {}) {
        const parts = [];

        if (filters.year) {
            parts.push(`An: ${filters.year}`);
        }

        if (filters.county_code) {
            parts.push(`Județ: ${filters.county_code}`);
        }

        if (filters.national_category) {
            parts.push(`Categorie națională: ${filters.national_category}`);
        }

        if (filters.community_category) {
            parts.push(`Categorie comunitară: ${filters.community_category}`);
        }

        if (filters.fuel_type) {
            parts.push(`Combustibil: ${filters.fuel_type}`);
        }

        if (filters.brand) {
            parts.push(`Marcă: ${filters.brand}`);
        }

        if (filters.model) {
            parts.push(`Model: ${filters.model}`);
        }

        return parts.length > 0 ? parts.join(' · ') : 'Nu sunt active filtre specifice.';
    }

    window.appFilters = {
        resetSelect,
        populateSelect,
        loadFilters,
        fillYears,
        fillCounties,
        fillNamedOptions,
        applyFiltersToForm,
        resetFormFilters,
        getSelectedFilterSummary,
    };
})();