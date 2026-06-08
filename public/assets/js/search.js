(function () {
    'use strict';

    const defaultYear = String(window.APP_DEFAULT_YEAR || new Date().getFullYear());
    const defaultLimit = Number(window.APP_DEFAULT_PAGE_SIZE || 25);
    const exportBaseUrl = window.APP_EXPORT_BASE_URL || 'api/export.php';

    const elements = {
        form: document.getElementById('search-filters-form'),
        resetButton: document.getElementById('reset-search-filters'),
        exportButton: document.getElementById('search-export-csv'),

        year: document.getElementById('search-filter-year'),
        county: document.getElementById('search-filter-county'),
        nationalCategory: document.getElementById('search-filter-national-category'),
        communityCategory: document.getElementById('search-filter-community-category'),
        fuelType: document.getElementById('search-filter-fuel-type'),
        brand: document.getElementById('search-filter-brand'),
        model: document.getElementById('search-filter-model'),
        limit: document.getElementById('search-filter-limit'),
        sortBy: document.getElementById('search-sort-by'),
        sortOrder: document.getElementById('search-sort-order'),

        selectionSummary: document.getElementById('search-selection-summary'),
        loadingState: document.getElementById('search-loading-state'),

        totalResults: document.getElementById('search-total-results'),
        currentPage: document.getElementById('search-current-page'),
        totalPages: document.getElementById('search-total-pages'),

        table: document.getElementById('search-results-table'),
        tableBody: document.querySelector('#search-results-table tbody'),

        paginationInfo: document.getElementById('search-pagination-info'),
        prevPageButton: document.getElementById('search-prev-page'),
        nextPageButton: document.getElementById('search-next-page'),

        emptyState: document.getElementById('search-empty-state'),
        sortableHeaders: document.querySelectorAll('#search-results-table th[data-sort-by]'),
    };

    const state = {
        filters: {
            year: defaultYear,
            county_code: '',
            national_category: '',
            community_category: '',
            fuel_type: '',
            brand: '',
            model: '',
            limit: String(defaultLimit),
            page: '1',
            sort_by: 'vehicle_count',
            sort_order: 'desc',
        },
        filterOptions: null,
        isLoading: false,
        total: 0,
        page: 1,
        pages: 1,
    };

    function ensureDependencies() {
        if (!window.appApi) {
            throw new Error('appApi nu este disponibil.');
        }

        if (!window.appUtils) {
            throw new Error('appUtils nu este disponibil.');
        }

        if (!window.appFilters) {
            throw new Error('appFilters nu este disponibil.');
        }
    }

    function setLoadingState(message) {
        if (elements.loadingState) {
            elements.loadingState.textContent = message;
        }
    }

    function setSelectionSummary() {
        if (!elements.selectionSummary) {
            return;
        }

        elements.selectionSummary.textContent = window.appFilters.getSelectedFilterSummary(state.filters);
    }

    function getCurrentFiltersFromForm() {
        if (!elements.form) {
            return { ...state.filters };
        }

        const values = window.appUtils.normalizeFilters(
            window.appUtils.getFormValues(elements.form)
        );

        return {
            year: values.year || '',
            county_code: values.county_code || '',
            national_category: values.national_category || '',
            community_category: values.community_category || '',
            fuel_type: values.fuel_type || '',
            brand: values.brand || '',
            model: values.model || '',
            limit: values.limit || String(defaultLimit),
            page: values.page || '1',
            sort_by: values.sort_by || 'vehicle_count',
            sort_order: values.sort_order || 'desc',
        };
    }

    function updateHiddenStateFields() {
        if (elements.sortBy) {
            elements.sortBy.value = state.filters.sort_by;
        }

        if (elements.sortOrder) {
            elements.sortOrder.value = state.filters.sort_order;
        }

        if (elements.limit) {
            elements.limit.value = state.filters.limit;
        }
    }

    function updateFilterHighlight() {
        if (!elements.form) {
            return;
        }

        const fields = elements.form.querySelectorAll('.form-field');

        fields.forEach((field) => {
            const input = field.querySelector('input, select, textarea');

            if (!input) {
                return;
            }

            let value;

            if (typeof input.value === 'string') {
                value = input.value.trim();
            } else {
                value = input.value;
            }

            const hasValue = value !== null && value !== undefined && value !== '';

            field.classList.toggle('is-active-filter', hasValue);
        });
    }

    function updateSortableHeaders() {
        elements.sortableHeaders.forEach((header) => {
            const sortBy = header.getAttribute('data-sort-by');
            const existingIndicator = header.querySelector('.sort-indicator');

            if (existingIndicator) {
                existingIndicator.remove();
            }

            header.classList.remove('is-sorted');

            if (sortBy === state.filters.sort_by) {
                header.classList.add('is-sorted');

                const indicator = document.createElement('span');
                indicator.className = 'sort-indicator';

                if (state.filters.sort_order === 'asc') {
                    indicator.textContent = '▲';
                } else {
                    indicator.textContent = '▼';
                }

                header.appendChild(indicator);
            }
        });
    }

    function updateMeta(total, page, pages) {
        state.total = Number(total || 0);
        state.page = Number(page || 1);
        state.pages = Number(pages || 1);

        window.appUtils.setText(elements.totalResults, window.appUtils.formatNumber(state.total));
        window.appUtils.setText(elements.currentPage, String(state.page));
        window.appUtils.setText(elements.totalPages, String(state.pages));

        if (elements.paginationInfo) {
            elements.paginationInfo.textContent =
                `Pagina ${state.page} din ${state.pages} · ${window.appUtils.formatNumber(state.total)} rezultate`;
        }

        if (elements.prevPageButton) {
            elements.prevPageButton.disabled = state.page <= 1;
        }

        if (elements.nextPageButton) {
            elements.nextPageButton.disabled = state.page >= state.pages;
        }
    }

    function renderTableRows(rows) {
        if (!elements.tableBody) {
            return;
        }

        window.appUtils.clearElement(elements.tableBody);

        if (!Array.isArray(rows) || rows.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 9;
            cell.textContent = 'Nu există rezultate pentru selecția curentă.';

            row.appendChild(cell);
            elements.tableBody.appendChild(row);

            if (elements.emptyState) {
                elements.emptyState.hidden = false;
            }

            return;
        }

        if (elements.emptyState) {
            elements.emptyState.hidden = true;
        }

        rows.forEach((item) => {
            const row = document.createElement('tr');

            const values = [
                item.year,
                item.county_code,
                item.county_name,
                item.national_category,
                item.community_category,
                item.brand_name,
                item.model_description,
                item.fuel_type,
                window.appUtils.formatNumber(item.vehicle_count),
            ];

            values.forEach((value, index) => {
                const cell = document.createElement('td');
                cell.textContent = window.appUtils.safeText(value, '-');

                if (index === values.length - 1) {
                    cell.style.textAlign = 'right';
                }

                row.appendChild(cell);
            });

            elements.tableBody.appendChild(row);
        });
    }

    function renderLoadingState() {
        if (!elements.tableBody) {
            return;
        }

        window.appUtils.clearElement(elements.tableBody);

        const row = document.createElement('tr');
        const cell = document.createElement('td');

        cell.colSpan = 9;
        cell.textContent = 'Se încarcă rezultatele...';

        row.appendChild(cell);
        elements.tableBody.appendChild(row);

        if (elements.emptyState) {
            elements.emptyState.hidden = true;
        }
    }

    function renderErrorState(message) {
        if (!elements.tableBody) {
            return;
        }

        window.appUtils.clearElement(elements.tableBody);

        const row = document.createElement('tr');
        const cell = document.createElement('td');

        cell.colSpan = 9;
        cell.textContent = message;

        row.appendChild(cell);
        elements.tableBody.appendChild(row);

        if (elements.emptyState) {
            elements.emptyState.hidden = true;
        }
    }

    function buildSearchQuery() {
        return {
            year: state.filters.year,
            county_code: state.filters.county_code,
            national_category: state.filters.national_category,
            community_category: state.filters.community_category,
            fuel_type: state.filters.fuel_type,
            brand: state.filters.brand,
            model: state.filters.model,
            page: state.filters.page,
            limit: state.filters.limit,
            sort_by: state.filters.sort_by,
            sort_order: state.filters.sort_order,
        };
    }

    function buildExportQuery() {
        return {
            resource: 'search',
            format: 'csv',
            year: state.filters.year,
            county_code: state.filters.county_code,
            national_category: state.filters.national_category,
            community_category: state.filters.community_category,
            fuel_type: state.filters.fuel_type,
            brand: state.filters.brand,
            model: state.filters.model,
            sort_by: state.filters.sort_by,
            sort_order: state.filters.sort_order,
            limit: state.filters.limit,
        };
    }

    function updateExportButton() {
        if (!elements.exportButton) {
            return;
        }

        const queryString = window.appUtils.buildQueryString(buildExportQuery());
        elements.exportButton.href = `${exportBaseUrl}${queryString}`;
    }

    function normalizeSearchResponse(payload) {
        if (!payload || typeof payload !== 'object') {
            return {
                rows: [],
                total: 0,
                page: 1,
                pages: 1,
            };
        }

        let rows = [];

        if (Array.isArray(payload.rows)) {
            rows = payload.rows;
        } else if (Array.isArray(payload.result)) {
            rows = payload.result;
        } else if (Array.isArray(payload.items)) {
            rows = payload.items;
        } else {
            rows = [];
        }

        const total = Number(payload.total || payload.total_results || 0);
        const page = Number(payload.page || 1);
        const pages = Number(payload.pages || payload.total_pages || 1);

        return { rows, total, page, pages };
    }

    async function fetchSearchResults() {
        const data = await window.appApi.getJson('search.php', buildSearchQuery());
        return normalizeSearchResponse(data);
    }

    async function initializeFilters() {
        const queryValues = window.appUtils.getQueryParams();

        const selectedValues = {
            year: queryValues.year || defaultYear,
            county_code: queryValues.county_code || '',
            national_category: queryValues.national_category || '',
            community_category: queryValues.community_category || '',
            fuel_type: queryValues.fuel_type || '',
            brand: queryValues.brand || '',
        };

        state.filters = {
            ...state.filters,
            year: selectedValues.year,
            county_code: selectedValues.county_code,
            national_category: selectedValues.national_category,
            community_category: selectedValues.community_category,
            fuel_type: selectedValues.fuel_type,
            brand: selectedValues.brand,
            model: queryValues.model || '',
            page: queryValues.page || '1',
            limit: queryValues.limit || String(defaultLimit),
            sort_by: queryValues.sort_by || 'vehicle_count',
            sort_order: queryValues.sort_order || 'desc',
        };

        state.filterOptions = await window.appFilters.loadFilters();

        window.appFilters.applyFiltersToForm(elements.form, state.filterOptions, {
            selectedValues,
            yearDefaultLabel: 'Toți anii disponibili',
            countyDefaultLabel: 'Toate județele',
            nationalCategoryDefaultLabel: 'Toate categoriile',
            communityCategoryDefaultLabel: 'Toate categoriile comunitare',
            fuelTypeDefaultLabel: 'Toate tipurile de combustibil',
            brandDefaultLabel: 'Toate mărcile',
        });

        if (elements.model) {
            elements.model.value = state.filters.model;
        }

        if (elements.limit) {
            elements.limit.value = state.filters.limit;
        }

        if (elements.sortBy) {
            elements.sortBy.value = state.filters.sort_by;
        }

        if (elements.sortOrder) {
            elements.sortOrder.value = state.filters.sort_order;
        }

        updateFilterHighlight();
        updateSortableHeaders();
        setSelectionSummary();
        updateExportButton();
    }

    async function executeSearch(event) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        state.filters = getCurrentFiltersFromForm();
        updateHiddenStateFields();
        updateFilterHighlight();
        setSelectionSummary();
        updateSortableHeaders();
        updateExportButton();

        window.appUtils.updateUrlQuery(buildSearchQuery());

        state.isLoading = true;
        setLoadingState('Se încarcă rezultatele căutării...');
        renderLoadingState();

        try {
            const result = await fetchSearchResults();

            renderTableRows(result.rows);
            updateMeta(result.total, result.page, result.pages);

            setLoadingState('Rezultate actualizate.');
        } catch (error) {
            let errorMessage = 'A apărut o eroare la încărcarea rezultatelor.';

            if (error instanceof Error) {
                errorMessage = error.message;
            }

            console.error('Eroare la încărcarea rezultatelor:', error);
            renderErrorState(errorMessage);

            updateMeta(0, 1, 1);
            setLoadingState('A apărut o eroare.');
        } finally {
            state.isLoading = false;
        }
    }

    function goToPage(page) {
        const safePage = Math.max(1, Number(page || 1));
        state.filters.page = String(safePage);

        if (elements.form) {
            const pageInput = elements.form.querySelector('input[name="page"]');

            if (pageInput) {
                pageInput.value = state.filters.page;
            }
        }

        updateExportButton();
        executeSearch();
    }

    function resetSearchFilters() {
        if (!elements.form) {
            return;
        }

        window.appFilters.resetFormFilters(elements.form);

        state.filters = {
            year: defaultYear,
            county_code: '',
            national_category: '',
            community_category: '',
            fuel_type: '',
            brand: '',
            model: '',
            limit: String(defaultLimit),
            page: '1',
            sort_by: 'vehicle_count',
            sort_order: 'desc',
        };

        if (elements.year) {
            elements.year.value = defaultYear;
        }

        if (elements.model) {
            elements.model.value = '';
        }

        updateHiddenStateFields();
        updateFilterHighlight();
        updateSortableHeaders();
        setSelectionSummary();
        updateExportButton();
        executeSearch();
    }

    function handleSortableHeaderClick(event) {
        const header = event.currentTarget;
        const sortBy = header.getAttribute('data-sort-by');

        if (!sortBy) {
            return;
        }

        if (state.filters.sort_by === sortBy) {
            if (state.filters.sort_order === 'asc') {
                state.filters.sort_order = 'desc';
            } else {
                state.filters.sort_order = 'asc';
            }
        } else {
            state.filters.sort_by = sortBy;
            state.filters.sort_order = 'asc';
        }

        state.filters.page = '1';
        updateHiddenStateFields();
        updateSortableHeaders();
        updateExportButton();
        executeSearch();
    }

    function bindEvents() {
        if (elements.form) {
            elements.form.addEventListener('submit', executeSearch);
        }

        if (elements.resetButton) {
            elements.resetButton.addEventListener('click', resetSearchFilters);
        }

        if (elements.prevPageButton) {
            elements.prevPageButton.addEventListener('click', () => {
                if (state.page > 1) {
                    goToPage(state.page - 1);
                }
            });
        }

        if (elements.nextPageButton) {
            elements.nextPageButton.addEventListener('click', () => {
                if (state.page < state.pages) {
                    goToPage(state.page + 1);
                }
            });
        }

        elements.sortableHeaders.forEach((header) => {
            header.addEventListener('click', handleSortableHeaderClick);
        });

        let formFields = [];

        if (elements.form) {
            formFields = elements.form.querySelectorAll('input, select, textarea');
        } else {
            formFields = [];
        }

        formFields.forEach((field) => {
            field.addEventListener('change', () => {
                updateFilterHighlight();
                state.filters = getCurrentFiltersFromForm();
                updateExportButton();
            });

            field.addEventListener('input', () => {
                updateFilterHighlight();
                state.filters = getCurrentFiltersFromForm();
                updateExportButton();
            });
        });

        if (elements.model) {
            const debouncedSearch = window.appUtils.debounce(() => {
                state.filters.page = '1';
                updateExportButton();
                executeSearch();
            }, 350);

            elements.model.addEventListener('input', debouncedSearch);
        }
    }

    async function init() {
        ensureDependencies();

        if (!elements.form || !elements.tableBody) {
            return;
        }

        setLoadingState('Se inițializează modulul de căutare...');

        try {
            await initializeFilters();
            bindEvents();
            await executeSearch();
        } catch (error) {
            let errorMessage = 'Nu s-a putut inițializa pagina de căutare.';

            if (error instanceof Error) {
                errorMessage = error.message;
            }

            console.error('Nu s-a putut inițializa pagina de căutare:', error);
            renderErrorState(errorMessage);
            setLoadingState('Inițializare eșuată.');
        }
    }

    window.addEventListener('DOMContentLoaded', init);
})();