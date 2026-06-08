(function () {
    'use strict';

    const defaultYear = Number(window.APP_DEFAULT_YEAR || new Date().getFullYear());
    const exportBaseUrl = window.APP_EXPORT_BASE_URL || 'api/export.php';

    const elements = {
        form: document.getElementById('dashboard-filters-form'),
        year: document.getElementById('filter-year'),
        county: document.getElementById('filter-county'),
        nationalCategory: document.getElementById('filter-national-category'),
        communityCategory: document.getElementById('filter-community-category'),
        fuelType: document.getElementById('filter-fuel-type'),
        brand: document.getElementById('filter-brand'),
        reset: document.getElementById('reset-dashboard-filters'),

        exportOverview: document.getElementById('dashboard-export-overview'),
        exportYearly: document.getElementById('dashboard-export-yearly'),
        exportRanking: document.getElementById('dashboard-export-ranking'),
        exportMapData: document.getElementById('dashboard-export-map-data'),
        exportYearlyTotalsChart: document.getElementById('chart-export-yearly-totals'),
        exportTopBrandsChart: document.getElementById('chart-export-top-brands'),
        exportFuelDistributionChart: document.getElementById('chart-export-fuel-distribution'),
        exportCategoryDistributionChart: document.getElementById('chart-export-category-distribution'),
        exportCountyRankingChart: document.getElementById('chart-export-county-ranking'),

        exportYearlyTotalsWebp: document.getElementById('chart-export-yearly-totals-webp'),
        exportTopBrandsWebp: document.getElementById('chart-export-top-brands-webp'),
        exportFuelDistributionWebp: document.getElementById('chart-export-fuel-distribution-webp'),
        exportCategoryDistributionWebp: document.getElementById('chart-export-category-distribution-webp'),

        overviewTotal: document.getElementById('overview-total-vehicles'),
        overviewCounties: document.getElementById('overview-counties-count'),
        overviewBrands: document.getElementById('overview-brands-count'),
        overviewFuelTypes: document.getElementById('overview-fuel-types-count'),
        overviewCategories: document.getElementById('overview-categories-count'),

        yearlyTotals: document.getElementById('chart-yearly-totals'),
        yearlyTotalsCaption: document.getElementById('chart-yearly-totals-caption'),

        topBrands: document.getElementById('chart-top-brands'),
        topBrandsCaption: document.getElementById('chart-top-brands-caption'),

        fuelDistribution: document.getElementById('chart-fuel-distribution'),
        fuelDistributionCaption: document.getElementById('chart-fuel-distribution-caption'),

        categoryDistribution: document.getElementById('chart-category-distribution'),
        categoryDistributionCaption: document.getElementById('chart-category-distribution-caption'),

        countyRanking: document.getElementById('county-ranking-table'),
        mapPreview: document.getElementById('dashboard-map-preview'),
        mapSummary: document.getElementById('dashboard-map-summary'),

        selectionSummary: document.getElementById('dashboard-selection-summary'),
        loadingState: document.getElementById('dashboard-loading-state'),
        contextSummary: document.getElementById('dashboard-context-summary'),
    };

    const state = {
        filters: {
            year: String(defaultYear),
            county_code: '',
            national_category: '',
            community_category: '',
            fuel_type: '',
            brand: '',
        },
        filterOptions: null,
        loading: false,
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

        if (!window.appExportImage) {
            throw new Error('appExportImage nu este disponibil.');
        }
    }

    function extractResult(payload) {
        if (payload && typeof payload === 'object' && Object.prototype.hasOwnProperty.call(payload, 'result')) {
            return payload.result;
        }

        return payload;
    }

    function getCurrentFiltersFromForm() {
        if (!elements.form) {
            return { ...state.filters };
        }

        const rawValues = window.appUtils.getFormValues(elements.form);
        const normalized = window.appUtils.normalizeFilters(rawValues);

        return {
            year: normalized.year || '',
            county_code: normalized.county_code || '',
            national_category: normalized.national_category || '',
            community_category: normalized.community_category || '',
            fuel_type: normalized.fuel_type || '',
            brand: normalized.brand || '',
        };
    }

    function getStatisticsQuery(filters, extraParams = {}) {
        return {
            year: filters.year || '',
            county_code: filters.county_code || '',
            national_category: filters.national_category || '',
            community_category: filters.community_category || '',
            fuel_type: filters.fuel_type || '',
            brand: filters.brand || '',
            ...extraParams,
        };
    }

    function buildExportHref(resource, params = {}) {
        const queryString = window.appUtils.buildQueryString({
            resource,
            format: 'csv',
            ...params,
        });

        return `${exportBaseUrl}${queryString}`;
    }

    function updateExportLinks() {
        const filters = { ...state.filters };

        if (elements.exportOverview) {
            elements.exportOverview.href = buildExportHref('statistics', {
                view: 'overview',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportYearly) {
            elements.exportYearly.href = buildExportHref('statistics', {
                view: 'yearly-totals',
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportRanking) {
            elements.exportRanking.href = buildExportHref('statistics', {
                view: 'county-ranking',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportMapData) {
            elements.exportMapData.href = buildExportHref('map', {
                year: filters.year,
                fuel_type: filters.fuel_type,
                national_category: filters.national_category,
            });
        }

        if (elements.exportYearlyTotalsChart) {
            elements.exportYearlyTotalsChart.href = buildExportHref('statistics', {
                view: 'yearly-totals',
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportTopBrandsChart) {
            elements.exportTopBrandsChart.href = buildExportHref('statistics', {
                view: 'top-brands',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
                limit: 10,
            });
        }

        if (elements.exportFuelDistributionChart) {
            elements.exportFuelDistributionChart.href = buildExportHref('statistics', {
                view: 'fuel-distribution',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportCategoryDistributionChart) {
            elements.exportCategoryDistributionChart.href = buildExportHref('statistics', {
                view: 'category-distribution',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }

        if (elements.exportCountyRankingChart) {
            elements.exportCountyRankingChart.href = buildExportHref('statistics', {
                view: 'county-ranking',
                year: filters.year,
                county_code: filters.county_code,
                national_category: filters.national_category,
                community_category: filters.community_category,
                fuel_type: filters.fuel_type,
                brand: filters.brand,
            });
        }
    }

    function buildWebpBaseName(chartKey) {
        const parts = [
            'dashboard',
            chartKey,
            state.filters.year || 'all-years',
            state.filters.county_code || 'all-counties',
            state.filters.national_category || 'all-national-categories',
            state.filters.community_category || 'all-community-categories',
            state.filters.fuel_type || 'all-fuels',
            state.filters.brand || 'all-brands',
        ];

        return parts.join('_');
    }

    function exportChartAsWebp(containerId, chartKey, options = {}) {
    try {
        if (!window.appExportImage.isCanvasExportable(containerId)) {
            throw new Error('Graficul nu este încă pregătit pentru export.');
        }

        const fileName = buildWebpBaseName(chartKey);

        window.appExportImage.exportCanvasToWebP(containerId, {
            fileName,
            quality: 0.95,
            backgroundColor: options.backgroundColor || '#0f172a',
        });
    } catch (error) {
        console.error('Eroare la exportul WebP:', error);
        alert(error instanceof Error ? error.message : 'Nu s-a putut genera exportul WebP.');
    }
}

    function bindWebpExportButtons() {
        if (elements.exportYearlyTotalsWebp) {
            elements.exportYearlyTotalsWebp.addEventListener('click', () => {
                exportChartAsWebp('chart-yearly-totals', 'yearly-totals');
            });
        }

        if (elements.exportTopBrandsWebp) {
            elements.exportTopBrandsWebp.addEventListener('click', () => {
                exportChartAsWebp('chart-top-brands', 'top-brands');
            });
        }

        if (elements.exportFuelDistributionWebp) {
            elements.exportFuelDistributionWebp.addEventListener('click', () => {
                exportChartAsWebp('chart-fuel-distribution', 'fuel-distribution');
            });
        }

        if (elements.exportCategoryDistributionWebp) {
            elements.exportCategoryDistributionWebp.addEventListener('click', () => {
                exportChartAsWebp('chart-category-distribution', 'category-distribution');
            });
        }
    }

    function updateUrlFromFilters() {
        window.appUtils.updateUrlQuery({
            year: state.filters.year,
            county_code: state.filters.county_code,
            national_category: state.filters.national_category,
            community_category: state.filters.community_category,
            fuel_type: state.filters.fuel_type,
            brand: state.filters.brand,
        });
    }

    function setLoadingState(message) {
        if (elements.loadingState) {
            elements.loadingState.textContent = message;
        }
    }

    function setSelectionSummary() {
        if (elements.selectionSummary) {
            elements.selectionSummary.textContent = window.appFilters.getSelectedFilterSummary(state.filters);
        }
    }

    function setContextSummary(message) {
        if (elements.contextSummary) {
            elements.contextSummary.textContent = message;
        }
    }

    function setMapSummary(message) {
        if (elements.mapSummary) {
            elements.mapSummary.textContent = message;
        }
    }

    function renderOverview(overview) {
        const data = overview || {};

        window.appUtils.setText(elements.overviewTotal, window.appUtils.formatNumber(data.total_vehicles));
        window.appUtils.setText(elements.overviewCounties, window.appUtils.formatNumber(data.counties_count));
        window.appUtils.setText(elements.overviewBrands, window.appUtils.formatNumber(data.brands_count));
        window.appUtils.setText(elements.overviewFuelTypes, window.appUtils.formatNumber(data.fuel_types_count));
        window.appUtils.setText(elements.overviewCategories, window.appUtils.formatNumber(data.national_categories_count));
    }

    function renderYearlyTotals(yearlyTotals) {
        if (!elements.yearlyTotals) {
            return;
        }

        window.appCharts.createYearlyTotalsChart(elements.yearlyTotals, yearlyTotals);

        if (elements.yearlyTotalsCaption) {
            if (Array.isArray(yearlyTotals) && yearlyTotals.length > 0) {
                elements.yearlyTotalsCaption.textContent =
                    'Evoluția anuală a volumului total de vehicule pentru selecția curentă.';
            } else {
                elements.yearlyTotalsCaption.textContent =
                    'Nu există suficiente date pentru a afișa evoluția anuală.';
            }
        }
    }

    function renderTopBrands(topBrands) {
        if (!elements.topBrands) {
            return;
        }

        window.appCharts.createTopBrandsChart(elements.topBrands, topBrands);

        if (elements.topBrandsCaption) {
            if (Array.isArray(topBrands) && topBrands.length > 0) {
                elements.topBrandsCaption.textContent =
                    'Ierarhia mărcilor cele mai reprezentate în selecția curentă.';
            } else {
                elements.topBrandsCaption.textContent =
                    'Nu există date pentru topul mărcilor.';
            }
        }
    }

    function renderFuelDistribution(fuelDistribution) {
        if (!elements.fuelDistribution) {
            return;
        }

        window.appCharts.createFuelDistributionChart(elements.fuelDistribution, fuelDistribution);

        if (elements.fuelDistributionCaption) {
            if (Array.isArray(fuelDistribution) && fuelDistribution.length > 0) {
                elements.fuelDistributionCaption.textContent =
                    'Structura selecției active în funcție de tipul de combustibil.';
            } else {
                elements.fuelDistributionCaption.textContent =
                    'Nu există date pentru distribuția pe combustibil.';
            }
        }
    }

    function renderCategoryDistribution(categoryDistribution) {
        if (!elements.categoryDistribution) {
            return;
        }

        window.appCharts.createCategoryDistributionChart(elements.categoryDistribution, categoryDistribution);

        if (elements.categoryDistributionCaption) {
            if (Array.isArray(categoryDistribution) && categoryDistribution.length > 0) {
                elements.categoryDistributionCaption.textContent =
                    'Distribuția selecției active pe categorii naționale.';
            } else {
                elements.categoryDistributionCaption.textContent =
                    'Nu există date pentru distribuția pe categorii.';
            }
        }
    }

    function renderCountyRanking(rows) {
        if (!elements.countyRanking) {
            return;
        }

        const tbody = elements.countyRanking.querySelector('tbody');

        if (!tbody) {
            return;
        }

        window.appUtils.clearElement(tbody);

        if (!Array.isArray(rows) || rows.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 4;
            cell.textContent = 'Nu există date pentru clasamentul județelor.';

            row.appendChild(cell);
            tbody.appendChild(row);
            return;
        }

        rows.forEach((item, index) => {
            const row = document.createElement('tr');

            const rankCell = document.createElement('td');
            rankCell.textContent = String(index + 1);

            const codeCell = document.createElement('td');
            codeCell.textContent = window.appUtils.safeText(item.code);

            const nameCell = document.createElement('td');
            nameCell.textContent = window.appUtils.safeText(item.name);

            const valueCell = document.createElement('td');
            valueCell.textContent = window.appUtils.formatNumber(item.total_vehicles);
            valueCell.style.textAlign = 'right';

            row.appendChild(rankCell);
            row.appendChild(codeCell);
            row.appendChild(nameCell);
            row.appendChild(valueCell);

            tbody.appendChild(row);
        });
    }

    function renderMapPreview(countyRanking) {
        if (!elements.mapPreview) {
            return;
        }

        if (!Array.isArray(countyRanking) || countyRanking.length === 0) {
            window.appUtils.renderEmptyState(
                elements.mapPreview,
                'Nu există date pentru preview-ul teritorial.'
            );
            setMapSummary('Preview-ul teritorial nu poate fi afișat pentru selecția curentă.');
            return;
        }

        const topCounties = countyRanking.slice(0, 5);
        const maxValue = Math.max(...topCounties.map((item) => Number(item.total_vehicles || 0)));

        window.appUtils.clearElement(elements.mapPreview);

        const wrapper = document.createElement('div');
        wrapper.className = 'bar-chart';

        topCounties.forEach((item) => {
            const row = document.createElement('div');
            row.className = 'bar-row';

            const label = document.createElement('div');
            label.className = 'bar-label';
            label.textContent =
                `${window.appUtils.safeText(item.code, '-')} · ${window.appUtils.safeText(item.name, 'Nedefinit')}`;

            const bar = document.createElement('div');
            bar.className = 'bar-fill-wrapper';

            const fill = document.createElement('div');
            fill.className = 'bar-fill';

            if (maxValue > 0) {
                fill.style.width = `${Math.round((Number(item.total_vehicles || 0) / maxValue) * 100)}%`;
            } else {
                fill.style.width = '0%';
            }

            bar.appendChild(fill);

            const value = document.createElement('div');
            value.className = 'bar-value';
            value.textContent = window.appUtils.formatNumber(item.total_vehicles);

            row.appendChild(label);
            row.appendChild(bar);
            row.appendChild(value);

            wrapper.appendChild(row);
        });

        elements.mapPreview.appendChild(wrapper);

        const first = topCounties[0];

        setMapSummary(
            `Preview-ul teritorial evidențiază județele dominante din selecția curentă. În acest context, ${window.appUtils.safeText(first.name, 'județul principal')} ocupă prima poziție.`
        );
    }

    function renderAll(data) {
        renderOverview(data.overview);
        renderYearlyTotals(data.yearlyTotals);
        renderTopBrands(data.topBrands);
        renderFuelDistribution(data.fuelDistribution);
        renderCategoryDistribution(data.categoryDistribution);
        renderCountyRanking(data.countyRanking);
        renderMapPreview(data.countyRanking);

        const activeFilters = window.appFilters.getSelectedFilterSummary(state.filters);

        setContextSummary(
            `Dashboard-ul reflectă contextul curent: ${activeFilters}. Indicatorii sintetici, distribuțiile și clasamentul teritorial sunt sincronizate cu această selecție.`
        );
    }

    async function fetchDashboardData() {
        const filters = { ...state.filters };

        const yearlyTotalsQuery = {};

        if (filters.county_code) {
            yearlyTotalsQuery.county_code = filters.county_code;
        }

        if (filters.national_category) {
            yearlyTotalsQuery.national_category = filters.national_category;
        }

        if (filters.community_category) {
            yearlyTotalsQuery.community_category = filters.community_category;
        }

        if (filters.fuel_type) {
            yearlyTotalsQuery.fuel_type = filters.fuel_type;
        }

        if (filters.brand) {
            yearlyTotalsQuery.brand = filters.brand;
        }

        const [
            overviewPayload,
            yearlyTotalsPayload,
            topBrandsPayload,
            fuelDistributionPayload,
            categoryDistributionPayload,
            countyRankingPayload,
        ] = await Promise.all([
            window.appApi.getJson('statistics.php', getStatisticsQuery(filters, { view: 'overview' })),
            window.appApi.getJson('statistics.php', { view: 'yearly-totals', ...yearlyTotalsQuery }),
            window.appApi.getJson('statistics.php', getStatisticsQuery(filters, { view: 'top-brands', limit: 10 })),
            window.appApi.getJson('statistics.php', getStatisticsQuery(filters, { view: 'fuel-distribution' })),
            window.appApi.getJson('statistics.php', getStatisticsQuery(filters, { view: 'category-distribution' })),
            window.appApi.getJson('statistics.php', getStatisticsQuery(filters, { view: 'county-ranking' })),
        ]);

        return {
            overview: extractResult(overviewPayload),
            yearlyTotals: extractResult(yearlyTotalsPayload),
            topBrands: extractResult(topBrandsPayload),
            fuelDistribution: extractResult(fuelDistributionPayload),
            categoryDistribution: extractResult(categoryDistributionPayload),
            countyRanking: extractResult(countyRankingPayload),
        };
    }

    function getElementId(element) {
        if (element && element.id) {
            return element.id;
        }

        return '';
    }

    function renderLoading() {
        renderOverview({});

        window.appCharts.destroyChart(getElementId(elements.yearlyTotals));
        window.appCharts.destroyChart(getElementId(elements.topBrands));
        window.appCharts.destroyChart(getElementId(elements.fuelDistribution));
        window.appCharts.destroyChart(getElementId(elements.categoryDistribution));

        window.appUtils.renderLoadingState(elements.yearlyTotals, 'Se încarcă evoluția pe ani...');
        window.appUtils.renderLoadingState(elements.topBrands, 'Se încarcă topul mărcilor...');
        window.appUtils.renderLoadingState(elements.fuelDistribution, 'Se încarcă distribuția pe combustibil...');
        window.appUtils.renderLoadingState(elements.categoryDistribution, 'Se încarcă distribuția pe categorii...');
        window.appUtils.renderLoadingState(elements.mapPreview, 'Se pregătește preview-ul teritorial...');

        if (elements.yearlyTotalsCaption) {
            elements.yearlyTotalsCaption.textContent = 'Se încarcă vizualizarea anuală.';
        }

        if (elements.topBrandsCaption) {
            elements.topBrandsCaption.textContent = 'Se încarcă distribuția mărcilor.';
        }

        if (elements.fuelDistributionCaption) {
            elements.fuelDistributionCaption.textContent = 'Se încarcă structura pe combustibil.';
        }

        if (elements.categoryDistributionCaption) {
            elements.categoryDistributionCaption.textContent = 'Se încarcă distribuția pe categorii.';
        }

        renderCountyRanking([]);
        setMapSummary('Se pregătește rezumatul teritorial.');
        setContextSummary('Se încarcă datele dashboard-ului pentru selecția curentă.');
    }

    function renderError(error) {
        let message = 'A apărut o eroare la încărcarea dashboard-ului.';

        if (error instanceof Error) {
            message = error.message;
        }

        window.appCharts.destroyChart(getElementId(elements.yearlyTotals));
        window.appCharts.destroyChart(getElementId(elements.topBrands));
        window.appCharts.destroyChart(getElementId(elements.fuelDistribution));
        window.appCharts.destroyChart(getElementId(elements.categoryDistribution));

        window.appUtils.renderErrorState(elements.yearlyTotals, message);
        window.appUtils.renderErrorState(elements.topBrands, message);
        window.appUtils.renderErrorState(elements.fuelDistribution, message);
        window.appUtils.renderErrorState(elements.categoryDistribution, message);
        window.appUtils.renderErrorState(elements.mapPreview, message);

        renderCountyRanking([]);

        if (elements.yearlyTotalsCaption) {
            elements.yearlyTotalsCaption.textContent = 'Nu s-a putut încărca evoluția pe ani.';
        }

        if (elements.topBrandsCaption) {
            elements.topBrandsCaption.textContent = 'Nu s-a putut încărca topul mărcilor.';
        }

        if (elements.fuelDistributionCaption) {
            elements.fuelDistributionCaption.textContent = 'Nu s-a putut încărca distribuția pe combustibil.';
        }

        if (elements.categoryDistributionCaption) {
            elements.categoryDistributionCaption.textContent = 'Nu s-a putut încărca distribuția pe categorii.';
        }

        setMapSummary('Preview-ul teritorial nu a putut fi încărcat.');
        setContextSummary(message);
    }

    async function refreshDashboard(event) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        state.filters = getCurrentFiltersFromForm();
        setSelectionSummary();
        updateUrlFromFilters();
        updateExportLinks();

        state.loading = true;
        setLoadingState('Se încarcă datele dashboard-ului...');
        renderLoading();

        try {
            const data = await fetchDashboardData();

            renderAll(data);
            setLoadingState('Dashboard actualizat.');
        } catch (error) {
            console.error('Eroare la încărcarea dashboard-ului:', error);
            renderError(error);
            setLoadingState('A apărut o eroare.');
        } finally {
            state.loading = false;
        }
    }

    function resetFilters() {
        if (!elements.form) {
            return;
        }

        window.appFilters.resetFormFilters(elements.form);

        if (elements.year) {
            elements.year.value = String(defaultYear);
        }

        state.filters = getCurrentFiltersFromForm();
        setSelectionSummary();
        updateUrlFromFilters();
        updateExportLinks();
        refreshDashboard();
    }

    async function initializeFilters() {
        if (!elements.form) {
            return;
        }

        const queryValues = window.appUtils.getQueryParams();

        const selectedValues = {
            year: queryValues.year || String(defaultYear),
            county_code: queryValues.county_code || '',
            national_category: queryValues.national_category || '',
            community_category: queryValues.community_category || '',
            fuel_type: queryValues.fuel_type || '',
            brand: queryValues.brand || '',
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

        state.filters = getCurrentFiltersFromForm();
        setSelectionSummary();
        updateExportLinks();
    }

    async function init() {
        ensureDependencies();

        if (!elements.form) {
            return;
        }

        try {
            await initializeFilters();
        } catch (error) {
            console.error('Nu s-au putut inițializa filtrele dashboard-ului:', error);
            setLoadingState('A apărut o eroare la încărcarea filtrelor.');
            setContextSummary('Filtrele dashboard-ului nu au putut fi încărcate.');
            return;
        }

        elements.form.addEventListener('submit', refreshDashboard);

        if (elements.reset) {
            elements.reset.addEventListener('click', resetFilters);
        }

        const filterFields = elements.form.querySelectorAll('select, input, textarea');

        filterFields.forEach((field) => {
            field.addEventListener('change', () => {
                state.filters = getCurrentFiltersFromForm();
                updateExportLinks();
            });

            field.addEventListener('input', () => {
                state.filters = getCurrentFiltersFromForm();
                updateExportLinks();
            });
        });

        bindWebpExportButtons();
        await refreshDashboard();
    }

    window.addEventListener('DOMContentLoaded', init);
})();