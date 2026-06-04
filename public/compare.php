<?php

declare(strict_types=1);

$config = require __DIR__ . '/../app/config/config.php';

if (isset($config['app_name'])) {
    $appName = $config['app_name'];
} else {
    $appName = 'Pax';
}

if (isset($config['app']['default_year'])) {
    $defaultYear = $config['app']['default_year'];
} else {
    $defaultYear = 2024;
}

if (isset($config['app']['min_year'])) {
    $minYear = $config['app']['min_year'];
} else {
    $minYear = 2020;
}

if (isset($config['app']['max_year'])) {
    $maxYear = $config['app']['max_year'];
} else {
    $maxYear = 2024;
}

?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Comparații</title>
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        /* Fix critic pentru textul invizibil din meniurile derulante */
        select option {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        /* Ajustare stil pentru listele de rezultate */
        .compare-summary ul {
            list-style-type: none;
            padding-left: 0 !important;
        }

        .compare-summary li {
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.03);
            margin-bottom: 4px;
            border-radius: 6px;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <header class="site-header">
            <div class="container header-inner">
                <a href="index.php" class="brand-mark">
                    <span class="brand-badge">P</span>
                    <span class="brand-text"><?php echo htmlspecialchars($appName); ?></span>
                </a>

                <nav class="main-nav" aria-label="Navigație principală">
                    <a href="index.php" class="nav-link">Acasă</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="map-view.php" class="nav-link">Hartă</a>
                    <a href="search-view.php" class="nav-link">Căutare</a>
                    <a href="compare.php" class="nav-link active">Comparații</a>
                    <a href="about.php" class="nav-link">Despre</a>
                </nav>
            </div>
        </header>

        <main class="section">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">Analiză comparativă</p>
                    <h2>Compară două mărci</h2>
                    <p class="section-lead">Alege două branduri și un an pentru a vedea ce producător a avut mai multă
                        prezență în parcul auto.</p>
                </div>

                <form id="compare-form" class="hero-panel-card" autocomplete="off">
                    <div class="hero-panel-grid"
                        style="grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:18px;">
                        <div class="form-field">
                            <label class="form-label">An</label>
                            <select id="compare-year" name="year" class="select-custom">
                                <option value="">Toți anii</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Brand A</label>
                            <select id="compare-brand-a" name="brand_a" class="select-custom">
                                <option value="">Alege marca A</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Brand B</label>
                            <select id="compare-brand-b" name="brand_b" class="select-custom">
                                <option value="">Alege marca B</option>
                            </select>
                        </div>

                        <div style="display:flex; align-items:end; gap:12px;">
                            <button type="submit" class="btn btn-primary">Compară</button>
                            <button type="button" id="compare-reset" class="btn btn-secondary">Resetare</button>
                        </div>
                    </div>
                </form>

                <section id="compare-results" style="margin-top:24px;">
                    <div class="hero-panel-card">
                        <div id="compare-summary" class="compare-summary">
                            <p>Selectează două mărci pentru a vedea comparația.</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Compare</span></p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script>
        (async function () {
            const form = document.getElementById('compare-form');
            const yearSelect = document.getElementById('compare-year');
            const brandASelect = document.getElementById('compare-brand-a');
            const brandBSelect = document.getElementById('compare-brand-b');
            const resetButton = document.getElementById('compare-reset');
            const compareSummary = document.getElementById('compare-summary');

            function aggregateData(records) {
                const totals = {
                    totalVehicles: 0,
                    counties: new Set(),
                    models: new Set(),
                    byCounty: {},
                    byModel: {},
                };

                records.forEach((record) => {
                    const count = Number(record.vehicle_count) || Number(record.total_vehicles) || 0;
                    totals.totalVehicles += count;
                    if (record.county_name) totals.counties.add(record.county_name);
                    if (record.model_description || record.model) totals.models.add(record.model_description || record.model);

                    if (record.county_name) {
                        totals.byCounty[record.county_name] = (totals.byCounty[record.county_name] || 0) + count;
                    }
                    if (record.model_description || record.model) {
                        const modelKey = record.model_description || record.model;
                        totals.byModel[modelKey] = (totals.byModel[modelKey] || 0) + count;
                    }
                });

                const topCounties = Object.entries(totals.byCounty)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 5)
                    .map(([name, count]) => ({ name, count }));

                const topModels = Object.entries(totals.byModel)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 5)
                    .map(([name, count]) => ({ name, count }));

                return {
                    totalVehicles: totals.totalVehicles,
                    countiesCount: totals.counties.size,
                    modelsCount: totals.models.size,
                    topCounties,
                    topModels,
                };
            }

            function createCard(title, value, subtitle) {
                return `
                    <div class="hero-mini-card" style="padding:18px; text-align:center; background: rgba(255,255,255,0.02); border: 1px solid rgba(148,163,184,0.08); border-radius: 12px;">
                        <p class="mini-label" style="margin-bottom:8px; color: var(--accent, #38bdf8); font-weight: 600;">${title}</p>
                        <p class="mini-value" style="margin-bottom:6px; font-size: 1.4rem; font-weight:700;">${value}</p>
                        <p class="form-label" style="color:#94a3b8; font-size:0.85rem; margin:0;">${subtitle}</p>
                    </div>
                `;
            }

            function renderCompare({ brandA, brandB, year, summaryA, summaryB }) {
                const diff = summaryA.totalVehicles - summaryB.totalVehicles;
                const winner = diff === 0 ? 'Egalitate' : diff > 0 ? brandA : brandB;
                const diffText = diff === 0 ? 'Același volum total' : `${window.appUtils.formatNumber(Math.abs(diff))} vehicule în plus`;

                const topCountiesA = summaryA.topCounties.map((item) => `<li><span>${item.name}</span> <strong>${window.appUtils.formatNumber(item.count)}</strong></li>`).join('');
                const topCountiesB = summaryB.topCounties.map((item) => `<li><span>${item.name}</span> <strong>${window.appUtils.formatNumber(item.count)}</strong></li>`).join('');

                const topModelsA = summaryA.topModels.map((item) => `<li><span>${item.name}</span> <strong>${window.appUtils.formatNumber(item.count)}</strong></li>`).join('');
                const topModelsB = summaryB.topModels.map((item) => `<li><span>${item.name}</span> <strong>${window.appUtils.formatNumber(item.count)}</strong></li>`).join('');

                compareSummary.innerHTML = `
                    <div class="hero-panel-grid" style="display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:20px; margin-bottom:24px;">
                        ${createCard(brandA, window.appUtils.formatNumber(summaryA.totalVehicles), `${summaryA.countiesCount} județe, ${summaryA.modelsCount} modele`)}
                        ${createCard(brandB, window.appUtils.formatNumber(summaryB.totalVehicles), `${summaryB.countiesCount} județe, ${summaryB.modelsCount} modele`)}
                    </div>
                    <div class="hero-panel-card" style="margin-bottom:24px; padding: 20px; background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.15); border-radius: 12px;">
                        <p class="panel-eyebrow" style="color: var(--accent, #38bdf8); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; font-weight: 600;">Rezumat comparație</p>
                        <h2 style="font-size:1.5rem; margin-top: 4px; margin-bottom:8px;">${diff === 0 ? 'Egalitate perfectă' : winner + ' domină selecția'}</h2>
                        <p style="color:#94a3b8; margin: 0;">${diffText} în intervalul analizat (${year || 'toți anii'}). <strong>${winner}</strong> deține avantajul numeric de volum.</p>
                    </div>
                    <div class="hero-panel-grid" style="display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:18px;">
                        <div class="hero-panel-card" style="padding: 16px; background: rgba(30, 41, 59, 0.2); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 12px;">
                            <p class="panel-eyebrow" style="color: #94a3b8; font-weight: 600;">Top județe pentru ${brandA}</p>
                            <ul style="margin-top:12px; color:var(--text-main);">${topCountiesA || '<li>Fără date</li>'}</ul>
                        </div>
                        <div class="hero-panel-card" style="padding: 16px; background: rgba(30, 41, 59, 0.2); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 12px;">
                            <p class="panel-eyebrow" style="color: #94a3b8; font-weight: 600;">Top județe pentru ${brandB}</p>
                            <ul style="margin-top:12px; color:var(--text-main);">${topCountiesB || '<li>Fără date</li>'}</ul>
                        </div>
                    </div>
                    <div class="hero-panel-grid" style="display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:18px; margin-top: 18px;">
                        <div class="hero-panel-card" style="padding: 16px; background: rgba(30, 41, 59, 0.2); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 12px;">
                            <p class="panel-eyebrow" style="color: #94a3b8; font-weight: 600;">Modele principale ${brandA}</p>
                            <ul style="margin-top:12px; color:var(--text-main);">${topModelsA || '<li>Fără date</li>'}</ul>
                        </div>
                        <div class="hero-panel-card" style="padding: 16px; background: rgba(30, 41, 59, 0.2); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 12px;">
                            <p class="panel-eyebrow" style="color: #94a3b8; font-weight: 600;">Modele principale ${brandB}</p>
                            <ul style="margin-top:12px; color:var(--text-main);">${topModelsB || '<li>Fără date</li>'}</ul>
                        </div>
                    </div>
                `;
            }

            async function loadFilters() {
                try {
                    // CORECTAT: Eliminat / din fața URL-ului pentru funcționare corectă în subfoldere locale
                    const filters = await window.appApi.getJson('api/filters.php');
                    yearSelect.innerHTML = '<option value="">Toți anii</option>';
                    (Array.isArray(filters.years) ? filters.years : []).forEach((year) => {
                        yearSelect.appendChild(window.appUtils.createOption(year, year));
                    });

                    brandASelect.innerHTML = '<option value="">Alege marca A</option>';
                    brandBSelect.innerHTML = '<option value="">Alege marca B</option>';
                    (Array.isArray(filters.brands) ? filters.brands : []).forEach((brand) => {
                        const option = window.appUtils.createOption(brand.name, brand.name);
                        brandASelect.appendChild(option.cloneNode(true));
                        brandBSelect.appendChild(option.cloneNode(true));
                    });
                } catch (err) {
                    console.error('compare filters error', err);
                    compareSummary.innerHTML = '<p>Nu am putut încărca filtrele de comparație.</p>';
                }
            }

            async function fetchCompareData(brand, year) {
                const params = new URLSearchParams();
                if (brand) params.set('brand', brand);
                if (year) params.set('year', year);
                params.set('limit', '5000');

                // CORECTAT: Eliminat / din fața URL-ului pentru interogare locală corectă
                const data = await window.appApi.getJson('api/search.php?' + params.toString());
                return data.result || data || [];
            }

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const brandA = brandASelect.value;
                const brandB = brandBSelect.value;
                const year = yearSelect.value;

                if (!brandA || !brandB) {
                    compareSummary.innerHTML = '<p style="color:#ef4444; text-align:center;">Completează ambele mărci pentru comparație.</p>';
                    return;
                }

                if (brandA === brandB) {
                    compareSummary.innerHTML = '<p style="color:#ef4444; text-align:center;">Alege două mărci diferite.</p>';
                    return;
                }

                compareSummary.innerHTML = '<p style="text-align:center; color:#94a3b8;">Se compară datele parcului auto...</p>';

                try {
                    const [recordsA, recordsB] = await Promise.all([
                        fetchCompareData(brandA, year),
                        fetchCompareData(brandB, year),
                    ]);

                    const summaryA = aggregateData(recordsA);
                    const summaryB = aggregateData(recordsB);

                    renderCompare({ brandA, brandB, year, summaryA, summaryB });
                } catch (err) {
                    console.error('compare fetch error', err);
                    compareSummary.innerHTML = '<p style="color:#ef4444; text-align:center;">Eroare la încărcarea datelor de comparație.</p>';
                }
            });

            resetButton.addEventListener('click', () => {
                form.reset();
                compareSummary.innerHTML = '<p>Selectează două mărci pentru a vedea comparația.</p>';
            });

            await loadFilters();
        })();
    </script>
</body>

</html>