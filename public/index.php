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
    <title><?php echo htmlspecialchars($appName); ?> - Auto Park Web Explorer</title>
    <meta name="description"
        content="Platformă Web pentru analiza, compararea și vizualizarea evoluției parcului auto din România.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
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
                    <a href="map-view.php" class="nav-link">Hartă</a>
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

        <main>
            <section class="hero-section">
                <div class="container hero-grid">
                    <div class="hero-content">
                        <p class="hero-kicker">Platformă Web de analiză a parcului auto</p>

                        <h1 class="hero-title">
                            <?php echo htmlspecialchars($appName); ?>
                            <span class="hero-title-accent">Auto Park Web Explorer</span>
                        </h1>

                        <p class="hero-description">
                            Platformă interactivă pentru explorarea, compararea și vizualizarea datelor publice
                            referitoare la parcul auto din România. Interfața reunește statistici, filtrare
                            multi-criterială, componente cartografice și analize comparative pentru perioada
                            <strong><?php echo htmlspecialchars((string) $minYear); ?> -
                                <?php echo htmlspecialchars((string) $maxYear); ?></strong>.
                        </p>

                        <div class="hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="map-view.php">Acces către harta interactivă</a>
                        </div>

                        <div class="hero-meta">
                            <div class="meta-pill">
                                <span class="meta-label">Perioadă analizată</span>
                                <span class="meta-value"><?php echo htmlspecialchars((string) $minYear); ?> -
                                    <?php echo htmlspecialchars((string) $maxYear); ?></span>
                            </div>
                            <div class="meta-pill">
                                <span class="meta-label">Sursă date</span>
                                <span class="meta-value">Seturi publice oficiale</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-panel">
                        <div class="hero-panel-card hero-panel-card-main">
                            <p class="panel-eyebrow">Arhitectură bazată pe servicii Web</p>
                            <h2>Analiză vizuală, filtrare asincronă și explorare interactivă</h2>
                            <p>
                                Interfața consolidează volume mari de date și oferă un flux coerent de analiză prin
                                dashboard, hartă interactivă, căutare avansată și comparații dedicate.
                            </p>
                        </div>

                        <div class="hero-panel-grid">
                            <div class="hero-mini-card">
                                <span class="mini-value">4</span>
                                <span class="mini-label">module publice</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">REST</span>
                                <span class="mini-label">servicii Web asincrone</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">RO</span>
                                <span class="mini-label">acoperire națională</span>
                            </div>
                            <div class="hero-mini-card">
                                <span class="mini-value">CSV</span>
                                <span class="mini-label">date publice procesate</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section section-features">
                <div class="container">
                    <div class="section-heading">
                        <p class="section-kicker">Modulele aplicației</p>
                        <h2>Zonele principale de analiză</h2>
                        <p class="section-lead">
                            Aplicația este structurată în module complementare, fiecare orientat pe un tip clar
                            de explorare și interpretare a datelor.
                        </p>
                    </div>

                    <div class="feature-grid">
                        <article class="feature-card feature-card-accent">
                            <div class="feature-icon">01</div>
                            <h3>Dashboard analitic</h3>
                            <p>
                                Explorează distribuția indicatorilor principali, evoluția pe ani, topurile relevante
                                și structura generală a datelor disponibile.
                            </p>
                            <a class="feature-link" href="dashboard.php">Deschidere dashboard &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">02</div>
                            <h3>Hartă interactivă</h3>
                            <p>
                                Explorează distribuția geografică la nivel de județ și evidențiază diferențele
                                teritoriale prin filtre și reprezentare cartografică.
                            </p>
                            <a class="feature-link" href="map-view.php">Acces către hartă &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">03</div>
                            <h3>Căutare multi-criterială</h3>
                            <p>
                                Filtrează înregistrările după an, județ, categorie, combustibil, marcă
                                sau model comercial pentru analiza segmentelor relevante.
                            </p>
                            <a class="feature-link" href="search-view.php">Acces către căutare &rarr;</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">04</div>
                            <h3>Comparații dedicate</h3>
                            <p>
                                Compară selecții de date și evidențiază diferențele dintre mărci, perioade
                                sau contexte teritoriale prin rezumate și rezultate comparative.
                            </p>
                            <a class="feature-link" href="compare.php">Acces către comparații &rarr;</a>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section section-showcase">
                <div class="container showcase-grid">
                    <div class="showcase-card">
                        <p class="section-kicker">Despre proiect</p>
                        <h2>Transformarea datelor brute într-o interfață de analiză</h2>
                        <p>
                            <strong><?php echo htmlspecialchars($appName); ?></strong> organizează și expune datele
                            publice privind parcul auto din România într-o formă accesibilă, interactivă și ușor de
                            explorat.
                        </p>
                        <p>
                            Back-end-ul procesează eficient datele și oferă un API REST dedicat, iar interfața publică
                            consumă asincron informațiile pentru o experiență fluidă și coerentă.
                        </p>
                    </div>

                    <div class="showcase-card">
                        <p class="section-kicker">Despre date</p>
                        <h2>Segmente acoperite în analiză</h2>
                        <p>
                            Baza de date acoperă perioada
                            <?php echo htmlspecialchars((string) $minYear); ?> -
                            <?php echo htmlspecialchars((string) $maxYear); ?> și permite analiza pe:
                        </p>
                        <ul class="info-list">
                            <li>cronologie și evoluție anuală</li>
                            <li>distribuție regională pe județe</li>
                            <li>categorii naționale și comunitare</li>
                            <li>tipuri de combustibil</li>
                            <li>mărci și modele comerciale</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="section section-cta">
                <div class="container cta-box">
                    <div class="cta-content">
                        <p class="section-kicker">Acces rapid</p>
                        <h2>Intrare directă către modulele principale</h2>
                        <p>
                            Selectarea modulului potrivit permite trecerea directă către analiză statistică,
                            hartă interactivă, căutare sau comparații.
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
                    <p class="footer-text">Proiect academic pentru analiza, compararea și vizualizarea parcului auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioadă: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Arhitectură: PHP REST API + interfață asincronă</span>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>