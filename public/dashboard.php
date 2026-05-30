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
    <title><?php echo htmlspecialchars($appName); ?> - Dashboard</title>
    <meta name="description" content="Dashboard analitic pentru explorarea și vizualizarea datelor despre parcul auto din România.">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
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
                    <a href="index.php">Acasă</a>
                    <a href="dashboard.php" class="active">Dashboard</a>
                    <a href="map-view.php">Hartă</a>
                    <a href="search-view.php">Căutare</a>
                    <a href="compare.php">Comparații</a>
                    <a href="about.php">Despre</a>
                </nav>
            </div>
        </header>

        <main class="dashboard-main">
            <section class="dashboard-hero">
                <div class="container dashboard-hero-grid">
                    <div class="dashboard-hero-content">
                        <p class="section-kicker">Zona principală de analiză</p>
                        <h1>Dashboard analitic pentru parcul auto din România</h1>
                        <p class="dashboard-lead">
                            Explorează distribuția indicatorilor principali, vizualizează tendințele pe ani,
                            analizează topurile relevante și corelează rezultatele statistice cu reprezentarea geografică.
                        </p>

                        <div class="dashboard-hero-actions">
                            <a class="btn btn-primary" href="map-view.php">Acces către harta interactivă</a>
                            <a class="btn btn-secondary" href="search-view.php">Acces către căutare avansată</a>
                        </div>
                    </div>

                    <aside class="dashboard-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">An implicit</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string)$defaultYear); ?></span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Interval disponibil</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Tip analiză</span>
                            <span class="hero-stat-value">Statistici + grafice + preview hartă</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Filtre globale</p>
                        <h2>Controlul setului de date analizat</h2>
                        <p class="section-lead">
                            Selectarea criteriilor de filtrare actualizează asincron indicatorii, graficele,
                            topurile și sumarul afișat în dashboard.
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
                        </div>
                    </form>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Indicatori sintetici</p>
                        <h2>Overview al selecției curente</h2>
                    </div>

                    <div class="overview-grid">
                        <article class="overview-card">
                            <span class="overview-label">Total vehicule</span>
                            <strong class="overview-value" id="overview-total-vehicles">-</strong>
                            <span class="overview-meta">valoare agregată pentru selecția curentă</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Județe active</span>
                            <strong class="overview-value" id="overview-counties-count">-</strong>
                            <span class="overview-meta">unități teritoriale cu date disponibile</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Mărci distincte</span>
                            <strong class="overview-value" id="overview-brands-count">-</strong>
                            <span class="overview-meta">mărci identificate în setul selectat</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Tipuri combustibil</span>
                            <strong class="overview-value" id="overview-fuel-types-count">-</strong>
                            <span class="overview-meta">diversitatea energetică a selecției</span>
                        </article>

                        <article class="overview-card">
                            <span class="overview-label">Categorii naționale</span>
                            <strong class="overview-value" id="overview-categories-count">-</strong>
                            <span class="overview-meta">categorii acoperite de selecția curentă</span>
                        </article>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Vizualizări principale</p>
                        <h2>Grafice și distribuții</h2>
                        <p class="section-lead">
                            Datele sunt prezentate în forme multiple pentru a susține explorarea evoluției,
                            a topurilor și a structurii interne a parcului auto.
                        </p>
                    </div>

                    <div class="dashboard-grid">
                        <article class="chart-card chart-card-large">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Evoluție temporală</p>
                                    <h3>Total vehicule pe ani</h3>
                                </div>
                                <span class="chart-badge">statistics.php?view=yearly-totals</span>
                            </div>
                            <div class="chart-placeholder" id="chart-yearly-totals">
                                Graficul pentru evoluția pe ani va fi randat aici.
                            </div>
                        </article>

                        <article class="chart-card">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Topuri</p>
                                    <h3>Top mărci</h3>
                                </div>
                                <span class="chart-badge">statistics.php?view=top-brands</span>
                            </div>
                            <div class="chart-placeholder" id="chart-top-brands">
                                Graficul pentru topul mărcilor va fi randat aici.
                            </div>
                        </article>

                        <article class="chart-card">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Distribuție</p>
                                    <h3>Structură pe combustibil</h3>
                                </div>
                                <span class="chart-badge">statistics.php?view=fuel-distribution</span>
                            </div>
                            <div class="chart-placeholder" id="chart-fuel-distribution">
                                Graficul pentru distribuția pe combustibil va fi randat aici.
                            </div>
                        </article>

                        <article class="chart-card chart-card-wide">
                            <div class="chart-card-header">
                                <div>
                                    <p class="chart-kicker">Structură categorii</p>
                                    <h3>Distribuție pe categorii naționale</h3>
                                </div>
                                <span class="chart-badge">statistics.php?view=category-distribution</span>
                            </div>
                            <div class="chart-placeholder" id="chart-category-distribution">
                                Graficul pentru distribuția pe categorii va fi randat aici.
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
                            <span class="chart-badge">statistics.php?view=county-ranking</span>
                        </div>

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
                    </article>

                    <article class="data-card">
                        <div class="data-card-header">
                            <div>
                                <p class="chart-kicker">Preview geografic</p>
                                <h3>Distribuție pe hartă</h3>
                            </div>
                            <span class="chart-badge">map.php</span>
                        </div>

                        <div class="map-preview" id="dashboard-map-preview">
                            Preview-ul hărții va fi integrat aici.
                        </div>

                        <div class="map-preview-footer">
                            <a class="btn btn-secondary" href="map-view.php">Deschidere hartă completă</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="dashboard-section dashboard-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div>
                            <p class="section-kicker">Context curent</p>
                            <h2>Rezumat al selecției active</h2>
                            <p id="dashboard-selection-summary" class="section-lead">
                                Selectarea filtrelor va actualiza această secțiune cu un sumar al contextului analizat.
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
                    <p class="footer-text">Dashboard public pentru explorarea datelor despre parcul auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Date analizate: <?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                    <span>Interfață publică conectată la servicii Web REST</span>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>