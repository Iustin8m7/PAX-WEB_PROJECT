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
    <meta name="description"
        content="Pagină de comparații pentru analiza diferențelor dintre două selecții de date privind parcul auto din România.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/compare.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body>
    <div class="page-shell compare-page-shell">
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

        <main class="compare-main">
            <section class="compare-hero-section">
                <div class="container compare-hero-grid">
                    <div class="compare-hero-content">
                        <p class="section-kicker">Analiză comparativă</p>
                        <h1>Compararea a două selecții de date în același context vizual</h1>
                        <p class="compare-lead">
                            Explorează diferențele dintre două seturi de criterii și evidențiază variațiile dintre perioade, județe,
                            categorii, combustibili sau mărci prin indicatori sintetici, grafice comparative și
                            clasamente paralele.
                        </p>

                        <div class="compare-hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="search-view.php">Acces către căutare</a>
                        </div>
                    </div>

                    <aside class="compare-hero-panel">
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
                            <span class="hero-stat-label">Format comparație</span>
                            <span class="hero-stat-value">Selecția A vs Selecția B</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="compare-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Configurarea comparației</p>
                        <h2>Definirea selecțiilor A și B</h2>
                        <p class="section-lead">
                            Fiecare selecție este definită prin propriul set de filtre. Aplicarea comparației
                            actualizează asincron indicatorii, diferențele și vizualizările comparative.
                        </p>
                    </div>

                    <form id="compare-form" class="compare-filters-layout" autocomplete="off">
                        <section class="compare-filter-card">
                            <div class="compare-filter-card-header">
                                <p class="chart-kicker">Selecția A</p>
                                <h3>Context de referință</h3>
                            </div>

                            <div class="filters-grid compare-filters-grid">
                                <div class="form-field">
                                    <label for="compare-a-year">An</label>
                                    <select id="compare-a-year" name="a_year">
                                        <option value="">Toți anii disponibili</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-a-county">Județ</label>
                                    <select id="compare-a-county" name="a_county_code">
                                        <option value="">Toate județele</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-a-national-category">Categorie națională</label>
                                    <select id="compare-a-national-category" name="a_national_category">
                                        <option value="">Toate categoriile</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-a-fuel-type">Combustibil</label>
                                    <select id="compare-a-fuel-type" name="a_fuel_type">
                                        <option value="">Toate tipurile de combustibil</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-a-brand">Marcă</label>
                                    <select id="compare-a-brand" name="a_brand">
                                        <option value="">Toate mărcile</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-a-model">Model</label>
                                    <input type="text" id="compare-a-model" name="a_model" placeholder="Exemplu: LOGAN, SPRINTER">
                                </div>
                            </div>
                        </section>

                        <section class="compare-filter-card">
                            <div class="compare-filter-card-header">
                                <p class="chart-kicker">Selecția B</p>
                                <h3>Context comparat</h3>
                            </div>

                            <div class="filters-grid compare-filters-grid">
                                <div class="form-field">
                                    <label for="compare-b-year">An</label>
                                    <select id="compare-b-year" name="b_year">
                                        <option value="">Toți anii disponibili</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-b-county">Județ</label>
                                    <select id="compare-b-county" name="b_county_code">
                                        <option value="">Toate județele</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-b-national-category">Categorie națională</label>
                                    <select id="compare-b-national-category" name="b_national_category">
                                        <option value="">Toate categoriile</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-b-fuel-type">Combustibil</label>
                                    <select id="compare-b-fuel-type" name="b_fuel_type">
                                        <option value="">Toate tipurile de combustibil</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-b-brand">Marcă</label>
                                    <select id="compare-b-brand" name="b_brand">
                                        <option value="">Toate mărcile</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label for="compare-b-model">Model</label>
                                    <input type="text" id="compare-b-model" name="b_model" placeholder="Exemplu: DUSTER, DAILY">
                                </div>
                            </div>
                        </section>

                        <div class="filters-actions compare-actions">
                            <button type="submit" class="btn btn-primary">Aplicare comparație</button>
                            <button type="button" class="btn btn-secondary" id="reset-compare-filters">Resetare comparație</button>
                        </div>
                    </form>

                    <div class="dashboard-status-bar compare-status-bar">
                        <div class="status-pill">
                            <span class="status-label">Context comparație</span>
                            <span class="status-value" id="compare-summary">Se pregătește comparația...</span>
                        </div>

                        <div class="status-pill">
                            <span class="status-label">Stare</span>
                            <span class="status-value" id="compare-loading-state">Pregătit pentru încărcare</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="compare-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Indicatori sintetici</p>
                        <h2>Rezumat comparativ</h2>
                    </div>

                    <div class="compare-overview-grid">
                        <article class="overview-card compare-overview-card">
                            <span class="overview-label">Total vehicule A</span>
                            <strong class="overview-value" id="compare-total-a">-</strong>
                            <span class="overview-meta">valoare agregată pentru selecția A</span>
                        </article>

                        <article class="overview-card compare-overview-card">
                            <span class="overview-label">Total vehicule B</span>
                            <strong class="overview-value" id="compare-total-b">-</strong>
                            <span class="overview-meta">valoare agregată pentru selecția B</span>
                        </article>

                        <article class="overview-card compare-overview-card">
                            <span class="overview-label">Diferență absolută</span>
                            <strong class="overview-value" id="compare-difference-absolute">-</strong>
                            <span class="overview-meta">diferența dintre A și B</span>
                        </article>

                        <article class="overview-card compare-overview-card">
                            <span class="overview-label">Diferență procentuală</span>
                            <strong class="overview-value" id="compare-difference-percent">-</strong>
                            <span class="overview-meta">raport procentual între selecții</span>
                        </article>
                    </div>
                </div>
            </section>

            <section class="compare-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Vizualizare comparativă</p>
                        <h2>Comparație vizuală între selecții</h2>
                        <p class="section-lead">
                            Reprezentarea grafică oferă o vedere rapidă asupra diferențelor dintre volumele agregate
                            și asupra raportului dintre cele două selecții.
                        </p>
                    </div>

                    <div class="compare-chart-card">
                        <div class="chart-card-header">
                            <div>
                                <p class="chart-kicker">Bar chart comparativ</p>
                                <h3>Selecția A versus Selecția B</h3>
                            </div>
                            <span class="chart-badge">Grafic comparativ</span>
                        </div>

                        <div class="chart-card-body">
                            <div class="chart-canvas" id="compare-main-chart"></div>
                            <div class="chart-caption" id="compare-main-chart-caption">
                                Comparația vizuală principală va fi afișată aici.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="compare-section">
                <div class="container compare-detail-grid">
                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Clasament A</p>
                                <h3>Top județe pentru selecția A</h3>
                            </div>
                        </div>

                        <div class="data-card-body">
                            <div class="table-shell">
                                <table class="summary-table" id="compare-top-counties-a-table">
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
                                            <td colspan="4">Clasamentul pentru selecția A va fi afișat aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>

                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Clasament B</p>
                                <h3>Top județe pentru selecția B</h3>
                            </div>
                        </div>

                        <div class="data-card-body">
                            <div class="table-shell">
                                <table class="summary-table" id="compare-top-counties-b-table">
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
                                            <td colspan="4">Clasamentul pentru selecția B va fi afișat aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="compare-section">
                <div class="container compare-detail-grid">
                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Modele dominante</p>
                                <h3>Top modele pentru selecția A</h3>
                            </div>
                        </div>

                        <div class="data-card-body">
                            <div class="table-shell">
                                <table class="summary-table" id="compare-top-models-a-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Marcă</th>
                                            <th>Model</th>
                                            <th>Total vehicule</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">Topul modelelor pentru selecția A va fi afișat aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>

                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Modele dominante</p>
                                <h3>Top modele pentru selecția B</h3>
                            </div>
                        </div>

                        <div class="data-card-body">
                            <div class="table-shell">
                                <table class="summary-table" id="compare-top-models-b-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Marcă</th>
                                            <th>Model</th>
                                            <th>Total vehicule</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">Topul modelelor pentru selecția B va fi afișat aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="compare-section compare-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div class="insight-content">
                            <p class="section-kicker">Interpretare</p>
                            <h2>Rezumatul comparației active</h2>
                            <p id="compare-context-summary" class="section-lead">
                                Comparația va actualiza această zonă cu un rezumat al diferențelor dintre selecția A și
                                selecția B, inclusiv variațiile de volum și repartiție.
                            </p>
                        </div>

                        <div class="insight-actions">
                            <a class="btn btn-primary" href="dashboard.php">Înapoi la dashboard</a>
                            <a class="btn btn-secondary" href="map-view.php">Acces către hartă</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Compare Explorer</span></p>
                    <p class="footer-text">Modul comparativ pentru analiza diferențelor dintre două selecții de date privind parcul auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioadă analizată: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Comparație asincronă + diferențe absolute și procentuale</span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        window.APP_API_BASE_URL = 'api';
        window.APP_DEFAULT_YEAR = <?php echo json_encode((int) $defaultYear); ?>;
        window.APP_MIN_YEAR = <?php echo json_encode((int) $minYear); ?>;
        window.APP_MAX_YEAR = <?php echo json_encode((int) $maxYear); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/compare.js"></script>
</body>

</html>