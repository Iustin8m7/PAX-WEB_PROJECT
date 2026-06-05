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
    <title><?php echo htmlspecialchars($appName); ?> - Hartă Interactivă</title>
    <meta name="description"
        content="Hartă interactivă bazată pe Leaflet pentru explorarea distribuției geografice a parcului auto din România.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/map.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css?cachebust=1" />
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
                </nav>
            </div>
        </header>

        <main class="map-main">
            <section class="hero-section map-hero">
                <div class="container map-hero-grid">
                    <div class="map-hero-content">
                        <p class="hero-kicker">🗺️ Modul Cartografic Asincron</p>
                        <h1 class="hero-title">Distribuția Geografică <span class="hero-title-accent">a Parcului
                                Auto</span></h1>
                        <p class="hero-description">
                            Explorează vizual densitatea vehiculelor din România. Selectează filtrele dorite pentru a
                            vedea cum se reconfigurează topul producătorilor și numărul total de mașini la nivel de
                            județ.
                        </p>
                    </div>

                    <aside class="map-hero-panel">
                        <div class="hero-mini-card">
                            <span class="mini-label">An Implicit</span>
                            <span class="mini-value"
                                style="font-size: 1.4rem; color: var(--color-accent);"><?php echo htmlspecialchars((string) $defaultYear); ?></span>
                        </div>
                        <div class="hero-mini-card">
                            <span class="mini-label">Orizont Temporal</span>
                            <span class="mini-value"
                                style="font-size: 1.4rem; color: var(--color-accent);"><?php echo htmlspecialchars((string) $minYear); ?>-<?php echo htmlspecialchars((string) $maxYear); ?></span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="section map-section">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Panou de Control</p>
                        <h2>Configurează Straturile de Date</h2>
                        <p class="section-lead">
                            Modificarea criteriilor interoghează automat API-ul REST în fundal și recalculează
                            dimensiunile markerilor de pe hartă.
                        </p>
                    </div>

                    <form id="map-filters-form" class="hero-panel-card" style="margin-bottom: 40px;" autocomplete="off">
                        <div class="hero-panel-grid"
                            style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <div class="form-field">
                                <label
                                    style="display: block; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">An
                                    Analiză</label>
                                <select id="map-filter-year" name="year" class="select-custom">
                                    <option value="">Selectare an</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label
                                    style="display: block; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Tip
                                    Combustibil</label>
                                <select id="map-filter-fuel-type" name="fuel_type" class="select-custom">
                                    <option value="">Toate tipurile de combustibil</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label
                                    style="display: block; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Categorie
                                    Națională</label>
                                <select id="map-filter-national-category" name="national_category"
                                    class="select-custom">
                                    <option value="">Toate categoriile</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary" id="reset-map-filters">Resetare</button>
                            <button type="submit" class="btn btn-primary">Aplică Filtrele</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="section" style="padding-top: 0;">
                <div class="container map-layout">
                    <div class="hero-panel-card"
                        style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                            <div>
                                <p class="panel-eyebrow">Reprezentare Spațială</p>
                                <h3 style="font-size: 1.3rem;">Hartă Proporțională pe Județe</h3>
                            </div>
                            <span class="hero-kicker" style="margin-bottom: 0;">Leaflet Engine</span>
                        </div>

                        <div id="map-canvas" class="map-canvas-container">
                            <div class="map-loader">Se inițializează motorul cartografic...</div>
                        </div>

                        <div class="map-legend-box">
                            <p
                                style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px; font-weight: 500;">
                                💡 Culoarea cercului reprezintă <strong>marca predominantă</strong> din acel județ.
                                Dimensiunea cercului reflectă <strong>volumul total</strong> al parcului auto.
                            </p>
                        </div>
                    </div>

                    <aside class="map-sidebar">
                        <article class="hero-panel-card" style="padding: 24px;">
                            <p class="panel-eyebrow">Filtre Active</p>
                            <h3 style="font-size: 1.1rem; margin-bottom: 12px;">Rezumat Selecție</h3>
                            <div id="map-selection-summary" class="meta-pill"
                                style="width: 100%; justify-content: center; font-weight: 600; color: var(--color-accent);">
                                Se încarcă...
                            </div>
                        </article>

                        <article class="hero-panel-card" style="padding: 24px;">
                            <p class="panel-eyebrow" style="color: var(--color-gradient-end);">Focus Regional</p>
                            <h3 style="font-size: 1.2rem; margin-bottom: 16px;" id="selected-county-name">Niciun județ
                                selectat</h3>

                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div
                                    style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                                    <span style="color: var(--text-muted); font-size: 0.9rem;">Cod administrativ:</span>
                                    <span id="selected-county-code" style="font-weight: 600;">-</span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                                    <span style="color: var(--text-muted); font-size: 0.9rem;">An raportare:</span>
                                    <span id="selected-county-year"
                                        style="font-weight: 600; color: var(--color-accent);"><?php echo htmlspecialchars((string) $defaultYear); ?></span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                                    <span style="color: var(--text-muted); font-size: 0.9rem;">Total
                                        Autovehicule:</span>
                                    <span id="selected-county-total" style="font-weight: 700; color: #fff;">-</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-bottom: 4px;">
                                    <span style="color: var(--text-muted); font-size: 0.9rem;">Marcă
                                        Predominantă:</span>
                                    <span id="selected-county-brand" class="brand-badge-inline">-</span>
                                </div>
                            </div>
                        </article>

                        <div class="hero-panel-grid" style="grid-template-columns: 1fr; gap: 12px;">
                            <a class="btn btn-secondary" style="font-size: 0.9rem; padding: 12px;"
                                href="dashboard.php">📊 Mergi la Dashboard</a>
                            <a class="btn btn-secondary" style="font-size: 0.9rem; padding: 12px;"
                                href="search-view.php">🔍 Deschide Filtrare Avansată</a>
                        </div>
                    </aside>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Map Engine</span></p>
                    <p class="footer-text">Modul cartografic asincron bazat pe hărți tematice OpenStreetMap comerciale.
                    </p>
                </div>
                <div class="footer-meta">
                    <span>Orizont: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js?cachebust=1"></script>
    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script>
        window.APP_DEFAULT_YEAR = <?php echo (int) $defaultYear; ?>;
        window.APP_MIN_YEAR = <?php echo (int) $minYear; ?>;
        window.APP_MAX_YEAR = <?php echo (int) $maxYear; ?>;
    </script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/map.js"></script>
</body>

</html>