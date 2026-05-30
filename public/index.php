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
    <meta name="description" content="Platformă Web pentru explorarea, compararea și vizualizarea datelor publice despre parcul auto din România.">
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
                    <a href="dashboard.php">Dashboard</a>
                    <a href="map-view.php">Hartă</a>
                    <a href="search-view.php">Căutare</a>
                    <a href="compare.php">Comparații</a>
                    <a href="about.php">Despre</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="hero-section">
                <div class="container hero-grid">
                    <div class="hero-content">
                        <p class="hero-kicker">Analiză vizuală a parcului auto din România</p>

                        <h1 class="hero-title">
                            <?php echo htmlspecialchars($appName); ?>
                            <span class="hero-title-accent">Auto Park Web Explorer</span>
                        </h1>

                        <p class="hero-description">
                            Platformă Web pentru explorarea, compararea și vizualizarea interactivă a datelor publice
                            referitoare la parcul auto din România, pentru perioada
                            <?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?>.
                        </p>

                        <div class="hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="map-view.php">Acces către harta interactivă</a>
                        </div>

                        <div class="hero-meta">
                            <div class="meta-pill">
                                <span class="meta-label">Perioada analizată</span>
                                <span class="meta-value"><?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                            </div>
                            <div class="meta-pill">
                                <span class="meta-label">Sursă</span>
                                <span class="meta-value">Date publice oficiale</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-panel">
                        <div class="hero-panel-card hero-panel-card-main">
                            <p class="panel-eyebrow">Funcționalități principale</p>
                            <h2>Explorare, comparare și vizualizare avansată</h2>
                            <p>
                                Interfața reunește statistici, filtre, tabele, grafice și reprezentare cartografică
                                într-un flux coerent de analiză a datelor.
                            </p>
                        </div>

                        <div class="hero-panel-grid">
                            <div class="hero-mini-card">
                                <span class="mini-value">3+</span>
                                <span class="mini-label">tipuri de vizualizare</span>
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
                        <p class="section-kicker">Module publice</p>
                        <h2>Zonele principale ale aplicației</h2>
                        <p class="section-lead">
                            Accesul către principalele funcționalități este organizat în module dedicate,
                            fiecare orientat pe un tip clar de analiză.
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
                            <a class="feature-link" href="dashboard.php">Deschidere dashboard</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">02</div>
                            <h3>Hartă interactivă</h3>
                            <p>
                                Explorează distribuția geografică la nivel de județ și evidențiază diferențele
                                teritoriale prin filtrare după an, combustibil și categorie națională.
                            </p>
                            <a class="feature-link" href="map-view.php">Acces către hartă</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">03</div>
                            <h3>Căutare multi-criterială</h3>
                            <p>
                                Filtrează înregistrările după an, județ, categorie, combustibil, marcă
                                sau model comercial și analizează segmentul relevant de date.
                            </p>
                            <a class="feature-link" href="search-view.php">Acces către căutare</a>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">04</div>
                            <h3>Comparații dedicate</h3>
                            <p>
                                Compară perioade, județe sau segmente de date pentru evidențierea
                                diferențelor, tendințelor și concentrărilor relevante.
                            </p>
                            <a class="feature-link" href="compare.php">Acces către comparații</a>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section section-showcase">
                <div class="container showcase-grid">
                    <div class="showcase-card">
                        <p class="section-kicker">Despre proiect</p>
                        <h2>Aplicație Web orientată pe analiză și explorare de date</h2>
                        <p>
                            <?php echo htmlspecialchars($appName); ?> integrează servicii Web, filtrare asincronă și
                            vizualizări moderne pentru a transforma un set de date publice într-o experiență
                            interactivă de explorare.
                        </p>
                        <p>
                            Arhitectura aplicației este construită în jurul unui API REST realizat în PHP,
                            iar interfața publică consumă datele în mod asincron prin apeluri Ajax.
                        </p>
                    </div>

                    <div class="showcase-card">
                        <p class="section-kicker">Despre date</p>
                        <h2>Seturi publice structurate pentru analiză comparativă</h2>
                        <p>
                            Datele acoperă perioada <?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?>
                            și permit analiza pe:
                        </p>
                        <ul class="info-list">
                            <li>ani și evoluție temporală</li>
                            <li>județe și distribuție geografică</li>
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
                        <p class="section-kicker">Punct de intrare rapid</p>
                        <h2>Acces direct către modulele principale</h2>
                        <p>
                            Selectarea zonei de interes permite trecerea directă către analiza statistică,
                            harta interactivă, căutare sau comparații.
                        </p>
                    </div>

                    <div class="cta-actions">
                        <a class="btn btn-primary" href="dashboard.php">Dashboard</a>
                        <a class="btn btn-secondary" href="map-view.php">Hartă</a>
                        <a class="btn btn-secondary" href="search-view.php">Căutare</a>
                        <a class="btn btn-secondary" href="compare.php">Comparații</a>
                        <a class="btn btn-secondary" href="about.php">Despre aplicație</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?></p>
                    <p class="footer-text">Proiect academic pentru analiza parcului auto din România.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioada analizată: <?php echo htmlspecialchars((string)$minYear); ?> - <?php echo htmlspecialchars((string)$maxYear); ?></span>
                    <span>Interfață publică + servicii Web REST</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>