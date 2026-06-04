<?php

declare(strict_types=1);

$config = require __DIR__ . '/../app/config/config.php';

if (isset($config['app_name'])) {
    $appName = $config['app_name'];
} else {
    $appName = 'Pax';
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
    <title><?php echo htmlspecialchars($appName); ?> - Auto Park Intelligence</title>
    <meta name="description"
        content="Platformă interactivă de business intelligence pentru analiza, compararea și vizualizarea evoluției parcului auto din România.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
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
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="map-view.php" class="nav-link">Hartă Interactivă</a>
                    <a href="search-view.php" class="nav-link">Filtrare Date</a>
                    <a href="compare.php" class="nav-link">Analiză Comparativă</a>
                    <a href="about.php" class="nav-link">Despre</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="hero-section">
                <div class="container hero-grid">
                    <div class="hero-content">
                        <p class="hero-kicker">📊 Smart Data Visualizer</p>

                        <h1 class="hero-title">
                            <?php echo htmlspecialchars($appName); ?>
                            <span class="hero-title-accent">Auto Park Web Explorer</span>
                        </h1>

                        <p class="hero-description">
                            Descoperă radiografia completă a parcului auto din România. O platformă avansată de
                            explorare analitică ce transformă milioane de înregistrări publice în insight-uri vizuale
                            clare, hărți tematice și statistici evolutive pentru perioada
                            <strong><?php echo htmlspecialchars((string) $minYear); ?> -
                                <?php echo htmlspecialchars((string) $maxYear); ?></strong>.
                        </p>

                        <div class="hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Explorează Dashboard-ul</a>
                            <a class="btn btn-secondary" href="map-view.php">Vezi Harta Distribuiției</a>
                        </div>

                        <div class="hero-meta">
                            <div class="meta-pill">
                                <span class="meta-label">Orizont Temporal</span>
                                <span class="meta-value"><?php echo htmlspecialchars((string) $minYear); ?> -
                                    <?php echo htmlspecialchars((string) $maxYear); ?></span>
                            </div>
                            <div class="meta-pill">
                                <span class="meta-label">Sursă Date</span>
                                <span class="meta-value">Seturi Oficiale Open-Data</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-panel">
                        <div class="hero-panel-card hero-panel-card-main">
                            <p class="panel-eyebrow">Arhitectură Performantă</p>
                            <h2>Procesare rapidă și rapoarte intuitive</h2>
                            <p>
                                Interfața consolidează date complexe, permițând izolarea tendințelor macroeconomice,
                                preferințele eco (electrice/hibride) și densitatea auto regională.
                            </p>
                        </div>

                        <div class="hero-panel-grid">
                            <div class="hero-mini-card">
                                <span class="mini-value">4</span>
                                <span class="mini-label">Module Analitice</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">REST</span>
                                <span class="mini-label">API Asincron (Ajax)</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">100%</span>
                                <span class="mini-label">Acoperire Națională</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">Optimized</span>
                                <span class="mini-label">Procesare CSV</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section section-features">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Ecosistemul Aplicației</p>
                        <h2>Zonele principale de analiză</h2>
                        <p class="section-lead">
                            Aplicația este structurată în module specializate pentru a-ți oferi perspective diferite
                            asupra setului de date.
                        </p>
                    </div>

                    <div class="feature-grid">
                        <article class="feature-card feature-card-accent">
                            <div class="feature-icon">📊</div>
                            <h3>Dashboard Analitic</h3>
                            <p>
                                Vizualizează macro-tendințele, evoluția de la an la an, topul mărcilor preferate de
                                români și distribuția pe tipuri de combustibil.
                            </p>
                            <a class="feature-link" href="dashboard.php">Deschide dashboard &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">🗺️</div>
                            <h3>Hartă Interactivă</h3>
                            <p>
                                Filtrează vizual județele României. Descoperă zonele cu cea mai mare densitate auto și
                                mărcile predominante dintr-o singură privire direct pe hartă.
                            </p>
                            <a class="feature-link" href="map-view.php">Acces către hartă &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">🔍</div>
                            <h3>Căutare Multi-Criterială</h3>
                            <p>
                                Sapează adânc în baza de date folosind filtre combinate: an, județ, categorie,
                                combustibil, marcă sau un anumit model comercial.
                            </p>
                            <a class="feature-link" href="search-view.php">Acces către căutare &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">⚔️</div>
                            <h3>Comparații Dedicate</h3>
                            <p>
                                Pune față în față două județe sau perioade diferite de timp pentru a genera grafice
                                comparative și a evidenția anomaliile de creștere.
                            </p>
                            <a class="feature-link" href="compare.php">Acces către comparații &rarr;</a>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section section-showcase">
                <div class="container showcase-grid">
                    <div class="showcase-card">
                        <p class="section-kicker">Inovație Tehnică</p>
                        <h2>Transformăm datele brute în cunoaștere</h2>
                        <p>
                            <strong><?php echo htmlspecialchars($appName); ?></strong> rezolvă problema fișierelor
                            masive de date guvernamentale. În loc să navighezi prin tabele infinite Excel, aplicația
                            noastră compilează totul instant.
                        </p>
                        <p>
                            În timp ce back-end-ul procesează eficient algoritmii statistici în PHP, front-end-ul
                            interoghează datele asincron pentru o experiență fluidă și rapidă, fără reîncărcarea
                            paginii.
                        </p>
                    </div>

                    <div class="showcase-card">
                        <p class="section-kicker">Granularitatea Datelor</p>
                        <h2>Segmente acoperite în analiză</h2>
                        <p>
                            Baza de date cuprinde înregistrări oficiale din perioada
                            <?php echo htmlspecialchars((string) $minYear); ?> -
                            <?php echo htmlspecialchars((string) $maxYear); ?> și permite filtrarea pe:
                        </p>
                        <ul class="info-list">
                            <li>Cronologie (Evoluție anuală și trenduri)</li>
                            <li>Geografie (Distribuție regională pe județe)</li>
                            <li>Clasificare (Categorii naționale și europene)</li>
                            <li>Sursă de energie (Benzină, Diesel, Electrice, Hibrid)</li>
                            <li>Nomenclator producători (Mărci și Modele comerciale)</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="section section-cta">
                <div class="container cta-box">
                    <div class="cta-content">
                        <p class="section-kicker">Navigare Rapidă</p>
                        <h2>Ești gata să începi analiza?</h2>
                        <p>
                            Alege modulul dorit pentru a accesa seturile de date și instrumentele grafice.
                        </p>
                    </div>

                    <div class="cta-actions">
                        <a class="btn btn-primary" href="dashboard.php">Dashboard</a>
                        <a class="btn btn-secondary" href="map-view.php">Hartă</a>
                        <a class="btn btn-secondary" href="search-view.php">Căutare</a>
                        <a class="btn btn-secondary" href="compare.php">Comparații</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Auto Explorer</span></p>
                    <p class="footer-text">Proiect academic avansat dezvoltat pentru analiza parcului auto din România.
                    </p>
                </div>

                <div class="footer-meta">
                    <span>📅 Interval: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>⚡ Arhitectură: PHP REST API + Async UI</span>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>