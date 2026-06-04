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
    <title><?php echo htmlspecialchars($appName); ?> - Căutare Avansată</title>
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        /* Design modern pentru tabelul generat din JS */
        .results-table {
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 16px;
            overflow: hidden;
        }

        .results-table th {
            background-color: rgba(255, 255, 255, 0.04);
            color: #94a3b8;
            padding: 14px 18px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .results-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.06);
            color: #f8fafc;
        }

        .results-table tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* Fix critic pentru textul invizibil din meniurile derulante */
        select option {
            background-color: #1e293b !important;
            color: #f8fafc !important;
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
                    <a href="search-view.php" class="nav-link active">Căutare</a>
                    <a href="compare.php" class="nav-link">Comparații</a>
                    <a href="about.php" class="nav-link">Despre</a>
                </nav>
            </div>
        </header>

        <main class="section">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">Căutare Avansată</p>
                    <h2>Filtrează în parcul auto</h2>
                    <p class="section-lead">Folosește filtrele de mai jos pentru a construi interogări detaliate.</p>
                </div>

                <form id="search-form" class="hero-panel-card" autocomplete="off">
                    <div class="hero-panel-grid"
                        style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                        <div class="form-field">
                            <label class="form-label">An</label>
                            <select id="search-year" name="year" class="select-custom">
                                <option value="">Toți anii</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Județ (cod)</label>
                            <select id="search-county" name="county_code" class="select-custom">
                                <option value="">Toate județele</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Marcă</label>
                            <select id="search-brand" name="brand" class="select-custom">
                                <option value="">Toate mărcile</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Tip combustibil</label>
                            <select id="search-fuel" name="fuel_type" class="select-custom">
                                <option value="">Toate tipurile</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Categorie națională</label>
                            <select id="search-national-category" name="national_category" class="select-custom">
                                <option value="">Toate categoriile</option>
                            </select>
                        </div>

                        <div style="display: flex; align-items: flex-end; gap: 12px;">
                            <button type="submit" class="btn btn-primary">Caută</button>
                            <button type="button" id="search-reset" class="btn btn-secondary">Resetare</button>
                        </div>
                    </div>
                </form>

                <section style="margin-top: 20px;">
                    <div id="search-results" class="hero-panel-card" style="padding: 16px;">
                        <div id="results-list">Rezultatele vor apărea aici după căutare.</div>
                        <div id="results-pagination" style="margin-top: 12px;"></div>
                    </div>
                </section>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Search</span></p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script>
        (async function () {
            const form = document.getElementById('search-form');
            const year = document.getElementById('search-year');
            const county = document.getElementById('search-county');
            const brand = document.getElementById('search-brand');
            const fuel = document.getElementById('search-fuel');
            const nat = document.getElementById('search-national-category');
            const resultsList = document.getElementById('results-list');
            const resetBtn = document.getElementById('search-reset');

            async function loadFilters() {
                try {
                    // CORECTAT: Schimbat din '/api/filters.php' în 'api/filters.php' pentru compatibilitate locală
                    const filters = await window.appApi.getJson('api/filters.php');

                    year.innerHTML = '<option value="">Toți anii</option>';
                    (Array.isArray(filters.years) ? filters.years : []).forEach(y => {
                        year.appendChild(window.appUtils.createOption(y, y));
                    });

                    county.innerHTML = '<option value="">Toate județele</option>';
                    (Array.isArray(filters.counties) ? filters.counties : []).forEach(c => {
                        county.appendChild(window.appUtils.createOption(c.code, c.name));
                    });

                    brand.innerHTML = '<option value="">Toate mărcile</option>';
                    (Array.isArray(filters.brands) ? filters.brands : []).forEach(b => {
                        brand.appendChild(window.appUtils.createOption(b.name, b.name));
                    });

                    fuel.innerHTML = '<option value="">Toate tipurile</option>';
                    (Array.isArray(filters.fuel_types) ? filters.fuel_types : []).forEach(f => {
                        fuel.appendChild(window.appUtils.createOption(f.name, f.name));
                    });

                    nat.innerHTML = '<option value="">Toate categoriile</option>';
                    (Array.isArray(filters.national_categories) ? filters.national_categories : []).forEach(n => {
                        nat.appendChild(window.appUtils.createOption(n.name, n.name));
                    });
                } catch (err) {
                    console.error('filters load error', err);
                    resultsList.textContent = 'Nu am putut încărca filtrele.';
                }
            }

            function renderResults(data) {
                if (!Array.isArray(data) || data.length === 0) {
                    resultsList.innerHTML = '<div>Nu s-au găsit rezultate pentru criteriile selectate.</div>';
                    return;
                }

                const table = document.createElement('table');
                table.className = 'results-table';
                table.style.width = '100%';
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>An</th>
                            <th>Județ</th>
                            <th>Marcă</th>
                            <th>Model</th>
                            <th>Combustibil</th>
                            <th style="text-align:right">Număr</th>
                        </tr>
                    </thead>
                `;

                const tbody = document.createElement('tbody');
                data.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${r.year ?? ''}</td>
                        <td>${r.county_name ?? r.county_code ?? ''}</td>
                        <td>${r.brand ?? ''}</td>
                        <td>${r.model ?? ''}</td>
                        <td>${r.fuel_type ?? ''}</td>
                        <td style="text-align:right">${window.appUtils.formatNumber(r.vehicle_count ?? r.total_vehicles ?? 0)}</td>
                    `;
                    tbody.appendChild(tr);
                });

                table.appendChild(tbody);
                resultsList.innerHTML = '';
                resultsList.appendChild(table);
            }

            async function doSearch(e) {
                if (e && e.preventDefault) e.preventDefault();

                const params = new URLSearchParams();
                if (year.value) params.set('year', year.value);
                if (county.value) params.set('county_code', county.value);
                if (brand.value) params.set('brand', brand.value);
                if (fuel.value) params.set('fuel_type', fuel.value);
                if (nat.value) params.set('national_category', nat.value);
                params.set('limit', '100');

                resultsList.textContent = 'Se caută...';

                try {
                    // CORECTAT: Schimbat din '/api/search.php?' în 'api/search.php?' pentru a funcționa corect local
                    const data = await window.appApi.getJson('api/search.php?' + params.toString());
                    renderResults(data.result || data || []);
                } catch (err) {
                    console.error('search error', err);
                    resultsList.textContent = 'Eroare la încărcarea rezultatelor.';
                }
            }

            form.addEventListener('submit', doSearch);
            resetBtn.addEventListener('click', () => {
                form.reset();
            });

            await loadFilters();
        })();
    </script>
</body>

</html>