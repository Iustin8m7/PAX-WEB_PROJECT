(function () {
    'use strict';

    const mapCenter = [45.9432, 24.9668];
    const mapZoom = 7;
    const geoJsonUrl = window.APP_GEOJSON_URL || 'assets/data/romania-counties.geojson';
    const exportBaseUrl = window.APP_EXPORT_BASE_URL || 'api/export.php';

    const elements = {
        form: document.getElementById('map-filters-form'),
        year: document.getElementById('map-filter-year'),
        fuelType: document.getElementById('map-filter-fuel-type'),
        nationalCategory: document.getElementById('map-filter-national-category'),
        reset: document.getElementById('reset-map-filters'),

        exportButton: document.getElementById('map-export-csv'),
        exportInlineButton: document.getElementById('map-export-inline'),

        selectionSummary: document.getElementById('map-selection-summary'),
        loadingState: document.getElementById('map-loading-state'),
        contextSummary: document.getElementById('map-context-summary'),

        selectedCountyName: document.getElementById('selected-county-name'),
        selectedCountyCode: document.getElementById('selected-county-code'),
        selectedCountyTotal: document.getElementById('selected-county-total'),
        selectedCountyYear: document.getElementById('selected-county-year'),
        selectedCountyBrand: document.getElementById('selected-county-brand'),
    };

    const state = {
        filters: {
            year: String(window.APP_DEFAULT_YEAR || new Date().getFullYear()),
            fuel_type: '',
            national_category: '',
        },
        filtersData: null,
        counties: [],
        geoJson: null,
        map: null,
        tileLayer: null,
        geoJsonLayer: null,
        selectedCountyCode: null,
        isLoading: false,
    };

    function ensureDependencies() {
        if (typeof window.L === 'undefined') {
            throw new Error('Leaflet nu este disponibil.');
        }

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
        const summary = window.appFilters.getSelectedFilterSummary({
            year: state.filters.year,
            fuel_type: state.filters.fuel_type,
            national_category: state.filters.national_category,
        });

        if (elements.selectionSummary) {
            elements.selectionSummary.textContent = summary;
        }
    }

    function setContextSummary(message) {
        if (elements.contextSummary) {
            elements.contextSummary.textContent = message;
        }
    }

    function updateUrlFromFilters() {
        window.appUtils.updateUrlQuery({
            year: state.filters.year,
            fuel_type: state.filters.fuel_type,
            national_category: state.filters.national_category,
        });
    }

    function buildExportHref() {
        const queryString = window.appUtils.buildQueryString({
            resource: 'map',
            format: 'csv',
            year: state.filters.year,
            fuel_type: state.filters.fuel_type,
            national_category: state.filters.national_category,
        });

        return `${exportBaseUrl}${queryString}`;
    }

    function updateExportLinks() {
        const href = buildExportHref();

        if (elements.exportButton) {
            elements.exportButton.href = href;
        }

        if (elements.exportInlineButton) {
            elements.exportInlineButton.href = href;
        }
    }

    function resetSelectedCountyPanel(message = 'Niciun județ selectat') {
        window.appUtils.setText(elements.selectedCountyName, message, 'Niciun județ selectat');
        window.appUtils.setText(elements.selectedCountyCode, 'Cod județ: -');
        window.appUtils.setText(elements.selectedCountyTotal, '-');
        window.appUtils.setText(elements.selectedCountyYear, state.filters.year || '-');
        window.appUtils.setText(elements.selectedCountyBrand, '-');
    }

    function getCurrentFiltersFromForm() {
        if (!elements.form) {
            return { ...state.filters };
        }

        const values = window.appUtils.normalizeFilters(window.appUtils.getFormValues(elements.form));

        return {
            year: values.year || String(window.APP_DEFAULT_YEAR || new Date().getFullYear()),
            fuel_type: values.fuel_type || '',
            national_category: values.national_category || '',
        };
    }

    function getCountyBrand(county) {
        return county.top_brand || county.brand_name || county.brand || 'Necunoscut';
    }

    function getCountyCodeFromFeature(feature) {
        const props = feature?.properties || {};
        return props.mnemonic || props.code || props.countyCodeAlpha || props.abbr || null;
    }

    function getCountyNameFromFeature(feature) {
        const props = feature?.properties || {};
        return props.name || props.countyName || 'Județ';
    }

    function getCountyDataMap() {
        const map = new Map();

        state.counties.forEach((county) => {
            if (county && county.county_code) {
                map.set(String(county.county_code).toUpperCase(), county);
            }
        });

        return map;
    }

    function getMaxVehicles() {
        if (!Array.isArray(state.counties) || state.counties.length === 0) {
            return 0;
        }

        return Math.max(...state.counties.map((county) => Number(county.total_vehicles || 0)));
    }

    function getFillColorByValue(value, maxValue) {
        const safeValue = Number(value || 0);

        if (safeValue <= 0 || maxValue <= 0) {
            return 'rgba(148, 163, 184, 0.18)';
        }

        const ratio = safeValue / maxValue;

        if (ratio >= 0.8) {
            return 'rgba(96, 165, 250, 0.90)';
        }
        if (ratio >= 0.6) {
            return 'rgba(96, 165, 250, 0.72)';
        }
        if (ratio >= 0.4) {
            return 'rgba(96, 165, 250, 0.54)';
        }
        if (ratio >= 0.2) {
            return 'rgba(96, 165, 250, 0.34)';
        }

        return 'rgba(96, 165, 250, 0.18)';
    }

    function getFeatureStyle(feature) {
        const countyCode = getCountyCodeFromFeature(feature);
        const countyDataMap = getCountyDataMap();
        const county = countyCode ? countyDataMap.get(String(countyCode).toUpperCase()) : null;
        const maxValue = getMaxVehicles();
        const isSelected = state.selectedCountyCode && countyCode && String(countyCode).toUpperCase() === state.selectedCountyCode;

        return {
            color: isSelected ? '#f8fafc' : 'rgba(255,255,255,0.18)',
            weight: isSelected ? 2.5 : 1.2,
            fillColor: getFillColorByValue(county ? county.total_vehicles : 0, maxValue),
            fillOpacity: county ? 0.92 : 0.45,
        };
    }

    function updateSelectedCounty(county) {
        if (!county) {
            resetSelectedCountyPanel();
            state.selectedCountyCode = null;
            return;
        }

        state.selectedCountyCode = String(county.county_code || '').toUpperCase();

        window.appUtils.setText(elements.selectedCountyName, county.county_name, 'Județ necunoscut');
        window.appUtils.setText(elements.selectedCountyCode, `Cod județ: ${window.appUtils.safeText(county.county_code, '-')}`);
        window.appUtils.setText(elements.selectedCountyTotal, window.appUtils.formatNumber(county.total_vehicles));
        window.appUtils.setText(elements.selectedCountyYear, state.filters.year || '-');
        window.appUtils.setText(elements.selectedCountyBrand, getCountyBrand(county), '-');

        setContextSummary(
            `Județul selectat este ${window.appUtils.safeText(county.county_name, 'necunoscut')}. În contextul activ, acesta înregistrează ${window.appUtils.formatNumber(county.total_vehicles)} vehicule, iar marca predominantă este ${getCountyBrand(county)}.`
        );

        refreshGeoJsonStyles();
    }

    function getPopupHtml(feature, county) {
        const countyName = county?.county_name || getCountyNameFromFeature(feature);
        const countyCode = county?.county_code || getCountyCodeFromFeature(feature) || '-';
        const totalVehicles = county ? window.appUtils.formatNumber(county.total_vehicles) : '-';
        const brand = county ? getCountyBrand(county) : '-';

        return `
            <div>
                <strong>${window.appUtils.safeText(countyName, 'Județ')}</strong><br>
                Cod: <strong>${window.appUtils.safeText(countyCode, '-')}</strong><br>
                Total vehicule: <strong>${totalVehicles}</strong><br>
                Marcă predominantă: <strong>${window.appUtils.safeText(brand, '-')}</strong>
            </div>
        `;
    }

    function handleFeatureHover(event) {
        const layer = event.target;
        layer.setStyle({
            weight: 2.5,
            color: '#f8fafc',
        });

        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }
    }

    function handleFeatureMouseOut() {
        refreshGeoJsonStyles();
    }

    function handleFeatureClick(feature, county, layer) {
        if (county) {
            updateSelectedCounty(county);
        } else {
            const fallbackCounty = {
                county_code: getCountyCodeFromFeature(feature),
                county_name: getCountyNameFromFeature(feature),
                total_vehicles: 0,
                top_brand: 'Necunoscut',
            };
            updateSelectedCounty(fallbackCounty);
        }

        if (layer && typeof layer.openPopup === 'function') {
            layer.openPopup();
        }
    }

    function onEachCountyFeature(feature, layer) {
        const countyCode = getCountyCodeFromFeature(feature);
        const countyDataMap = getCountyDataMap();
        const county = countyCode ? countyDataMap.get(String(countyCode).toUpperCase()) : null;

        layer.bindPopup(getPopupHtml(feature, county));

        layer.on({
            mouseover: handleFeatureHover,
            mouseout: handleFeatureMouseOut,
            click() {
                handleFeatureClick(feature, county, layer);
            },
        });
    }

    function refreshGeoJsonStyles() {
        if (!state.geoJsonLayer) {
            return;
        }

        state.geoJsonLayer.setStyle((feature) => getFeatureStyle(feature));
    }

    function renderGeoJsonLayer() {
        if (!state.map || !state.geoJson) {
            return;
        }

        if (state.geoJsonLayer) {
            state.map.removeLayer(state.geoJsonLayer);
            state.geoJsonLayer = null;
        }

        state.geoJsonLayer = L.geoJSON(state.geoJson, {
            style: getFeatureStyle,
            onEachFeature: onEachCountyFeature,
        });

        state.geoJsonLayer.addTo(state.map);
    }

    function fitMapToGeoJson() {
        if (!state.map || !state.geoJsonLayer) {
            return;
        }

        try {
            const bounds = state.geoJsonLayer.getBounds();
            if (bounds && bounds.isValid()) {
                state.map.fitBounds(bounds, { padding: [20, 20] });
            }
        } catch (error) {
            console.warn('Nu s-a putut ajusta automat harta:', error);
        }
    }

    async function loadGeoJson() {
        const response = await fetch(geoJsonUrl, { credentials: 'same-origin' });

        if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul GeoJSON.');
        }

        const geoJson = await response.json();

        if (!geoJson || geoJson.type !== 'FeatureCollection' || !Array.isArray(geoJson.features)) {
            throw new Error('Fișierul GeoJSON este invalid.');
        }

        state.geoJson = geoJson;
    }

    async function loadFilters() {
        state.filtersData = await window.appFilters.loadFilters();

        if (!elements.form) {
            return;
        }

        const queryValues = window.appUtils.getQueryParams();
        const selectedValues = {
            year: queryValues.year || state.filters.year,
            fuel_type: queryValues.fuel_type || '',
            national_category: queryValues.national_category || '',
        };

        window.appFilters.applyFiltersToForm(elements.form, state.filtersData, {
            selectedValues,
            yearDefaultLabel: 'Selectare an',
            fuelTypeDefaultLabel: 'Toate tipurile de combustibil',
            nationalCategoryDefaultLabel: 'Toate categoriile',
        });

        state.filters = getCurrentFiltersFromForm();
        setSelectionSummary();
        updateExportLinks();
    }

    async function fetchMapData() {
        return window.appApi.getJson('map.php', {
            year: state.filters.year,
            fuel_type: state.filters.fuel_type,
            national_category: state.filters.national_category,
        });
    }

    function extractCountyRows(payload) {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (payload && Array.isArray(payload.result)) {
            return payload.result;
        }

        if (payload && Array.isArray(payload.rows)) {
            return payload.rows;
        }

        return [];
    }

    function selectInitialCounty() {
        if (!Array.isArray(state.counties) || state.counties.length === 0) {
            resetSelectedCountyPanel('Niciun județ disponibil');
            return;
        }

        const firstCounty = state.counties[0];
        updateSelectedCounty(firstCounty);
    }

    function renderNoDataState() {
        state.counties = [];
        state.selectedCountyCode = null;
        refreshGeoJsonStyles();
        resetSelectedCountyPanel('Niciun județ disponibil');
        setContextSummary('Nu există date pentru selecția curentă.');
    }

    async function refreshMap(event) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        state.filters = getCurrentFiltersFromForm();
        state.isLoading = true;

        setSelectionSummary();
        setLoadingState('Se încarcă datele hărții...');
        resetSelectedCountyPanel('Se încarcă date...');
        updateUrlFromFilters();
        updateExportLinks();

        try {
            const payload = await fetchMapData();
            const rows = extractCountyRows(payload);

            state.counties = rows
                .map((item) => ({
                    county_code: window.appUtils.safeText(item.county_code, ''),
                    county_name: window.appUtils.safeText(item.county_name, 'Județ'),
                    total_vehicles: Number(item.total_vehicles || 0),
                    top_brand: window.appUtils.safeText(getCountyBrand(item), 'Necunoscut'),
                }))
                .filter((item) => item.county_code !== '');

            renderGeoJsonLayer();

            if (state.counties.length === 0) {
                renderNoDataState();
                setLoadingState('Nu există date pentru selecția activă.');
                return;
            }

            selectInitialCounty();
            fitMapToGeoJson();
            setLoadingState('Hartă actualizată.');
        } catch (error) {
            console.error('Eroare la încărcarea hărții:', error);
            state.counties = [];
            state.selectedCountyCode = null;
            refreshGeoJsonStyles();
            resetSelectedCountyPanel('Eroare la încărcare');
            setContextSummary(error instanceof Error ? error.message : 'A apărut o eroare la încărcarea hărții.');
            setLoadingState('A apărut o eroare.');
        } finally {
            state.isLoading = false;
        }
    }

    function resetFilters() {
        if (!elements.form) {
            return;
        }

        window.appFilters.resetFormFilters(elements.form);

        if (elements.year) {
            elements.year.value = String(window.APP_DEFAULT_YEAR || new Date().getFullYear());
        }

        state.filters = getCurrentFiltersFromForm();
        setSelectionSummary();
        updateUrlFromFilters();
        updateExportLinks();
        refreshMap();
    }

    function initMap() {
        state.map = L.map('map-canvas', {
            minZoom: 6,
            maxZoom: 12,
            zoomControl: true,
        }).setView(mapCenter, mapZoom);

        state.tileLayer = L.tileLayer(
            'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            {
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 19,
            }
        );

        state.tileLayer.addTo(state.map);
    }

    function bindEvents() {
        if (elements.form) {
            elements.form.addEventListener('submit', refreshMap);
        }

        if (elements.reset) {
            elements.reset.addEventListener('click', resetFilters);
        }

        const autoSubmitHandler = window.appUtils.debounce(() => {
            if (elements.form) {
                if (typeof elements.form.requestSubmit === 'function') {
                    elements.form.requestSubmit();
                } else {
                    refreshMap();
                }
            }
        }, 220);

        [elements.year, elements.fuelType, elements.nationalCategory].forEach((element) => {
            if (element) {
                element.addEventListener('change', autoSubmitHandler);
                element.addEventListener('input', () => {
                    state.filters = getCurrentFiltersFromForm();
                    updateExportLinks();
                });
            }
        });
    }

    async function init() {
        ensureDependencies();

        if (!elements.form) {
            return;
        }

        setLoadingState('Se inițializează componenta cartografică...');
        setSelectionSummary();
        resetSelectedCountyPanel();

        try {
            initMap();
            await Promise.all([loadGeoJson(), loadFilters()]);
            bindEvents();
            await refreshMap();
        } catch (error) {
            console.error('Nu s-a putut inițializa pagina de hartă:', error);
            setLoadingState('Inițializare eșuată.');
            setContextSummary(error instanceof Error ? error.message : 'Nu s-a putut inițializa componenta cartografică.');
            resetSelectedCountyPanel('Inițializare eșuată');
        }
    }

    window.addEventListener('DOMContentLoaded', init);
})();