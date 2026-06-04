<?php

declare(strict_types=1);

$config = require __DIR__ . '/../app/config/config.php';

if (isset($config['app_name'])) {
    $appName = $config['app_name'];
} else {
    $appName = 'Pax';
}

// Extragerea dinamică a limitelor de ani din fișierul global de configurare
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
    <title><?php echo htmlspecialchars($appName); ?> - Despre</title>
    <link rel="stylesheet" href="assets/css/main.css">
    
    <style>
        /* Ajustări de layout specifice pentru pagina Despre */
        .about-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 28px;
            margin-top: 24px;
        }

        @media (max-width: 992px) {
            .about-content-grid {
                grid-template-columns: 1fr;
            }
        }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .tech-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.12);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #f8fafc;
        }

        .tech-item.highlight {
            border-color: rgba(56, 189, 248, 0.3);
            color: var(--accent, #38bdf8);
        }

        .features-bullet-list {
            list-style: none;
            padding: 0;
            margin: 14px 0 0 0;
        }

        .features-bullet-list li {
            position: relative;
            padding-left: 24px;
            margin-bottom: 10px;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .features-bullet-list li::before {
            content: "✦";
            position: absolute;
            left: 0;
            top: 0;
            color: var(--accent, #38bdf8);
        }
    </style>
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
                    <a href="index.php" class="nav-link">Acasă</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="map-view.php" class="nav-link">Hartă</a>
                    <a href="search-view.php" class="nav-link">Căutare</a>
                    <a href="compare.php" class="nav-link">Comparații</a>
                    <a href="about.php" class="nav-link active">Despre</a>
                </nav>
            </div>
        </header>

        <main class="section">
            <div class="container">
                <div class="section-heading">
                    <p class="section-kicker">Informații Proiect</p>
                    <h2>Despre Documentație</h2>
                    <p class="section-lead">Aici găsești detaliile legate de scopul aplicației și tehnologiile implementate.</p>
                </div>

                <div class="about-content-grid">
                    
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        
                        <div class="hero-panel-card" style="padding: 24px;">
                            <h3 style="font-size: 1.25rem; margin-bottom: 12px; color: #f8fafc;">Scopul aplicației</h3>
                            <p style="color: #94a3b8; line-height: 1.6; margin: 0;">
                                Această platformă a fost creată pentru a oferi o interfață intuitivă și modernă de explorare a datelor statistice privind parcul auto din România. Proiectul permite vizualizarea distribuției autovehiculelor pe județe, mărci și tipuri de combustibil în intervalul de timp <strong><?php echo $minYear; ?> - <?php echo $maxYear; ?></strong>, transformând fișiere brute de date în grafice și hărți tematice interactive.
                            </p>
                        </div>

                        <div class="hero-panel-card" style="padding: 24px;">
                            <h3 style="font-size: 1.25rem; margin-bottom: 12px; color: #f8fafc;">Funcționalități cheie</h3>
                            <ul class="features-bullet-list">
                                <li>Dashboard analitic cu topuri de mărci și distribuția tipurilor de combustibili.</li>
                                <li>Hartă tematică interactivă bazată pe marcaje geografice pentru fiecare județ.</li>
                                <li>Căutare avansată cu filtre multiple și paginare eficientă a seturilor de date.</li>
                                <li>Comparație în timp real între performanțele și volumele a două mărci auto diferite.</li>
                                <li>Statistici și topuri optimizate generate asincron direct la nivelul clientului.</li>
                            </ul>
                        </div>

                        <div class="hero-panel-card" style="padding: 24px;">
                            <h3 style="font-size: 1.25rem; margin-bottom: 12px; color: #f8fafc;">Tehnologii utilizate</h3>
                            <p style="color: #94a3b8; line-height: 1.6; margin: 0;">
                                Proiectul este construit pe o arhitectură modernă de tip **decoupled** (separată), unde interfața grafică comunică exclusiv prin servicii web asincrone cu serverul de date:
                            </p>
                            <div class="tech-list">
                                <span class="tech-item highlight">PHP 8 (Strict Types)</span>
                                <span class="tech-item highlight">REST API (JSON Architecture)</span>
                                <span class="tech-item">Vanilla JavaScript (ES6+)</span>
                                <span class="tech-item">Asynchronous Fetch API</span>
                                <span class="tech-item">Leaflet.js (Hărți)</span>
                                <span class="tech-item">CSS Custom Variables</span>
                                <span class="tech-item">SQLite Database Layer</span>
                            </div>
                        </div>

                    </div>

                    <div>
                        <div class="hero-panel-card" style="padding: 20px; position: sticky; top: 24px;">
                            <h3 style="font-size: 1rem; margin-bottom: 16px; color: #f8fafc; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(148,163,184,0.12); padding-bottom: 8px;">🖥️ Status Mediu</h3>
                            
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                    <span style="color: #94a3b8;">Aplicație:</span>
                                    <span style="color: var(--accent); font-weight: 600;"><?php echo htmlspecialchars($appName); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                    <span style="color: #94a3b8;">Mediu rulare:</span>
                                    <span style="color: #22c55e; font-weight: 600;">Localhost</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                    <span style="color: #94a3b8;">Interval Date:</span>
                                    <span style="color: #f8fafc; font-weight: 600;"><?php echo $minYear; ?> - <?php echo $maxYear; ?></span>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding: 10px; background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.15); border-radius: 8px; text-align: center;">
                                <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">Toate modulele platformei sunt acum complet configurate și active.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="site-footer" style="margin-top: 4rem;">
            <div class="container footer-content">
                <div>
                    <p class="footer-brand"><?php echo htmlspecialchars($appName); ?> <span>Info</span></p>
                    <p class="footer-text">Pagina Despre oferă context asupra funcțiilor și a tehnologiilor folosite în proiect.</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>