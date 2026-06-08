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

if (isset($config['app']['default_page_size'])) {
    $defaultPageSize = $config['app']['default_page_size'];
} else {
    $defaultPageSize = 25;
}

if (isset($config['app']['max_page_size'])) {
    $maxPageSize = $config['app']['max_page_size'];
} else {
    $maxPageSize = 100;
}

?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Căutare multi-criterială</title>
    <meta name="description"
        content="Pagină de căutare multi-criterială pentru explorarea detaliată a datelor despre parcul auto din România.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/search.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body>
    <div class="page-shell search-page-shell">
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

        <main class="search-main">
            <section class="search-hero-section">
                <div class="container search-hero-grid">
                    <div class="search-hero-content">
                        <p class="section-kicker">Explorare detaliată a datelor</p>
                        <h1>Căutare multi-criterială pentru înregistrările din baza de date</h1>
                        <p class="search-lead">
                            Explorează înregistrările după an, județ, categorie națională, categorie comunitară,
                            combustibil, marcă sau model comercial și consultă rezultatele într-un tabel pregătit
                            pentru sortare, paginare și analiză punctuală.
                        </p>

                        <div class="search-hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="map-view.php">Acces către hartă</a>
                        </div>
                    </div>

                    <aside class="search-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">An implicit</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $defaultYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Paginare implicită</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $defaultPageSize); ?> rezultate</span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Limită maximă</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $maxPageSize); ?> rezultate</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="search-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Panou de filtrare</p>
                        <h2>Configurarea interogării</h2>
                        <p class="section-lead">
                            Selectarea criteriilor actualizează asincron rezultatele și rezumatul curent al căutării.
                        </p>
                    </div>

                    <form id="search-filters-form" class="filters-panel" autocomplete="off">
                        <input type="hidden" name="page" value="1">

                        <div class="filters-grid search-filters-grid">
                            <div class="form-field">
                                <label for="search-filter-year">An</label>
                                <select id="search-filter-year" name="year">
                                    <option value="">Toți anii disponibili</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-county">Județ</label>
                                <select id="search-filter-county" name="county_code">
                                    <option value="">Toate județele</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-national-category">Categorie națională</label>
                                <select id="search-filter-national-category" name="national_category">
                                    <option value="">Toate categoriile</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-community-category">Categorie comunitară</label>
                                <select id="search-filter-community-category" name="community_category">
                                    <option value="">Toate categoriile comunitare</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-fuel-type">Combustibil</label>
                                <select id="search-filter-fuel-type" name="fuel_type">
                                    <option value="">Toate tipurile de combustibil</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-brand">Marcă</label>
                                <select id="search-filter-brand" name="brand">
                                    <option value="">Toate mărcile</option>
                                </select>
                            </div>

                            <div class="form-field form-field-wide">
                                <label for="search-filter-model">Model comercial</label>
                                <input
                                    type="text"
                                    id="search-filter-model"
                                    name="model"
                                    placeholder="Exemplu: SPRINTER, DAILY, LOGAN">
                            </div>

                            <div class="form-field">
                                <label for="search-sort-by">Sortare</label>
                                <select id="search-sort-by" name="sort_by">
                                    <option value="vehicle_count">Total vehicule</option>
                                    <option value="year">An</option>
                                    <option value="county_code">Cod județ</option>
                                    <option value="county_name">Județ</option>
                                    <option value="national_category">Categorie națională</option>
                                    <option value="community_category">Categorie comunitară</option>
                                    <option value="brand_name">Marcă</option>
                                    <option value="model_description">Model comercial</option>
                                    <option value="fuel_type">Combustibil</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-sort-order">Ordine</label>
                                <select id="search-sort-order" name="sort_order">
                                    <option value="desc">Descrescător</option>
                                    <option value="asc">Crescător</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="search-filter-limit">Rezultate / pagină</label>
                                <select id="search-filter-limit" name="limit">
                                    <option value="10" <?php echo $defaultPageSize === 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="25" <?php echo $defaultPageSize === 25 ? 'selected' : ''; ?>>25</option>
                                    <option value="50" <?php echo $defaultPageSize === 50 ? 'selected' : ''; ?>>50</option>
                                    <option value="100" <?php echo $defaultPageSize === 100 ? 'selected' : ''; ?>>100</option>
                                </select>
                            </div>
                        </div>

                        <div class="filters-actions">
                            <button type="submit" class="btn btn-primary">Aplicare filtre</button>
                            <button type="button" class="btn btn-secondary" id="reset-search-filters">Resetare filtre</button>
                            <a
                                href="api/export.php?resource=search&format=csv&year=<?php echo urlencode((string) $defaultYear); ?>"
                                class="btn btn-secondary"
                                id="search-export-csv">
                                Export CSV
                            </a>
                        </div>
                    </form>

                    <div class="dashboard-status-bar search-status-bar">
                        <div class="status-pill">
                            <span class="status-label">Context curent</span>
                            <span class="status-value" id="search-selection-summary">Se pregătește interogarea...</span>
                        </div>

                        <div class="status-pill">
                            <span class="status-label">Stare căutare</span>
                            <span class="status-value" id="search-loading-state">Pregătit pentru încărcare</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="search-section">
                <div class="container">
                    <div class="search-results-card">
                        <div class="search-results-header">
                            <div>
                                <p class="chart-kicker">Rezultate interogare</p>
                                <h2>Tabel de rezultate</h2>
                                <p class="chart-caption">
                                    Exportul CSV descarcă setul filtrat curent, folosind criteriile active din formular.
                                </p>
                            </div>

                            <div class="results-meta">
                                <div class="meta-stat">
                                    <span class="meta-stat-label">Total rezultate</span>
                                    <strong class="meta-stat-value" id="search-total-results">-</strong>
                                </div>

                                <div class="meta-stat">
                                    <span class="meta-stat-label">Pagina curentă</span>
                                    <strong class="meta-stat-value" id="search-current-page">1</strong>
                                </div>

                                <div class="meta-stat">
                                    <span class="meta-stat-label">Total pagini</span>
                                    <strong class="meta-stat-value" id="search-total-pages">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="search-results-body">
                            <div id="search-empty-state" class="search-empty-state" hidden>
                                Nicio înregistrare nu corespunde criteriilor selectate.
                            </div>

                            <div class="table-shell">
                                <table class="results-table" id="search-results-table">
                                    <thead>
                                        <tr>
                                            <th data-sort-by="year" class="sortable">An</th>
                                            <th data-sort-by="county_code" class="sortable">Cod județ</th>
                                            <th data-sort-by="county_name" class="sortable">Județ</th>
                                            <th data-sort-by="national_category" class="sortable">Categorie națională</th>
                                            <th data-sort-by="community_category" class="sortable">Categorie comunitară</th>
                                            <th data-sort-by="brand_name" class="sortable">Marcă</th>
                                            <th data-sort-by="model_description" class="sortable">Model comercial</th>
                                            <th data-sort-by="fuel_type" class="sortable">Combustibil</th>
                                            <th data-sort-by="vehicle_count" class="sortable">Total vehicule</th>
                                        </tr>
                                    </thead>
                                    <tbody id="search-results-body">
                                        <tr>
                                            <td colspan="9">Rezultatele căutării vor fi afișate aici.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-bar">
                                <button type="button" class="btn btn-secondary" id="search-prev-page">Pagina anterioară</button>
                                <span class="pagination-info" id="search-pagination-info">Pagina 1 din 1</span>
                                <button type="button" class="btn btn-secondary" id="search-next-page">Pagina următoare</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="search-section search-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div class="insight-content">
                            <p class="section-kicker">Interpretare și continuare</p>
                            <h2>Explorare detaliată a setului activ de date</h2>
                            <p id="search-context-summary" class="section-lead">
                                Căutarea va actualiza această zonă cu un rezumat al selecției active, al rezultatelor
                                obținute și al contextului curent de analiză.
                            </p>
                        </div>

                        <div class="insight-actions">
                            <a class="btn btn-primary" href="dashboard.php">Înapoi la dashboard</a>
                            <a class="btn btn-secondary" href="compare.php">Acces către comparații</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Search Explorer</span></p>
                    <p class="footer-text">Modul de căutare multi-criterială pentru analiza detaliată a datelor despre parcul auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioadă analizată: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Filtrare asincronă + tabel paginat + sortare controlată</span>
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
        window.APP_DEFAULT_PAGE_SIZE = <?php echo json_encode((int) $defaultPageSize); ?>;
        window.APP_MAX_PAGE_SIZE = <?php echo json_encode((int) $maxPageSize); ?>;
    </script>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/utils.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="assets/js/search.js"></script>
</body>

</html>