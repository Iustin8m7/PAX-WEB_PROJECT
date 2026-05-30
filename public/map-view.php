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
    <title><?php echo htmlspecialchars($appName); ?> - Harta interactivă</title>
    <meta name="description" content="Hartă interactivă pentru explorarea distribuției geografice a parcului auto din România.">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/map.css">
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
                    <a href="index.php">Acasă</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="map-view.php" class="active">Hartă</a>
                    <a href="search-view.php">Căutare</a>
                    <a href="compare.php">Comparații</a>
                    <a href="about.php">Despre</a>
                </nav>
            </div>
        </header>

        <main class="map-main">
            <section class="map-hero">
                <div class="container map-hero-grid">
                    <div class="map-hero-content">
                        <p class="section-kicker">Componentă cartografică</p>
                        <h1>Harta interactivă a parcului auto din România</h1>
                        <p class="map-lead">
                            Explorează distribuția teritorială a indicatorilor disponibili și evidențiază
                            diferențele dintre județe prin filtrare după an, tip de combustibil și categorie națională.
                        </p>

                        <div class="map-hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="search-view.php">Acces către căutare</a>
                        </div>
                    </div>

                    <aside class="map-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">An implicit</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string)$defaultYear); ?></span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Interval analizat</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Mod vizualizare</span>
                            <span class="hero-stat-value">Distribuție geografică pe județe</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="map-section">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Filtre pentru hartă</p>
                        <h2>Configurarea stratului de analiză</h2>
                        <p class="section-lead">
                            Selectarea criteriilor actualizează stratul de date afișat pe hartă și informațiile
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
                        </div>
                    </form>
                </div>
            </section>

            <section class="map-section">
                <div class="container map-layout">
                    <div class="map-canvas-card">
                        <div class="map-card-header">
                            <div>
                                <p class="chart-kicker">Vizualizare geografică</p>
                                <h2>Distribuție pe județe</h2>
                            </div>
                            <span class="chart-badge">map.php</span>
                        </div>

                        <div id="map-canvas" class="map-canvas">
                            Harta interactivă va fi randată aici.
                        </div>

                        <div class="map-legend">
                            <div class="map-legend-header">
                                <h3>Legendă</h3>
                                <p>Intensitatea culorii reflectă valoarea agregată a selecției curente.</p>
                            </div>

                            <div class="legend-scale">
                                <span class="legend-step legend-step-1"></span>
                                <span class="legend-step legend-step-2"></span>
                                <span class="legend-step legend-step-3"></span>
                                <span class="legend-step legend-step-4"></span>
                                <span class="legend-step legend-step-5"></span>
                            </div>

                            <div class="legend-labels">
                                <span>scăzut</span>
                                <span>ridicat</span>
                            </div>
                        </div>
                    </div>

                    <aside class="map-side-panel">
                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Județ selectat</p>
                                    <h2>Detalii contextuale</h2>
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
                                    <strong class="selected-metric-value" id="selected-county-year"><?php echo htmlspecialchars((string)$defaultYear); ?></strong>
                                </div>
                            </div>
                        </article>

                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Context de filtrare</p>
                                    <h2>Rezumat curent</h2>
                                </div>
                            </div>

                            <p id="map-selection-summary" class="map-summary-text">
                                Selectarea filtrelor va actualiza sumarul contextual pentru harta afișată.
                            </p>

                            <ul class="info-list compact-info-list">
                                <li>an activ</li>
                                <li>filtru combustibil</li>
                                <li>filtru categorie națională</li>
                                <li>valoare agregată pe județ</li>
                            </ul>
                        </article>

                        <article class="map-info-card">
                            <div class="map-card-header">
                                <div>
                                    <p class="chart-kicker">Acces rapid</p>
                                    <h2>Legături utile</h2>
                                </div>
                            </div>

                            <div class="quick-action-list">
                                <a class="btn btn-secondary btn-block" href="dashboard.php">Înapoi la dashboard</a>
                                <a class="btn btn-secondary btn-block" href="search-view.php">Deschidere căutare</a>
                                <a class="btn btn-secondary btn-block" href="compare.php">Deschidere comparații</a>
                            </div>
                        </article>
                    </aside>
                </div>
            </section>

            <section class="map-section map-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div>
                            <p class="section-kicker">Mod de utilizare</p>
                            <h2>Explorare geografică a datelor</h2>
                            <p class="section-lead">
                                Selectarea unui județ din hartă va actualiza panoul lateral cu informații detaliate,
                                iar modificarea filtrelor va reconfigura distribuția afișată în mod asincron.
                            </p>
                        </div>

                        <div class="insight-actions">
                            <a class="btn btn-primary" href="search-view.php">Acces către rezultate detaliate</a>
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
                    <p class="footer-text">Componentă cartografică pentru analiza distribuției teritoriale a parcului auto.</p>
                </div>

                <div class="footer-meta">
                    <span>Date analizate: <?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                    <span>Filtrare asincronă + reprezentare geografică</span>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/map.js"></script>
</body>
</html>