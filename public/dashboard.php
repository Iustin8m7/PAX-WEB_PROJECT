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
    <title><?php echo htmlspecialchars($appName); ?> - Dashboard analitic</title>
    <meta name="description" content="Dashboard analitic pentru explorarea și vizualizarea datelor despre parcul auto din România.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <div class="page-shell dashboard-shell">
        <header class="site-header">
            <div class="container header-inner">
                <a href="index.php" class="brand-mark">
                    <span class="brand-badge">P</span>
                    <span class="brand-text"><?php echo htmlspecialchars($appName); ?></span>
                </a>

                <nav class="main-nav" aria-label="Navigație principală">
                    <a href="index.php" class="nav-link">Acasă</a>
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="map-view.php" class="nav-link">Hartă</a>
                    <a href="search-view.php" class="nav-link">Căutare</a>
                    <a href="compare.php" class="nav-link">Comparații</a>
                    <a href="about.php" class="nav-link">Despre</a>
                </nav>
            </div>
        </header>

        <main class="dashboard-main">
            <section class="dashboard-hero-section">
                <div class="container dashboard-hero-grid">
                    <div class="dashboard-hero-content">
                        <p class="section-kicker">Business Intelligence pentru date auto</p>
                        <h1>Dashboard analitic pentru distribuția parcului auto din România</h1>
                        <p class="dashboard-lead">
                            Explorează distribuția indicatorilor principali, corelează tendințele temporale cu structura pe combustibil
                            și categorii, analizează topurile relevante și utilizează harta ca punct de intrare în explorarea teritorială.
                        </p>

                        <div class="dashboard-hero-actions">
                            <a class="btn btn-primary" href="map-view.php">Acces către harta completă</a>
                            <a class="btn btn-secondary" href="search-view.php">Acces către căutarea avansată</a>
                        </div>
                    </div>

                    <aside class="dashboard-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">An implicit</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $defaultYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Interval analizat</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $minYear); ?> - <?php echo htmlspecialchars((string) $maxYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Mod de lucru</span>
                            <span class="hero-stat-value">Filtrare asincronă + statistici + preview geografic</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Panou de control</p>
                        <h2>Filtre globale pentru dashboard</h2>
                        <p class="section-lead">
                            Selectarea criteriilor actualizează asincron cardurile de overview, graficele,
                            clasamentul teritorial și preview-ul contextual al hărții.
                        </p>
                    </div>

                    <form id="dashboard-filters-form" class="filters-panel" autocomplete="off">
                        <div class="filters-grid">
                            <div class="form-field">
                                <label for="filter-year">An</label>
                                <select id="filter-year" name="year">
                                    <option value="">Toți anii disponibili</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="filter-county">Județ</label>
                                <select id="filter-county" name="county_code">
                                    <option value="">Toate județele</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="filter-national-category">Categorie națională</label>
                                <select id="filter-national-category" name="national_category">
                                    <option value="">Toate categoriile</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="filter-community-category">Categorie comunitară</label>
                                <select id="filter-community-category" name="community_category">
                                    <option value="">Toate categoriile comunitare</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="filter-fuel-type">Combustibil</label>
                                <select id="filter-fuel-type" name="fuel_type">
                                    <option value="">Toate tipurile de combustibil</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="filter-brand">Marcă</label>
                                <select id="filter-brand" name="brand">
                                    <option value="">Toate mărcile</option>
                                </select>
                            </div>
                        </div>

                        <div class="filters-actions">
                            <button type="submit" class="btn btn-primary">Aplicare filtre</button>
                            <button type="button" class="btn btn-secondary" id="reset-dashboard-filters">Resetare filtre</button>

                            <a
                                href="api/export.php?resource=statistics&format=csv&view=overview&year=<?php echo urlencode((string) $defaultYear); ?>"
                                class="btn btn-secondary"
                                id="dashboard-export-overview">
                                Export overview CSV
                            </a>

                            <a
                                href="api/export.php?resource=statistics&format=csv&view=yearly-totals"
                                class="btn btn-secondary"
                                id="dashboard-export-yearly">
                                Export yearly totals CSV
                            </a>

                            <a
                                href="api/export.php?resource=statistics&format=csv&view=county-ranking&year=<?php echo urlencode((string) $defaultYear); ?>"
                                class="btn btn-secondary"
                                id="dashboard-export-ranking">
                                Export county ranking CSV
                            </a>
                        </div>
                    </form>

                    <div class="dashboard-status-bar">
                        <div class="status-pill">
                            <span class="status-label">Context curent</span>
                            <span class="status-value" id="dashboard-selection-summary">Se încarcă sumarul selecției...</span>
                        </div>

                        <div class="status-pill">
                            <span class="status-label">Stare dashboard</span>
                            <span class="status-value" id="dashboard-loading-state">Pregătit pentru încărcare</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Indicatori sintetici</p>
                        <h2>Overview al selecției active</h2>
                        <p class="section-lead">
                            Exportul de overview descarcă indicatorii sintetici pentru contextul activ al dashboard-ului.
                        </p>
                    </div>

                    <div class="overview-grid">
                        <article class="overview-card">
                            <span class="overview-label">Total vehicule</span>
                            <strong class="overview-value" id="overview-total-vehicles">-</strong>
                            <span class="overview-meta">valoare agregată pentru contextul curent</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Județe active</span>
                            <strong class="overview-value" id="overview-counties-count">-</strong>
                            <span class="overview-meta">unități teritoriale prezente în selecție</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Mărci distincte</span>
                            <strong class="overview-value" id="overview-brands-count">-</strong>
                            <span class="overview-meta">producători identificați în datele active</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Tipuri combustibil</span>
                            <strong class="overview-value" id="overview-fuel-types-count">-</strong>
                            <span class="overview-meta">diversitatea energetică a selecției</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Categorii naționale</span>
                            <strong class="overview-value" id="overview-categories-count">-</strong>
                            <span class="overview-meta">structura pe clase de vehicule</span>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Vizualizări principale</p>
                        <h2>Grafice și distribuții</h2>
                        <p class="section-lead">
                            Dashboard-ul combină evoluția temporală, topurile relevante și structura internă a datelor
                            în vizualizări distincte, pregătite pentru randare dinamică, export CSV și export vizual WebP.
                        </p>
                    </div>

                    <div class="dashboard-grid">
                        <article class="chart-card chart-card-large">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Evoluție temporală</p>
                                    <h3>Total vehicule pe ani</h3>
                                </div>
                                <div class="chart-card-actions">
                                    <span class="chart-badge">statistics.php - view yearly-totals</span>
                                    <a href="api/export.php?resource=statistics&format=csv&view=yearly-totals" class="btn btn-secondary btn-sm" id="chart-export-yearly-totals">Export CSV</a>
                                    <button type="button" class="btn btn-secondary btn-sm" id="chart-export-yearly-totals-webp">Export WebP</button>
                                </div>
                            </div>
                            <div class="chart-card-body">
                                <div class="chart-canvas" id="chart-yearly-totals"></div>
                                <div class="chart-caption" id="chart-yearly-totals-caption">Evoluția anuală a volumului total de vehicule va fi afișată aici.</div>
                            </div>
                        </article>

                        <article class="chart-card">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Topuri</p>
                                    <h3>Top mărci</h3>
                                </div>
                                <div class="chart-card-actions">
                                    <span class="chart-badge">statistics.php - view top-brands</span>
                                    <a href="api/export.php?resource=statistics&format=csv&view=top-brands&year=<?php echo urlencode((string) $defaultYear); ?>&limit=10" class="btn btn-secondary btn-sm" id="chart-export-top-brands">Export CSV</a>
                                    <button type="button" class="btn btn-secondary btn-sm" id="chart-export-top-brands-webp">Export WebP</button>
                                </div>
                            </div>
                            <div class="chart-card-body">
                                <div class="chart-canvas" id="chart-top-brands"></div>
                                <div class="chart-caption" id="chart-top-brands-caption">Ierarhia celor mai reprezentate mărci va fi afișată aici.</div>
                            </div>
                        </article>

                        <article class="chart-card">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Distribuție</p>
                                    <h3>Structură pe combustibil</h3>
                                </div>
                                <div class="chart-card-actions">
                                    <span class="chart-badge">statistics.php - view fuel-distribution</span>
                                    <a href="api/export.php?resource=statistics&format=csv&view=fuel-distribution&year=<?php echo urlencode((string) $defaultYear); ?>" class="btn btn-secondary btn-sm" id="chart-export-fuel-distribution">Export CSV</a>
                                    <button type="button" class="btn btn-secondary btn-sm" id="chart-export-fuel-distribution-webp">Export WebP</button>
                                </div>
                            </div>
                            <div class="chart-card-body">
                                <div class="chart-canvas" id="chart-fuel-distribution"></div>
                                <div class="chart-caption" id="chart-fuel-distribution-caption">Distribuția pe tipuri de combustibil va fi afișată aici.</div>
                            </div>
                        </article>

                        <article class="chart-card chart-card-wide">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Structură categorii</p>
                                    <h3>Distribuție pe categorii naționale</h3>
                                </div>
                                <div class="chart-card-actions">
                                    <span class="chart-badge">statistics.php - view category-distribution</span>
                                    <a href="api/export.php?resource=statistics&format=csv&view=category-distribution&year=<?php echo urlencode((string) $defaultYear); ?>" class="btn btn-secondary btn-sm" id="chart-export-category-distribution">Export CSV</a>
                                    <button type="button" class="btn btn-secondary btn-sm" id="chart-export-category-distribution-webp">Export WebP</button>
                                </div>
                            </div>
                            <div class="chart-card-body">
                                <div class="chart-canvas" id="chart-category-distribution"></div>
                                <div class="chart-caption" id="chart-category-distribution-caption">Structura pe categorii naționale va fi afișată aici.</div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container dashboard-lower-grid">
                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Clasament teritorial</p>
                                <h3>Top județe</h3>
                            </div>
                            <div class="chart-card-actions">
                                <span class="chart-badge">statistics.php - view county-ranking</span>
                                <a href="api/export.php?resource=statistics&format=csv&view=county-ranking&year=<?php echo urlencode((string) $defaultYear); ?>" class="btn btn-secondary btn-sm" id="chart-export-county-ranking">Export CSV</a>
                            </div>
                        </div>

                        <div class="data-card-body">
                            <div class="table-shell">
                                <table class="summary-table" id="county-ranking-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cod</th>
                                            <th>Județ</th>
                                            <th>Total vehicule</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">Clasamentul județelor va fi încărcat aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>

                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Preview geografic</p>
                                <h3>Distribuție pe hartă</h3>
                            </div>
                            <span class="chart-badge">map.php</span>
                        </div>

                        <div class="data-card-body">
                            <div class="map-preview" id="dashboard-map-preview">
                                Preview-ul de hartă va fi integrat aici.
                            </div>

                            <div class="mini-legend">
                                <span class="mini-legend-title">Legendă preview</span>
                                <div class="mini-legend-scale">
                                    <span class="mini-legend-step mini-legend-step-1"></span>
                                    <span class="mini-legend-step mini-legend-step-2"></span>
                                    <span class="mini-legend-step mini-legend-step-3"></span>
                                    <span class="mini-legend-step mini-legend-step-4"></span>
                                </div>
                            </div>

                            <div class="map-preview-footer">
                                <p class="map-preview-text" id="dashboard-map-summary">
                                    Preview-ul va reflecta contextul activ și va oferi legătura rapidă către componenta cartografică detaliată.
                                </p>
                                <div class="map-preview-actions">
                                    <a class="btn btn-secondary" href="map-view.php">Deschidere hartă completă</a>
                                    <a class="btn btn-secondary" href="api/export.php?resource=map&format=csv&year=<?php echo urlencode((string) $defaultYear); ?>" id="dashboard-export-map-data">Export map CSV</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="dashboard-section dashboard-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div class="insight-content">
                            <p class="section-kicker">Rezumat operațional</p>
                            <h2>Contextul activ al dashboard-ului</h2>
                            <p id="dashboard-context-summary" class="section-lead">
                                Dashboard-ul va descrie aici selecția activă și modul în care aceasta influențează indicatorii, graficele și componentele teritoriale.
                            </p>
                        </div>

                        <div class="insight-actions">
                            <a class="btn btn-primary" href="search-view.php">Acces către căutare detaliată</a>
                            <a class="btn btn-secondary" href="compare.php">Acces către comparații</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?></p>
                    <p class="footer-text">Dashboard public pentru explorarea, compararea și interpretarea datelor despre parcul auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Date analizate: <?php echo htmlspecialchars((string) $minYear); ?> - <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Interfață publică conectată la servicii Web REST</span>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/export-image.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>