const mapCenter = [45.9432, 24.9668];
const mapZoom = 7;

const countyCoordinates = {
    AB: [46.1866, 21.3123],
    AG: [45.1369, 24.7925],
    AR: [46.1860, 21.3123],
    BC: [46.5670, 26.9138],
    BH: [47.0746, 21.9189],
    BN: [47.1333, 24.4833],
    BT: [47.7466, 26.6691],
    BV: [45.6428, 25.5880],
    BR: [45.2697, 27.9576],
    B: [44.4268, 26.1025],
    BZ: [45.1504, 26.8060],
    CJ: [46.7712, 23.6236],
    CL: [44.2197, 27.3288],
    CS: [45.3872, 21.9034],
    CT: [44.1733, 28.6383],
    CV: [45.8623, 25.7990],
    DB: [44.9163, 25.4586],
    DJ: [44.1737, 23.6000],
    GJ: [45.0660, 23.2653],
    GL: [45.4359, 28.0074],
    GR: [43.9035, 25.9690],
    HD: [45.7489, 22.8886],
    HR: [46.3569, 25.7969],
    IF: [44.5716, 26.0863],
    IS: [47.1585, 27.6014],
    MH: [44.9035, 22.6667],
    MM: [47.6592, 23.5880],
    MS: [46.5421, 24.5573],
    NT: [46.9759, 26.3819],
    OT: [44.4422, 24.3690],
    PH: [45.9422, 25.9540],
    SB: [45.7983, 24.1256],
    SJ: [47.1667, 23.2667],
    SM: [47.7927, 22.8850],
    SV: [47.6510, 26.2550],
    TL: [45.1719, 28.7954],
    TM: [45.7489, 21.2087],
    TR: [43.6313, 25.3672],
    VL: [45.1000, 24.3667],
    VN: [45.6990, 27.1916],
    VS: [46.6403, 27.7300],
};

const brandColors = {
    DACIA: '#1D4ED8',
    VOLKSWAGEN: '#0EA5E9',
    MERCEDES: '#10B981',
    BMW: '#8B5CF6',
    FORD: '#F97316',
    RENAULT: '#F59E0B',
    OPEL: '#EF4444',
    AUDI: '#9333EA',
    TOYOTA: '#14B8A6',
    NISSAN: '#F43F5E',
    IVECO: '#0F766E',
};

const elements = {
    form: document.getElementById('map-filters-form'),
    year: document.getElementById('map-filter-year'),
    fuelType: document.getElementById('map-filter-fuel-type'),
    nationalCategory: document.getElementById('map-filter-national-category'),
    reset: document.getElementById('reset-map-filters'),
    selectedCountyName: document.getElementById('selected-county-name'),
    selectedCountyCode: document.getElementById('selected-county-code'),
    selectedCountyTotal: document.getElementById('selected-county-total'),
    selectedCountyYear: document.getElementById('selected-county-year'),
    selectedCountyBrand: document.getElementById('selected-county-brand'),
    selectionSummary: document.getElementById('map-selection-summary'),
};

const mapInstance = L.map('map-canvas', {
    minZoom: 6,
    maxZoom: 12,
    zoomControl: true,
}).setView(mapCenter, mapZoom);

const mapLoader = document.querySelector('.map-loader');
const defaultYear = window.APP_DEFAULT_YEAR || new Date().getFullYear();

const tileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    subdomains: 'abcd',
    maxZoom: 19,
}).addTo(mapInstance);

tileLayer.on('load', () => {
    if (mapLoader) {
        mapLoader.style.display = 'none';
    }
});

const markerLayer = L.layerGroup().addTo(mapInstance);

function getBrandColor(brand) {
    if (!brand) {
        return '#6B7280';
    }

    const normalized = brand.toString().trim().toUpperCase();

    if (brandColors[normalized]) {
        return brandColors[normalized];
    }

    let hash = 0;
    for (let i = 0; i < normalized.length; i += 1) {
        hash = normalized.charCodeAt(i) + ((hash << 5) - hash);
    }

    const hue = Math.abs(hash) % 360;
    return `hsl(${hue}, 70%, 45%)`;
}

function getMarkerRadius(total) {
    if (total <= 0) {
        return 8;
    }

    return Math.min(40, Math.max(10, Math.sqrt(total) * 0.06));
}

function getSummaryText(year, fuelType, nationalCategory) {
    const parts = [`An: ${year}`];

    if (fuelType) {
        parts.push(`Combustibil: ${fuelType}`);
    }
    if (nationalCategory) {
        parts.push(`Categorie: ${nationalCategory}`);
    }

    return parts.join(' · ');
}

function renderCountyMarkers(counties) {
    markerLayer.clearLayers();

    counties.forEach((county) => {
        const coords = countyCoordinates[county.county_code];
        if (!coords) {
            return;
        }

        const color = getBrandColor(county.top_brand);
        const radius = getMarkerRadius(county.total_vehicles);

        const marker = L.circleMarker(coords, {
            radius,
            color,
            fillColor: color,
            weight: 2,
            opacity: 0.9,
            fillOpacity: 0.45,
        });

        marker.addTo(markerLayer);

        const popupHtml = `
            <div style="font-family: Inter, system-ui, sans-serif; font-size: 0.95rem;">
                <strong>${county.county_name}</strong><br/>
                Total vehicule: <strong>${window.appUtils.formatNumber(county.total_vehicles)}</strong><br/>
                Marcă predominantă: <strong>${county.top_brand}</strong>
            </div>
        `;

        marker.bindPopup(popupHtml);
        marker.on('click', () => updateSelectedCounty(county));
    });
}

function updateSelectedCounty(county) {
    elements.selectedCountyName.textContent = county.county_name;
    elements.selectedCountyCode.textContent = `Cod județ: ${county.county_code}`;
    elements.selectedCountyTotal.textContent = window.appUtils.formatNumber(county.total_vehicles);
    elements.selectedCountyBrand.textContent = county.top_brand;
}

async function loadFilters() {
    try {
        const filters = await window.appApi.getJson('/api/filters.php');

        console.log('filters payload', filters);

        // Ensure structure even if API returns empty/missing keys
        if (!filters || typeof filters !== 'object') {
            throw new Error('Payload filters invalid');
        }

        if (!Array.isArray(filters.years) || filters.years.length === 0) {
            // Fallback: generate years from APP_MIN_YEAR..APP_MAX_YEAR
            const min = Number(window.APP_MIN_YEAR) || (new Date().getFullYear() - 4);
            const max = Number(window.APP_MAX_YEAR) || new Date().getFullYear();
            const generated = [];
            for (let y = min; y <= max; y += 1) generated.push(y);
            filters.years = generated;
            console.warn('Filters: years missing from API, generated fallback', filters.years);
        }

        elements.year.innerHTML = '<option value="">Selectare an</option>';
        filters.years.forEach((year) => {
            elements.year.appendChild(window.appUtils.createOption(year, year));
        });

        const selectedYear = String(defaultYear);
        if (filters.years.some((year) => String(year) === selectedYear)) {
            elements.year.value = selectedYear;
        }

        elements.fuelType.innerHTML = '<option value="">Toate tipurile de combustibil</option>';
        (Array.isArray(filters.fuel_types) ? filters.fuel_types : []).forEach((fuel) => {
            elements.fuelType.appendChild(window.appUtils.createOption(fuel.name, fuel.name));
        });

        elements.nationalCategory.innerHTML = '<option value="">Toate categoriile</option>';
        (Array.isArray(filters.national_categories) ? filters.national_categories : []).forEach((category) => {
            elements.nationalCategory.appendChild(window.appUtils.createOption(category.name, category.name));
        });
    } catch (error) {
        console.error(error);
        elements.selectionSummary.textContent = 'Nu am putut încărca filtrele. Încearcă să reîmprospătezi pagina.';
    }
}

async function refreshMap() {
    const year = elements.year.value || String(defaultYear);
    const fuelType = elements.fuelType.value || null;
    const nationalCategory = elements.nationalCategory.value || null;

    elements.selectedCountyName.textContent = 'Încarcare date...';
    elements.selectedCountyCode.textContent = 'Cod județ: -';
    elements.selectedCountyTotal.textContent = '-';
    elements.selectedCountyBrand.textContent = '-';
    elements.selectionSummary.textContent = getSummaryText(year, fuelType, nationalCategory);
    elements.selectedCountyYear.textContent = year;

    try {
        const data = await window.appApi.getJson(
            `api/brand-map-data.php?year=${encodeURIComponent(year)}${fuelType ? `&fuel_type=${encodeURIComponent(fuelType)}` : ''}${nationalCategory ? `&national_category=${encodeURIComponent(nationalCategory)}` : ''}`
        );

        if (!Array.isArray(data.result) || data.result.length === 0) {
            elements.selectionSummary.textContent = 'Nu există date pentru selecția actuală.';
            markerLayer.clearLayers();
            return;
        }

        renderCountyMarkers(data.result);
        elements.selectionSummary.textContent = getSummaryText(year, fuelType, nationalCategory);
        updateSelectedCounty(data.result[0]);
    } catch (error) {
        console.error(error);
        elements.selectionSummary.textContent = 'Eroare la încărcarea datelor de pe hartă.';
    }
}

elements.form.addEventListener('submit', (event) => {
    event.preventDefault();
    refreshMap();
});

elements.reset.addEventListener('click', () => {
    elements.form.reset();
    refreshMap();
});

window.addEventListener('DOMContentLoaded', async () => {
    await loadFilters();
    refreshMap();
});
