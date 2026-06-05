const apiBase = window.APP_API_BASE_URL || 'api';
const defaultYear = Number(window.APP_DEFAULT_YEAR || new Date().getFullYear());

const elements = {
    form: document.getElementById('dashboard-filters-form'),
    year: document.getElementById('filter-year'),
    county: document.getElementById('filter-county'),
    nationalCategory: document.getElementById('filter-national-category'),
    communityCategory: document.getElementById('filter-community-category'),
    fuelType: document.getElementById('filter-fuel-type'),
    brand: document.getElementById('filter-brand'),
    reset: document.getElementById('reset-dashboard-filters'),
    overviewTotal: document.getElementById('overview-total-vehicles'),
    overviewCounties: document.getElementById('overview-counties-count'),
    overviewBrands: document.getElementById('overview-brands-count'),
    overviewFuelTypes: document.getElementById('overview-fuel-types-count'),
    overviewCategories: document.getElementById('overview-categories-count'),
    yearlyTotals: document.getElementById('chart-yearly-totals'),
    topBrands: document.getElementById('chart-top-brands'),
    fuelDistribution: document.getElementById('chart-fuel-distribution'),
    categoryDistribution: document.getElementById('chart-category-distribution'),
    countyRanking: document.getElementById('county-ranking-table'),
    selectionSummary: document.getElementById('dashboard-selection-summary'),
};

function buildApiUrl(path) {
    return `${apiBase}/${path}`;
}

function createOption(value, label) {
    const option = document.createElement('option');
    option.value = value;
    option.textContent = label;
    return option;
}

function formatNumber(value) {
    if (window.appUtils && typeof window.appUtils.formatNumber === 'function') {
        return window.appUtils.formatNumber(value);
    }

    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '-';
    }

    return Number(value).toLocaleString('ro-RO');
}

async function fetchJson(path) {
    return window.appApi.getJson(buildApiUrl(path));
}

function resetSelect(element, placeholder) {
    if (!element) return;
    element.innerHTML = '';
    element.appendChild(createOption('', placeholder));
}

function populateSelect(element, items, isPlainArray = false) {
    if (!element || !Array.isArray(items)) return;
    items.forEach((item) => {
        const value = isPlainArray ? item : item.name;
        const label = isPlainArray ? item : item.name;
        element.appendChild(createOption(value, label));
    });
}

async function loadFilters() {
    try {
        const filters = await fetchJson('filters.php');

        resetSelect(elements.year, 'Toți anii disponibili');
        populateSelect(elements.year, filters.years || [], true);

        resetSelect(elements.county, 'Toate județele');
        populateSelect(elements.county, filters.counties || []);

        resetSelect(elements.nationalCategory, 'Toate categoriile');
        populateSelect(elements.nationalCategory, filters.national_categories || []);

        resetSelect(elements.communityCategory, 'Toate categoriile comunitare');
        populateSelect(elements.communityCategory, filters.community_categories || []);

        resetSelect(elements.fuelType, 'Toate tipurile de combustibil');
        populateSelect(elements.fuelType, filters.fuel_types || []);

        resetSelect(elements.brand, 'Toate mărcile');
        populateSelect(elements.brand, filters.brands || []);

        if (elements.year && elements.year.options.length > 1) {
            const preferred = String(defaultYear);
            if ([...elements.year.options].some((option) => option.value === preferred)) {
                elements.year.value = preferred;
            } else {
                elements.year.selectedIndex = 1;
            }
        }
    } catch (error) {
        console.error('Nu am putut încărca filtrele dashboard:', error);
        if (elements.selectionSummary) {
            elements.selectionSummary.textContent = 'Nu am putut încărca filtrele pentru dashboard.';
        }
    }
}

function updateSelectionSummary() {
    const selected = [];
    if (elements.year && elements.year.value) selected.push(`An: ${elements.year.value}`);
    if (elements.fuelType && elements.fuelType.value) selected.push(`Combustibil: ${elements.fuelType.value}`);
    if (elements.nationalCategory && elements.nationalCategory.value) selected.push(`Categorie națională: ${elements.nationalCategory.value}`);
    if (elements.county && elements.county.value) selected.push(`Județ: ${elements.county.value}`);
    if (elements.brand && elements.brand.value) selected.push(`Marcă: ${elements.brand.value}`);
    if (elements.communityCategory && elements.communityCategory.value) selected.push(`Categorie comunitară: ${elements.communityCategory.value}`);

    elements.selectionSummary.textContent = selected.length > 0
        ? `Filtre active: ${selected.join(' · ')}`
        : 'Filtre active: toate datele disponibile';
}

function renderOverview(result) {
    if (!result) return;
    elements.overviewTotal.textContent = formatNumber(result.total_vehicles);
    elements.overviewCounties.textContent = formatNumber(result.counties_count);
    elements.overviewBrands.textContent = formatNumber(result.brands_count);
    elements.overviewFuelTypes.textContent = formatNumber(result.fuel_types_count);
    elements.overviewCategories.textContent = formatNumber(result.national_categories_count);
}

function createBarChart(data, container, labelKey) {
    if (!container) return;
    container.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = '<div class="chart-empty">Nu există date pentru această selecție.</div>';
        return;
    }

    const maxValue = Math.max(...data.map((item) => Number(item.total_vehicles || 0)));
    const chart = document.createElement('div');
    chart.className = 'bar-chart';

    data.forEach((item) => {
        const row = document.createElement('div');
        row.className = 'bar-row';

        const label = document.createElement('div');
        label.className = 'bar-label';
        label.textContent = item[labelKey] || 'Nedefinit';

        const value = Number(item.total_vehicles || 0);
        const valueText = document.createElement('div');
        valueText.className = 'bar-value';
        valueText.textContent = `${formatNumber(value)} vehicule`;

        const bar = document.createElement('div');
        bar.className = 'bar-fill-wrapper';
        const fill = document.createElement('div');
        fill.className = 'bar-fill';
        fill.style.width = maxValue > 0 ? `${Math.round((value / maxValue) * 100)}%` : '0%';
        bar.appendChild(fill);

        row.appendChild(label);
        row.appendChild(bar);
        row.appendChild(valueText);
        chart.appendChild(row);
    });

    container.appendChild(chart);
}

function renderTable(container, rows, columns) {
    if (!container) return;
    const tbody = container.querySelector('tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (!Array.isArray(rows) || rows.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = columns.length;
        cell.textContent = 'Nu există date pentru această selecție.';
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }

    rows.forEach((item, index) => {
        const row = document.createElement('tr');
        columns.forEach((column) => {
            const cell = document.createElement('td');
            if (column.key === 'rank') {
                cell.textContent = String(index + 1);
            } else if (column.key === 'total_vehicles') {
                cell.textContent = formatNumber(item.total_vehicles);
                cell.style.textAlign = 'right';
            } else {
                cell.textContent = item[column.key] ?? '';
            }
            row.appendChild(cell);
        });
        tbody.appendChild(row);
    });
}

function renderTopBrands(result) {
    if (!elements.topBrands) return;

    const table = document.createElement('table');
    table.className = 'summary-table';
    table.innerHTML = `
        <thead>
            <tr>
                <th>#</th>
                <th>Marcă</th>
                <th style="text-align:right">Total vehicule</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    elements.topBrands.innerHTML = '';
    elements.topBrands.appendChild(table);
    renderTable(table, result, [
        { key: 'rank' },
        { key: 'name' },
        { key: 'total_vehicles' },
    ]);
}

function renderCountyRanking(result) {
    if (!elements.countyRanking) return;
    renderTable(elements.countyRanking, result, [
        { key: 'rank' },
        { key: 'code' },
        { key: 'name' },
        { key: 'total_vehicles' },
    ]);
}

async function refreshDashboard(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    updateSelectionSummary();
    const selectedYear = (elements.year && elements.year.value) ? Number(elements.year.value) : defaultYear;
    const queryYear = Number.isNaN(selectedYear) ? defaultYear : selectedYear;

    try {
        const [overviewData, topBrandsData, fuelData, categoryData, countyData, yearlyTotalsData] = await Promise.all([
            fetchJson(`statistics.php?view=overview&year=${encodeURIComponent(queryYear)}`),
            fetchJson(`statistics.php?view=top-brands&year=${encodeURIComponent(queryYear)}&limit=10`),
            fetchJson(`statistics.php?view=fuel-distribution&year=${encodeURIComponent(queryYear)}`),
            fetchJson(`statistics.php?view=category-distribution&year=${encodeURIComponent(queryYear)}`),
            fetchJson(`statistics.php?view=county-ranking&year=${encodeURIComponent(queryYear)}`),
            fetchJson('statistics.php?view=yearly-totals'),
        ]);

        renderOverview(overviewData.result);
        renderTopBrands(topBrandsData.result);
        createBarChart(fuelData.result, elements.fuelDistribution, 'name');
        createBarChart(categoryData.result, elements.categoryDistribution, 'name');
        renderCountyRanking(countyData.result);

        if (elements.yearlyTotals) {
            elements.yearlyTotals.innerHTML = '';
            if (Array.isArray(yearlyTotalsData.result) && yearlyTotalsData.result.length > 0) {
                const rows = yearlyTotalsData.result.map((item) => `
                    <div class="year-row">
                        <span>${item.year}</span>
                        <strong>${formatNumber(item.total_vehicles)}</strong>
                    </div>
                `).join('');
                elements.yearlyTotals.innerHTML = `<div class="yearly-totals-list">${rows}</div>`;
            } else {
                elements.yearlyTotals.innerHTML = '<div class="chart-empty">Nu există date pentru evoluția pe ani.</div>';
            }
        }
    } catch (error) {
        console.error('Eroare la încărcarea dashboard-ului:', error);
        if (elements.selectionSummary) {
            elements.selectionSummary.textContent = 'A apărut o eroare la încărcarea datelor.';
        }
    }
}

function resetFilters() {
    if (!elements.form) return;
    elements.form.reset();
    if (elements.year) elements.year.value = String(defaultYear);
    refreshDashboard();
}

window.addEventListener('DOMContentLoaded', async () => {
    await loadFilters();
    if (elements.form) {
        elements.form.addEventListener('submit', refreshDashboard);
    }
    if (elements.reset) {
        elements.reset.addEventListener('click', resetFilters);
    }
    await refreshDashboard();
});
