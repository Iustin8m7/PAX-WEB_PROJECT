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
    <title><?php echo htmlspecialchars($appName); ?> - Despre aplicație</title>
    <meta name="description"
        content="Pagină informativă despre proiectul Pax, sursa datelor, tehnologiile utilizate și funcționalitățile principale.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>

<body>
    <div class="page-shell about-page-shell">
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
                    <a href="compare.php" class="nav-link">Comparații</a>
                    <a href="about.php" class="nav-link active">Despre</a>
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

        <main class="about-main">
            <section class="about-hero-section">
                <div class="container about-hero-grid">
                    <div class="about-hero-content">
                        <p class="section-kicker">Despre proiect</p>
                        <h1>Platformă Web pentru analiza parcului auto din România</h1>
                        <p class="about-lead">
                            <?php echo htmlspecialchars($appName); ?> este o aplicație Web orientată pe analiză de date,
                            concepută pentru explorarea, compararea și vizualizarea informațiilor publice privind
                            parcul auto din România, pentru perioada
                            <strong><?php echo htmlspecialchars((string) $minYear); ?> -
                                <?php echo htmlspecialchars((string) $maxYear); ?></strong>.
                        </p>

                        <div class="about-hero-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="index.php">Înapoi la pagina principală</a>
                        </div>
                    </div>

                    <aside class="about-hero-panel">
                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Tip aplicație</span>
                            <span class="hero-stat-value">Data exploration platform</span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Perioadă analizată</span>
                            <span class="hero-stat-value"><?php echo htmlspecialchars((string) $minYear); ?> -
                                <?php echo htmlspecialchars((string) $maxYear); ?></span>
                        </div>

                        <div class="hero-stat-card">
                            <span class="hero-stat-label">Arhitectură</span>
                            <span class="hero-stat-value">PHP + SQLite + Ajax + servicii Web REST</span>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="about-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Scopul aplicației</p>
                        <h2>De ce a fost dezvoltat proiectul</h2>
                    </div>

                    <div class="about-content-grid">
                        <article class="about-card">
                            <h3>Obiectiv principal</h3>
                            <p>
                                Aplicația oferă un mod coerent și interactiv de explorare a datelor publice privind
                                parcul auto din România, într-o formă mai accesibilă decât tabelele brute sau fișierele
                                CSV individuale.
                            </p>
                        </article>

                        <article class="about-card">
                            <h3>Valoare practică</h3>
                            <p>
                                Platforma transformă datele brute în indicatori sintetici, grafice, tabele filtrabile și
                                reprezentări cartografice, permițând analiza rapidă a evoluției, distribuției și
                                diferențelor teritoriale.
                            </p>
                        </article>

                        <article class="about-card">
                            <h3>Direcție funcțională</h3>
                            <p>
                                Proiectul este orientat pe vizualizare, căutare adecvată, comparație multi-criterială,
                                explorare geografică și integrarea datelor prin servicii Web asincrone.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <div class="container">
                    <div class="section-heading section-heading-left">
                        <p class="section-kicker">Funcționalități principale</p>
                        <h2>Modulele publice ale aplicației</h2>
                        <p class="section-lead">
                            Aplicația este structurată în module complementare, fiecare adresând un mod diferit de
                            explorare și interpretare a datelor.
                        </p>
                    </div>

                    <div class="feature-grid">
                        <article class="feature-card feature-card-accent">
                            <div class="feature-icon">01</div>
                            <h3>Dashboard analitic</h3>
                            <p>
                                Explorează indicatorii principali, evoluția pe ani, structura pe combustibil și
                                clasamentele relevante pentru contextul activ.
                            </p>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">02</div>
                            <h3>Hartă interactivă</h3>
                            <p>
                                Explorează distribuția geografică la nivel de județ și evidențiază diferențele
                                teritoriale prin filtre și reprezentare cartografică.
                            </p>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">03</div>
                            <h3>Căutare multi-criterială</h3>
                            <p>
                                Filtrează înregistrările după an, județ, categorie, combustibil, marcă sau model
                                comercial și analizează rezultatele în mod detaliat.
                            </p>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">04</div>
                            <h3>Comparații dedicate</h3>
                            <p>
                                Compară două selecții de date și evidențiază diferențele prin indicatori sintetici,
                                grafice comparative și clasamente paralele.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <div class="container about-two-column-grid">
                    <article class="about-card">
                        <p class="section-kicker">Tehnologii utilizate</p>
                        <h2>Stack tehnic</h2>
                        <ul class="info-list">
                            <li>PHP pentru logica de server și endpoint-uri API</li>
                            <li>SQLite pentru stocarea și interogarea datelor</li>
                            <li>HTML și CSS pentru interfața publică responsive</li>
                            <li>JavaScript pentru interacțiuni asincrone și consumul API-ului</li>
                            <li>Leaflet pentru componenta cartografică</li>
                            <li>JSON și CSV pentru import/export și schimb de date</li>
                        </ul>
                    </article>

                    <article class="about-card">
                        <p class="section-kicker">Arhitectură</p>
                        <h2>Organizarea aplicației</h2>
                        <p>
                            Aplicația este construită în jurul unei arhitecturi bazate pe servicii Web. Partea de server
                            expune endpoint-uri REST, iar interfața publică consumă datele prin apeluri Ajax, fără
                            reîncărcarea completă a paginilor.
                        </p>
                        <p>
                            Structura proiectului separă configurarea, accesul la baza de date, repository-urile,
                            endpoint-urile API și componentele publice ale interfeței.
                        </p>
                    </article>
                </div>
            </section>

            <section class="about-section">
                <div class="container about-two-column-grid">
                    <article class="about-card">
                        <p class="section-kicker">Sursa datelor</p>
                        <h2>Date publice utilizate în proiect</h2>
                        <p>
                            Datele procesate provin din seturi publice referitoare la parcul auto din România și au fost
                            importate, curățate, normalizate parțial și organizate într-o bază de date relațională.
                        </p>
                        <p>
                            Structura internă a datelor permite analiza pe:
                        </p>
                        <ul class="info-list">
                            <li>ani și evoluție temporală</li>
                            <li>județe și distribuție geografică</li>
                            <li>categorii naționale și comunitare</li>
                            <li>tipuri de combustibil</li>
                            <li>mărci și modele comerciale</li>
                        </ul>
                    </article>

                    <article class="about-card">
                        <p class="section-kicker">Prelucrare date</p>
                        <h2>Fluxul de integrare</h2>
                        <p>
                            Fișierele sursă au fost analizate, validate și importate într-o structură internă adaptată
                            cerințelor aplicației. Importul păstrează trasabilitatea fișierelor, anul de proveniență și
                            loturile de încărcare, facilitând verificarea și depanarea datelor.
                        </p>
                        <p>
                            La nivelul aplicației, datele sunt accesate prin repository-uri specializate și sunt expuse
                            prin endpoint-uri API dedicate filtrării, statisticilor, căutării și reprezentării pe hartă.
                        </p>
                    </article>
                </div>
            </section>

            <section class="about-section about-section-final">
                <div class="container">
                    <div class="insight-box">
                        <div class="insight-content">
                            <p class="section-kicker">Concluzie</p>
                            <h2>O aplicație orientată pe explorare, comparație și vizualizare</h2>
                            <p class="section-lead">
                                <?php echo htmlspecialchars($appName); ?> urmărește să transforme un set de date publice
                                complex într-o experiență Web clară, interactivă și utilă pentru analiză. Proiectul
                                combină date structurate, servicii Web, filtrare asincronă și componente vizuale moderne
                                într-o platformă coerentă de explorare a parcului auto din România.
                            </p>
                        </div>

                        <div class="insight-actions">
                            <a class="btn btn-primary" href="dashboard.php">Acces către dashboard</a>
                            <a class="btn btn-secondary" href="map-view.php">Acces către hartă</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>About</span></p>
                    <p class="footer-text">Pagină informativă dedicată proiectului, arhitecturii și sursei datelor.</p>
                </div>

                <div class="footer-meta">
                    <span>Perioadă analizată: <?php echo htmlspecialchars((string) $minYear); ?> -
                        <?php echo htmlspecialchars((string) $maxYear); ?></span>
                    <span>Proiect Web bazat pe date publice și servicii REST</span>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>