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
    <title><?php echo htmlspecialchars($appName); ?> - Hartă interactivă</title>
    <meta name="description"
        content="Componentă cartografică pentru explorarea distribuției geografice a parcului auto din România.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/map.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>
    <div class="page-shell map-page-shell">
        <header class="site-header">
            <div class="container header-inner">
                <a href="index.php" class="brand-mark">
                    <span class="brand-badge">P</span>
                    <span class="brand-text"><?php echo htmlspecialchars($appName); ?></span>
                </a>

                <nav class="main-nav" aria-label="Navigație principală">
                    <a href="index.php" class="nav-link">Acasă</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="map-view.php" class="nav-link active">Hartă</a>
                    <a href="search-view.php" class="nav-link">Căutare</a>
                    <a href="compare.php" class="nav-link">Comparații</a>
                    <a href="about.php" class="nav-link">Despre</a>
                    <a href="/admin/login.php" class="nav-link nav-link-admin">
    <svg class="admin-lock-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M17 10V8a5 5 0 0 0-10 0v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="5" y="10" width="14" height="10" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
        <circle cx="12" cy="15" r="1.2" fill="currentColor"/>
    </svg>
    <span>ADMIN</span>
</a>
                </nav>
            </div>
        </header>

        <main class="map-main">
            <section class="map-hero-section">
                <div class="container map-hero-grid">
                    <div class="map-hero-content">
                        <p class="section-kicker">Componentă cartografică</p>
                        <h1>Hartă interactivă pentru distribuția teritorială a parcului auto</h1>
                        <p class="map-lead">
                            Explorează distribuția geografică a indicatorilor disponibili la nivel de județ și
                            evidențiază diferențele teritoriale prin filtrare după an, tip de combustibil și categorie
                            națională. Harta funcționează ca punct principal de explorare spațială a datelor.
                        </p>

                        <div class="map-hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="search-view.php">Acces către căutare avansată</a>
                        </div>
                    </div>

                    <aside class="map-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">An implicit</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $defaultYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Interval analizat</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $minYear); ?> -
                                <?php echo htmlspecialchars((string) $maxYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Mod principal</span>
                            <span class="hero-stat-value">Poligoane județe + hover/click contextual</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="map-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Panou de filtrare</p>
                        <h2>Configurarea stratului de analiză</h2>
                        <p class="section-lead">
                            Selectarea criteriilor actualizează asincron valorile agregate de pe hartă și informațiile
                            detaliate pentru județul selectat.
                        </p>
                    </div>

                    <form id="map-filters-form" class="filters-panel" autocomplete="off">
                        <div class="filters-grid map-filters-grid">
                            <div class="form-field">
                                <label for="map-filter-year">An</label>
                                <select id="map-filter-year" name="year">
                                    <option value="">Selectare an</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="map-filter-fuel-type">Combustibil</label>
                                <select id="map-filter-fuel-type" name="fuel_type">
                                    <option value="">Toate tipurile de combustibil</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="map-filter-national-category">Categorie națională</label>
                                <select id="map-filter-national-category" name="national_category">
                                    <option value="">Toate categoriile</option>
                                </select>
                            </div>
                        </div>

                        <div class="filters-actions">
                            <button type="submit" class="btn btn-primary">Aplicare filtre</button>
                            <button type="button" class="btn btn-secondary" id="reset-map-filters">Resetare filtre</button>
                            <a
                                href="api/export.php?resource=map&format=csv&year=<?php echo urlencode((string) $defaultYear); ?>"
                                class="btn btn-secondary"
                                id="map-export-csv">
                                Export CSV
                            </a>
                        </div>
                    </form>

                    <div class="dashboard-status-bar map-status-bar">
                        <div class="status-pill">
                            <span class="status-label">Context curent</span>
                            <span class="status-value" id="map-selection-summary">Se încarcă sumarul selecției...</span>
                        </div>

                        <div class="status-pill">
                            <span class="status-label">Stare hartă</span>
                            <span class="status-value" id="map-loading-state">Pregătire încărcare hartă</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="map-section">
                <div class="container map-layout">
                    <section class="map-main-card">
                        <div class="map-card-header">
                            <div>
                                <p class="chart-kicker">Vizualizare cartografică</p>
                                <h2>Distribuție pe județe</h2>
                                <p class="chart-caption">
                                    Exportul CSV descarcă datele teritoriale pentru selecția activă: an, combustibil și categorie națională.
                                </p>
                            </div>
                            <div class="chart-card-actions">
                                <span class="chart-badge">GeoJSON + Leaflet</span>
                                <a href="api/export.php?resource=map&format=csv&year=<?php echo urlencode((string) $defaultYear); ?>" class="btn btn-secondary btn-sm" id="map-export-inline">
                                    Export CSV
                                </a>
                            </div>
                        </div>

                        <div class="map-card-body">
                            <div id="map-canvas" class="map-canvas-container">
                                <div class="map-loader" id="map-loader">Se inițializează componenta cartografică...</div>
                            </div>

                            <div class="map-legend-box">
                                <div class="map-legend-header">
                                    <h3>Legendă</h3>
                                    <p>
                                        Intensitatea culorii reflectă valoarea agregată pentru selecția activă.
                                        Hover-ul și click-ul pe județ actualizează panoul contextual din dreapta.
                                    </p>
                                </div>

                                <div class="legend-scale">
                                    <span class="legend-step legend-step-1"></span>
                                    <span class="legend-step legend-step-2"></span>
                                    <span class="legend-step legend-step-3"></span>
                                    <span class="legend-step legend-step-4"></span>
                                    <span class="legend-step legend-step-5"></span>
                                </div>

                                <div class="legend-labels">
                                    <span>valoare scăzută</span>
                                    <span>valoare ridicată</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <aside class="map-sidebar">
                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Județ selectat</p>
                                    <h2>Panou contextual</h2>
                                </div>
                            </div>

                            <div class="selected-county-box">
                                <p class="selected-county-name" id="selected-county-name">Niciun județ selectat</p>
                                <p class="selected-county-code" id="selected-county-code">Cod județ: -</p>
                            </div>

                            <div class="selected-metrics">
                                <div class="selected-metric">
                                    <span class="selected-metric-label">Total vehicule</span>
                                    <strong class="selected-metric-value" id="selected-county-total">-</strong>
                                </div>

                                <div class="selected-metric">
                                    <span class="selected-metric-label">An activ</span>
                                    <strong class="selected-metric-value" id="selected-county-year"><?php echo htmlspecialchars((string) $defaultYear); ?></strong>
                                </div>

                                <div class="selected-metric">
                                    <span class="selected-metric-label">Marcă predominantă</span>
                                    <strong class="selected-metric-value" id="selected-county-brand">-</strong>
                                </div>
                            </div>
                        </article>

                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Interpretare</p>
                                    <h2>Rezumat curent</h2>
                                </div>
                            </div>

                            <p id="map-context-summary" class="map-summary-text">
                                Selectarea filtrelor și a unui județ va actualiza această zonă cu un rezumat clar al
                                contextului activ și al valorilor afișate.
                            </p>

                            <ul class="info-list compact-info-list">
                                <li>an activ</li>
                                <li>filtru combustibil</li>
                                <li>filtru categorie națională</li>
                                <li>valoare agregată pe județ</li>
                                <li>brand predominant pe județ</li>
                            </ul>
                        </article>

                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Navigare</p>
                                    <h2>Acțiuni rapide</h2>
                                </div>
                            </div>

                            <div class="insight-actions">
                                <a class="btn btn-primary" href="dashboard.php">Înapoi la dashboard</a>
                                <a class="btn btn-secondary" href="search-view.php">Acces către căutare</a>
                            </div>
                        </article>
                    </aside>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Map Explorer</span></p>
                    <p class="footer-text">Modul cartografic pentru explorarea distribuției teritoriale a datelor despre parcul auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioadă analizată: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Filtrare asincronă + poligoane GeoJSON + panou contextual</span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        window.APP_API_BASE_URL = 'api';
        window.APP_EXPORT_BASE_URL = 'api/export.php';
        window.APP_DEFAULT_YEAR = <?php echo json_encode((int) $defaultYear); ?>;
        window.APP_MIN_YEAR = <?php echo json_encode((int) $minYear); ?>;
        window.APP_MAX_YEAR = <?php echo json_encode((int) $maxYear); ?>;
        window.APP_GEOJSON_URL = 'assets/data/romania-counties.geojson';
    </script>

    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/map.js"></script>
</body>

</html>