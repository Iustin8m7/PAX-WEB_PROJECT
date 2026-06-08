<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/services/AdminService.php';

startAdminSession();
requireAdminAuth();

$adminService = new AdminService();
$settingsData = $adminService->getSettingsData();

$config = require __DIR__ . '/../../app/config/config.php';
$appName = isset($config['app_name']) ? (string) $config['app_name'] : 'Pax';

$adminUsername = getAdminUsername();

$appConfig = isset($settingsData['app']) && is_array($settingsData['app']) ? $settingsData['app'] : [];
$pathsConfig = isset($settingsData['paths']) && is_array($settingsData['paths']) ? $settingsData['paths'] : [];
$databaseConfig = isset($settingsData['database']) && is_array($settingsData['database']) ? $settingsData['database'] : [];
$adminConfig = isset($settingsData['admin']) && is_array($settingsData['admin']) ? $settingsData['admin'] : [];

$debugEnabled = isset($settingsData['debug']) ? (bool) $settingsData['debug'] : false;

$projectRoot = isset($pathsConfig['project_root']) ? (string) $pathsConfig['project_root'] : '';
$dbPath = isset($databaseConfig['path']) ? (string) $databaseConfig['path'] : '';
$logsPath = isset($pathsConfig['logs']) ? (string) $pathsConfig['logs'] : '';
$geojsonPath = isset($pathsConfig['geojson']) ? (string) $pathsConfig['geojson'] : '';

$defaultYear = isset($appConfig['default_year']) ? (int) $appConfig['default_year'] : 0;
$minYear = isset($appConfig['min_year']) ? (int) $appConfig['min_year'] : 0;
$maxYear = isset($appConfig['max_year']) ? (int) $appConfig['max_year'] : 0;
$defaultPageSize = isset($appConfig['default_page_size']) ? (int) $appConfig['default_page_size'] : 0;
$maxPageSize = isset($appConfig['max_page_size']) ? (int) $appConfig['max_page_size'] : 0;

$essentialFiles = [
    'Bază de date SQLite' => $dbPath,
    'Director loguri' => $logsPath,
    'Fișier GeoJSON' => $geojsonPath,
    'Director rădăcină proiect' => $projectRoot,
];

function adminStatusExists(string $path): bool
{
    if ($path === '') {
        return false;
    }

    return file_exists($path);
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Admin Setări</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-main: #020617;
            --bg-surface: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.92);
            --border-soft: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --accent: #60a5fa;
            --accent-strong: #3b82f6;
            --success-bg: rgba(34, 197, 94, 0.12);
            --success-border: rgba(34, 197, 94, 0.25);
            --success-text: #bbf7d0;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: rgba(239, 68, 68, 0.25);
            --danger-text: #fecaca;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top, rgba(59, 130, 246, 0.12), transparent 32%),
                linear-gradient(180deg, #020617 0%, #0f172a 50%, #111827 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .admin-header {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(12px);
            background: rgba(2, 6, 23, 0.78);
            border-bottom: 1px solid var(--border-soft);
        }

        .admin-header-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .admin-badge {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: white;
            font-weight: 800;
        }

        .admin-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .admin-nav a {
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .admin-nav a.active {
            color: var(--text-main);
            background: rgba(96, 165, 250, 0.14);
            border-color: rgba(96, 165, 250, 0.24);
        }

        .admin-main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 24px 48px;
        }

        .hero-card,
        .panel-card,
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 26px;
            padding: 28px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
        }

        .hero-card {
            margin-bottom: 24px;
        }

        .hero-kicker {
            margin: 0 0 10px;
            color: var(--accent);
            font-size: 0.86rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-title {
            margin: 0 0 12px;
            font-size: 1.95rem;
            line-height: 1.15;
        }

        .hero-text {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 920px;
        }

        .section {
            margin-top: 24px;
        }

        .section-heading {
            margin-bottom: 16px;
        }

        .section-heading h2 {
            margin: 0 0 8px;
            font-size: 1.35rem;
        }

        .section-heading p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.65;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .stat-label {
            display: block;
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-meta {
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.55;
        }

        .grid-two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .info-list {
            display: grid;
            gap: 12px;
        }

        .info-row {
            display: grid;
            gap: 4px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.03);
        }

        .info-key {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .info-value {
            color: var(--text-main);
            font-size: 0.96rem;
            word-break: break-word;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .status-pill.ok {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .status-pill.error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }

        .table-shell {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid var(--border-soft);
            background: rgba(255, 255, 255, 0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        th,
        td {
            padding: 14px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            vertical-align: top;
        }

        th {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 980px) {
            .grid-two {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 1.65rem;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-header-inner">
            <div class="admin-brand">
                <span class="admin-badge">A</span>
                <span><?php echo htmlspecialchars($appName); ?> Admin</span>
            </div>

            <nav class="admin-nav" aria-label="Navigație admin">
                <a href="/admin/index.php">Dashboard</a>
                <a href="/admin/import.php">Importuri</a>
                <a href="/admin/logs.php">Loguri</a>
                <a href="/admin/settings.php" class="active">Setări</a>
                <a href="/index.php">Site public</a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <section class="hero-card">
            <p class="hero-kicker">Setări și informații aplicație</p>
            <h1 class="hero-title">Configurația curentă și statusul fișierelor esențiale</h1>
            <p class="hero-text">
                Această zonă afișează datele tehnice relevante pentru administrarea aplicației: parametrii principali,
                căile folosite de sistem, intervalul de ani activ, starea modului debug și existența fișierelor
                esențiale necesare funcționării corecte a proiectului.
            </p>
        </section>

        <section class="section">
            <div class="section-heading">
                <h2>Indicatori rapizi</h2>
                <p>Rezumat tehnic pentru mediul curent de execuție.</p>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Administrator autentificat</span>
                    <div class="stat-value"><?php echo htmlspecialchars((string) ($adminUsername ?? 'admin')); ?></div>
                    <div class="stat-meta">Utilizatorul activ din sesiunea administrativă curentă.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Debug</span>
                    <div class="stat-value">
                        <?php if ($debugEnabled): ?>
                            <span class="status-pill ok">Activ</span>
                        <?php else: ?>
                            <span class="status-pill error">Inactiv</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-meta">Statusul opțiunii debug din configurația aplicației.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">An implicit</span>
                    <div class="stat-value"><?php echo htmlspecialchars((string) $defaultYear); ?></div>
                    <div class="stat-meta">Anul implicit folosit de aplicație în interfața publică.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Interval ani</span>
                    <div class="stat-value"><?php echo htmlspecialchars((string) $minYear); ?> - <?php echo htmlspecialchars((string) $maxYear); ?></div>
                    <div class="stat-meta">Limitele minime și maxime pentru filtrarea și validarea anilor.</div>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="grid-two">
                <article class="panel-card">
                    <div class="section-heading">
                        <h2>Parametri aplicație</h2>
                        <p>Setările principale preluate din configurația curentă.</p>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-key">Nume aplicație</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) ($settingsData['app_name'] ?? $appName)); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Username admin configurat</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) ($adminConfig['username'] ?? 'admin')); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Dimensiune implicită paginare</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $defaultPageSize); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Dimensiune maximă paginare</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $maxPageSize); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Driver bază de date</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) ($databaseConfig['driver'] ?? 'sqlite')); ?></span>
                        </div>
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-heading">
                        <h2>Căi importante</h2>
                        <p>Locațiile esențiale folosite de aplicație.</p>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-key">Project root</span>
                            <span class="info-value"><?php echo htmlspecialchars($projectRoot !== '' ? $projectRoot : 'Nedefinit'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Bază de date</span>
                            <span class="info-value"><?php echo htmlspecialchars($dbPath !== '' ? $dbPath : 'Nedefinit'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Director loguri</span>
                            <span class="info-value"><?php echo htmlspecialchars($logsPath !== '' ? $logsPath : 'Nedefinit'); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Fișier GeoJSON</span>
                            <span class="info-value"><?php echo htmlspecialchars($geojsonPath !== '' ? $geojsonPath : 'Nedefinit'); ?></span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="section">
            <article class="panel-card">
                <div class="section-heading">
                    <h2>Status fișiere esențiale</h2>
                    <p>Verificare rapidă a existenței fișierelor și directoarelor importante.</p>
                </div>

                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Resursă</th>
                                <th>Cale</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($essentialFiles as $label => $path): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($label); ?></td>
                                    <td><?php echo htmlspecialchars($path !== '' ? $path : 'Nedefinit'); ?></td>
                                    <td>
                                        <?php if (adminStatusExists($path)): ?>
                                            <span class="status-pill ok">Disponibil</span>
                                        <?php else: ?>
                                            <span class="status-pill error">Lipsește / inaccesibil</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </main>
</body>
</html>