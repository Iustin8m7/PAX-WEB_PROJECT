(function () {
    'use strict';

    const defaultYear = String(window.APP_DEFAULT_YEAR || new Date().getFullYear());

    const elements = {
        form: document.getElementById('compare-form'),
        resetButton: document.getElementById('reset-compare-filters'),

        yearA: document.getElementById('compare-a-year'),
        countyA: document.getElementById('compare-a-county'),
        nationalCategoryA: document.getElementById('compare-a-national-category'),
        fuelTypeA: document.getElementById('compare-a-fuel-type'),
        brandA: document.getElementById('compare-a-brand'),
        modelA: document.getElementById('compare-a-model'),

        yearB: document.getElementById('compare-b-year'),
        countyB: document.getElementById('compare-b-county'),
        nationalCategoryB: document.getElementById('compare-b-national-category'),
        fuelTypeB: document.getElementById('compare-b-fuel-type'),
        brandB: document.getElementById('compare-b-brand'),
        modelB: document.getElementById('compare-b-model'),

        summary: document.getElementById('compare-summary'),
        loadingState: document.getElementById('compare-loading-state'),
        contextSummary: document.getElementById('compare-context-summary'),

        totalA: document.getElementById('compare-total-a'),
        totalB: document.getElementById('compare-total-b'),
        diffAbsolute: document.getElementById('compare-difference-absolute'),
        diffPercent: document.getElementById('compare-difference-percent'),

        chartContainer: document.getElementById('compare-main-chart'),
        chartCaption: document.getElementById('compare-main-chart-caption'),

        topCountiesATable: document.querySelector('#compare-top-counties-a-table tbody'),
        topCountiesBTable: document.querySelector('#compare-top-counties-b-table tbody'),
        topModelsATable: document.querySelector('#compare-top-models-a-table tbody'),
        topModelsBTable: document.querySelector('#compare-top-models-b-table tbody'),
    };

    const state = {
        filtersA: {
            year: defaultYear,
            county_code: '',
            national_category: '',
            fuel_type: '',
            brand: '',
            model: '',
        },
        filtersB: {
            year: defaultYear,
            county_code: '',
            national_category: '',
            fuel_type: '',
            brand: '',
            model: '',
        },
        filterOptions: null,
        isLoading: false,
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

        if (!window.appCharts) {
            throw new Error('appCharts nu este disponibil.');
        }
    }

    function setLoadingState(message) {
        if (elements.loadingState) {
            elements.loadingState.textContent = message;
        }
    }

    function setContextSummary(message) {
        if (elements.contextSummary) {
            elements.contextSummary.textContent = message;
        }
    }

    function setSummary(message) {
        if (elements.summary) {
            elements.summary.textContent = message;
        }
    }

    function getSelectionSummary(filters) {
        return window.appFilters.getSelectedFilterSummary(filters);
    }

    function setSelectionSummary() {
        const summaryA = getSelectionSummary(state.filtersA);
        const summaryB = getSelectionSummary(state.filtersB);

        setSummary(`Selecția A: ${summaryA} | Selecția B: ${summaryB}`);
    }

    function getFiltersFromForm(prefix) {
        if (!elements.form) {
            return {
                year: defaultYear,
                county_code: '',
                national_category: '',
                fuel_type: '',
                brand: '',
                model: '',
            };
        }

        const year = elements.form.querySelector(`[name="${prefix}_year"]`);
        const county = elements.form.querySelector(`[name="${prefix}_county_code"]`);
        const nationalCategory = elements.form.querySelector(`[name="${prefix}_national_category"]`);
        const fuelType = elements.form.querySelector(`[name="${prefix}_fuel_type"]`);
        const brand = elements.form.querySelector(`[name="${prefix}_brand"]`);
        const model = elements.form.querySelector(`[name="${prefix}_model"]`);

        return {
            year: year && year.value ? year.value.trim() : '',
            county_code: county && county.value ? county.value.trim() : '',
            national_category: nationalCategory && nationalCategory.value ? nationalCategory.value.trim() : '',
            fuel_type: fuelType && fuelType.value ? fuelType.value.trim() : '',
            brand: brand && brand.value ? brand.value.trim() : '',
            model: model && model.value ? model.value.trim() : '',
        };
    }

    function buildSearchQuery(filters) {
        return {
            year: filters.year,
            county_code: filters.county_code,
            national_category: filters.national_category,
            fuel_type: filters.fuel_type,
            brand: filters.brand,
            model: filters.model,
            page: 1,
            limit: 100,
            sort_by: 'vehicle_count',
            sort_order: 'desc',
        };
    }

    function normalizeSearchResponse(data) {
        if (!data || typeof data !== 'object') {
            return [];
        }

        if (Array.isArray(data.rows)) {
            return data.rows;
        }

        if (Array.isArray(data.result)) {
            return data.result;
        }

        if (Array.isArray(data.items)) {
            return data.items;
        }

        return [];
    }

    async function fetchDataset(filters) {
        const payload = await window.appApi.getJson('search.php', buildSearchQuery(filters));
        return normalizeSearchResponse(payload);
    }

    function sumVehicleCount(rows) {
        if (!Array.isArray(rows)) {
            return 0;
        }

        return rows.reduce((sum, row) => sum + Number(row.vehicle_count || 0), 0);
    }

    function computeDifference(a, b) {
        return a - b;
    }

    function computePercentDifference(a, b) {
        if (b === 0) {
            return null;
        }

        return ((a - b) / b) * 100;
    }

    function aggregateTopCounties(rows, limit = 10) {
        const aggregated = new Map();

        rows.forEach((row) => {
            const code = row.county_code || '-';
            const current = aggregated.get(code) || {
                county_code: code,
                county_name: row.county_name || 'Necunoscut',
                value: 0,
            };

            current.value += Number(row.vehicle_count || 0);
            aggregated.set(code, current);
        });

        return Array.from(aggregated.values())
            .sort((a, b) => b.value - a.value)
            .slice(0, limit);
    }

    function aggregateTopModels(rows, limit = 10) {
        const aggregated = new Map();

        rows.forEach((row) => {
            const modelKey = row.model_description || 'Necunoscut';
            const brandName = row.brand_name || 'Necunoscut';
            const compositeKey = `${brandName}|||${modelKey}`;

            const current = aggregated.get(compositeKey) || {
                brand_name: brandName,
                model_description: modelKey,
                value: 0,
            };

            current.value += Number(row.vehicle_count || 0);
            aggregated.set(compositeKey, current);
        });

        return Array.from(aggregated.values())
            .sort((a, b) => b.value - a.value)
            .slice(0, limit);
    }

    function renderSummary(totalA, totalB) {
        window.appUtils.setText(elements.totalA, window.appUtils.formatNumber(totalA));
        window.appUtils.setText(elements.totalB, window.appUtils.formatNumber(totalB));

        const diff = computeDifference(totalA, totalB);
        const diffPercent = computePercentDifference(totalA, totalB);

        if (elements.diffAbsolute) {
            elements.diffAbsolute.textContent =
                diff >= 0
                    ? `+${window.appUtils.formatNumber(diff)}`
                    : `-${window.appUtils.formatNumber(Math.abs(diff))}`;

            elements.diffAbsolute.classList.remove('compare-positive', 'compare-negative', 'compare-neutral');

            if (diff > 0) {
                elements.diffAbsolute.classList.add('compare-positive');
            } else if (diff < 0) {
                elements.diffAbsolute.classList.add('compare-negative');
            } else {
                elements.diffAbsolute.classList.add('compare-neutral');
            }
        }

        if (elements.diffPercent) {
            if (diffPercent === null) {
                elements.diffPercent.textContent = '-';
                elements.diffPercent.classList.remove('compare-positive', 'compare-negative', 'compare-neutral');
                elements.diffPercent.classList.add('compare-neutral');
            } else {
                elements.diffPercent.textContent =
                    diffPercent >= 0
                        ? `+${window.appUtils.formatPercent(diffPercent)}`
                        : `-${window.appUtils.formatPercent(Math.abs(diffPercent))}`;

                elements.diffPercent.classList.remove('compare-positive', 'compare-negative', 'compare-neutral');

                if (diffPercent > 0) {
                    elements.diffPercent.classList.add('compare-positive');
                } else if (diffPercent < 0) {
                    elements.diffPercent.classList.add('compare-negative');
                } else {
                    elements.diffPercent.classList.add('compare-neutral');
                }
            }
        }
    }

    function renderCountyTableRows(tbody, rows, emptyMessage) {
        if (!tbody) {
            return;
        }

        window.appUtils.clearElement(tbody);

        if (!Array.isArray(rows) || rows.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.textContent = emptyMessage;
            row.appendChild(cell);
            tbody.appendChild(row);
            return;
        }

        rows.forEach((item, index) => {
            const row = document.createElement('tr');

            const rankCell = document.createElement('td');
            rankCell.textContent = String(index + 1);

            const codeCell = document.createElement('td');
            codeCell.textContent = window.appUtils.safeText(item.county_code, '-');

            const nameCell = document.createElement('td');
            nameCell.textContent = window.appUtils.safeText(item.county_name, 'Necunoscut');

            const valueCell = document.createElement('td');
            valueCell.textContent = window.appUtils.formatNumber(item.value);
            valueCell.style.textAlign = 'right';

            row.appendChild(rankCell);
            row.appendChild(codeCell);
            row.appendChild(nameCell);
            row.appendChild(valueCell);

            tbody.appendChild(row);
        });
    }

    function renderModelTableRows(tbody, rows, emptyMessage) {
        if (!tbody) {
            return;
        }

        window.appUtils.clearElement(tbody);

        if (!Array.isArray(rows) || rows.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.textContent = emptyMessage;
            row.appendChild(cell);
            tbody.appendChild(row);
            return;
        }

        rows.forEach((item, index) => {
            const row = document.createElement('tr');

            const rankCell = document.createElement('td');
            rankCell.textContent = String(index + 1);

            const brandCell = document.createElement('td');
            brandCell.textContent = window.appUtils.safeText(item.brand_name, 'Necunoscut');

            const modelCell = document.createElement('td');
            modelCell.textContent = window.appUtils.safeText(item.model_description, 'Necunoscut');

            const valueCell = document.createElement('td');
            valueCell.textContent = window.appUtils.formatNumber(item.value);
            valueCell.style.textAlign = 'right';

            row.appendChild(rankCell);
            row.appendChild(brandCell);
            row.appendChild(modelCell);
            row.appendChild(valueCell);

            tbody.appendChild(row);
        });
    }

    function renderCompareChart(totalA, totalB) {
        if (!elements.chartContainer) {
            return;
        }

        window.appCharts.createTopBrandsChart(elements.chartContainer, [
            {
                name: 'Selecția A',
                total_vehicles: totalA,
            },
            {
                name: 'Selecția B',
                total_vehicles: totalB,
            },
        ]);

        if (elements.chartCaption) {
            elements.chartCaption.textContent =
                'Graficul compară volumele totale agregate pentru selecția A și selecția B.';
        }
    }

    function renderAll(datasetA, datasetB) {
        const totalA = sumVehicleCount(datasetA);
        const totalB = sumVehicleCount(datasetB);

        renderSummary(totalA, totalB);
        renderCompareChart(totalA, totalB);

        const topCountiesA = aggregateTopCounties(datasetA, 10);
        const topCountiesB = aggregateTopCounties(datasetB, 10);

        const topModelsA = aggregateTopModels(datasetA, 10);
        const topModelsB = aggregateTopModels(datasetB, 10);

        renderCountyTableRows(
            elements.topCountiesATable,
            topCountiesA,
            'Nu există date pentru județele din selecția A.'
        );

        renderCountyTableRows(
            elements.topCountiesBTable,
            topCountiesB,
            'Nu există date pentru județele din selecția B.'
        );

        renderModelTableRows(
            elements.topModelsATable,
            topModelsA,
            'Nu există date pentru modelele din selecția A.'
        );

        renderModelTableRows(
            elements.topModelsBTable,
            topModelsB,
            'Nu există date pentru modelele din selecția B.'
        );

        const summaryA = getSelectionSummary(state.filtersA);
        const summaryB = getSelectionSummary(state.filtersB);

        setContextSummary(
            `Comparația activă pune față în față două selecții distincte. Selecția A: ${summaryA}. Selecția B: ${summaryB}. Indicatorii sintetici, topurile teritoriale și distribuția modelelor sunt actualizate pe baza acestui context.`
        );
    }

    function renderLoadingState() {
        window.appUtils.setText(elements.totalA, '-');
        window.appUtils.setText(elements.totalB, '-');
        window.appUtils.setText(elements.diffAbsolute, '-');
        window.appUtils.setText(elements.diffPercent, '-');

        if (elements.chartContainer) {
            window.appCharts.destroyChart(elements.chartContainer.id);
            window.appUtils.renderLoadingState(elements.chartContainer, 'Se încarcă graficul comparativ...');
        }

        renderCountyTableRows(elements.topCountiesATable, [], 'Se încarcă...');
        renderCountyTableRows(elements.topCountiesBTable, [], 'Se încarcă...');
        renderModelTableRows(elements.topModelsATable, [], 'Se încarcă...');
        renderModelTableRows(elements.topModelsBTable, [], 'Se încarcă...');
    }

    function renderErrorState(message) {
        window.appUtils.setText(elements.totalA, '-');
        window.appUtils.setText(elements.totalB, '-');
        window.appUtils.setText(elements.diffAbsolute, '-');
        window.appUtils.setText(elements.diffPercent, '-');

        if (elements.chartContainer) {
            window.appCharts.destroyChart(elements.chartContainer.id);
            window.appUtils.renderErrorState(elements.chartContainer, message);
        }

        renderCountyTableRows(elements.topCountiesATable, [], message);
        renderCountyTableRows(elements.topCountiesBTable, [], message);
        renderModelTableRows(elements.topModelsATable, [], message);
        renderModelTableRows(elements.topModelsBTable, [], message);
    }

    async function initializeFilters() {
        state.filterOptions = await window.appFilters.loadFilters();

        const queryValues = window.appUtils.getQueryParams();

        state.filtersA = {
            year: queryValues.a_year || defaultYear,
            county_code: queryValues.a_county_code || '',
            national_category: queryValues.a_national_category || '',
            fuel_type: queryValues.a_fuel_type || '',
            brand: queryValues.a_brand || '',
            model: queryValues.a_model || '',
        };

        state.filtersB = {
            year: queryValues.b_year || defaultYear,
            county_code: queryValues.b_county_code || '',
            national_category: queryValues.b_national_category || '',
            fuel_type: queryValues.b_fuel_type || '',
            brand: queryValues.b_brand || '',
            model: queryValues.b_model || '',
        };

        window.appFilters.fillYears(elements.yearA, state.filterOptions.years, state.filtersA.year, 'Toți anii disponibili');
        window.appFilters.fillCounties(elements.countyA, state.filterOptions.counties, state.filtersA.county_code, 'Toate județele');
        window.appFilters.fillNamedOptions(elements.nationalCategoryA, state.filterOptions.nationalCategories, state.filtersA.national_category, 'Toate categoriile');
        window.appFilters.fillNamedOptions(elements.fuelTypeA, state.filterOptions.fuelTypes, state.filtersA.fuel_type, 'Toate tipurile de combustibil');
        window.appFilters.fillNamedOptions(elements.brandA, state.filterOptions.brands, state.filtersA.brand, 'Toate mărcile');

        window.appFilters.fillYears(elements.yearB, state.filterOptions.years, state.filtersB.year, 'Toți anii disponibili');
        window.appFilters.fillCounties(elements.countyB, state.filterOptions.counties, state.filtersB.county_code, 'Toate județele');
        window.appFilters.fillNamedOptions(elements.nationalCategoryB, state.filterOptions.nationalCategories, state.filtersB.national_category, 'Toate categoriile');
        window.appFilters.fillNamedOptions(elements.fuelTypeB, state.filterOptions.fuelTypes, state.filtersB.fuel_type, 'Toate tipurile de combustibil');
        window.appFilters.fillNamedOptions(elements.brandB, state.filterOptions.brands, state.filtersB.brand, 'Toate mărcile');

        if (elements.modelA) {
            elements.modelA.value = state.filtersA.model;
        }

        if (elements.modelB) {
            elements.modelB.value = state.filtersB.model;
        }

        setSelectionSummary();
    }

    function updateStateFromForm() {
        state.filtersA = getFiltersFromForm('a');
        state.filtersB = getFiltersFromForm('b');
        setSelectionSummary();
    }

    function updateUrl() {
        window.appUtils.updateUrlQuery({
            a_year: state.filtersA.year,
            a_county_code: state.filtersA.county_code,
            a_national_category: state.filtersA.national_category,
            a_fuel_type: state.filtersA.fuel_type,
            a_brand: state.filtersA.brand,
            a_model: state.filtersA.model,

            b_year: state.filtersB.year,
            b_county_code: state.filtersB.county_code,
            b_national_category: state.filtersB.national_category,
            b_fuel_type: state.filtersB.fuel_type,
            b_brand: state.filtersB.brand,
            b_model: state.filtersB.model,
        });
    }

    async function executeCompare(event) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        updateStateFromForm();
        updateUrl();

        state.isLoading = true;
        setLoadingState('Se încarcă comparația...');
        renderLoadingState();

        try {
            const [datasetA, datasetB] = await Promise.all([
                fetchDataset(state.filtersA),
                fetchDataset(state.filtersB),
            ]);

            renderAll(datasetA, datasetB);
            setLoadingState('Comparație actualizată.');
        } catch (error) {
            const errorMessage = error instanceof Error
                ? error.message
                : 'A apărut o eroare la încărcarea comparației.';

            console.error('Eroare la încărcarea comparației:', error);
            renderErrorState(errorMessage);
            setContextSummary(errorMessage);
            setLoadingState('A apărut o eroare.');
        } finally {
            state.isLoading = false;
        }
    }

    function resetCompare() {
        if (!elements.form) {
            return;
        }

        window.appFilters.resetFormFilters(elements.form);

        state.filtersA = {
            year: defaultYear,
            county_code: '',
            national_category: '',
            fuel_type: '',
            brand: '',
            model: '',
        };

        state.filtersB = {
            year: defaultYear,
            county_code: '',
            national_category: '',
            fuel_type: '',
            brand: '',
            model: '',
        };

        if (elements.yearA) {
            elements.yearA.value = defaultYear;
        }

        if (elements.yearB) {
            elements.yearB.value = defaultYear;
        }

        if (elements.modelA) {
            elements.modelA.value = '';
        }

        if (elements.modelB) {
            elements.modelB.value = '';
        }

        setSelectionSummary();
        executeCompare();
    }

    function bindEvents() {
        if (elements.form) {
            elements.form.addEventListener('submit', executeCompare);
        }

        if (elements.resetButton) {
            elements.resetButton.addEventListener('click', resetCompare);
        }
    }

    async function init() {
        ensureDependencies();

        if (!elements.form) {
            return;
        }

        setLoadingState('Se inițializează comparația...');

        try {
            await initializeFilters();
            bindEvents();
            await executeCompare();
        } catch (error) {
            const errorMessage = error instanceof Error
                ? error.message
                : 'Nu s-a putut inițializa pagina de comparații.';

            console.error('Nu s-a putut inițializa pagina de comparații:', error);
            renderErrorState(errorMessage);
            setLoadingState('Inițializare eșuată.');
        }
    }

    window.addEventListener('DOMContentLoaded', init);
})();