<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/services/AdminService.php';

startAdminSession();
requireAdminAuth();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logoutAdmin();
    header('Location: /admin/login.php');
    exit;
}

$adminService = new AdminService();
$overview = $adminService->getDashboardOverview();

$config = require __DIR__ . '/../../app/config/config.php';
$appName = isset($config['app_name']) ? (string) $config['app_name'] : 'Pax';

$adminUsername = getAdminUsername();
$vehicleRecordsCount = isset($overview['vehicle_records_count']) ? (int) $overview['vehicle_records_count'] : 0;
$importBatchesCount = isset($overview['import_batches_count']) ? (int) $overview['import_batches_count'] : 0;
$availableYears = isset($overview['available_years']) && is_array($overview['available_years']) ? $overview['available_years'] : [];
$latestBatch = isset($overview['latest_import_batch']) && is_array($overview['latest_import_batch']) ? $overview['latest_import_batch'] : null;
$databasePath = isset($overview['database_path']) ? (string) $overview['database_path'] : '';
$debugEnabled = isset($overview['debug_enabled']) ? (bool) $overview['debug_enabled'] : false;

function formatAdminNumber(int $value): string
{
    return number_format($value, 0, ',', '.');
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Admin Dashboard</title>

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
            --success-text: #bbf7d0;
            --success-border: rgba(34, 197, 94, 0.25);
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

        .admin-shell {
            min-height: 100vh;
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
            max-width: 1240px;
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
            letter-spacing: 0.01em;
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

        .admin-header-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .admin-user-pill {
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--border-soft);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .admin-logout {
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            font-weight: 700;
        }

        .admin-main {
            max-width: 1240px;
            margin: 0 auto;
            padding: 36px 24px 48px;
        }

        .hero-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.30);
            margin-bottom: 28px;
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
            font-size: 2rem;
            line-height: 1.15;
        }

        .hero-text {
            margin: 0;
            color: var(--text-muted);
            max-width: 900px;
            line-height: 1.7;
        }

        .section {
            margin-top: 28px;
        }

        .section-heading {
            margin-bottom: 18px;
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
            gap: 18px;
        }

        .stat-card,
        .panel-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 24px;
        }

        .stat-label {
            display: block;
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-meta {
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 18px;
        }

        .panel-title {
            margin: 0 0 14px;
            font-size: 1.08rem;
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

        .status-ok {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
            font-size: 0.92rem;
            font-weight: 700;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .quick-link-card {
            display: block;
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            padding: 22px;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .quick-link-card:hover {
            transform: translateY(-2px);
            border-color: rgba(96, 165, 250, 0.32);
        }

        .quick-link-title {
            margin: 0 0 8px;
            font-size: 1rem;
            font-weight: 700;
        }

        .quick-link-text {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        @media (max-width: 900px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 1.65rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-brand">
                    <span class="admin-badge">A</span>
                    <span><?php echo htmlspecialchars($appName); ?> Admin</span>
                </div>

                <div class="admin-header-meta">
                    <div class="admin-user-pill">
                        Autentificat ca: <strong><?php echo htmlspecialchars($adminUsername ?? 'admin'); ?></strong>
                    </div>

                    <a class="admin-logout" href="/admin/index.php?action=logout">Logout</a>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <section class="hero-card">
                <p class="hero-kicker">Dashboard administrare</p>
                <h1 class="hero-title">Panou central pentru controlul operațional al aplicației</h1>
                <p class="hero-text">
                    Această zonă internă este destinată administrării aplicației web. De aici poți verifica rapid
                    starea generală a bazei de date, istoricul importurilor, disponibilitatea anilor importați,
                    configurația activă și legăturile către celelalte secțiuni administrative.
                </p>
            </section>

            <section class="section">
                <div class="section-heading">
                    <h2>Stare generală</h2>
                    <p>Indicatori sintetici pentru starea curentă a aplicației și a setului de date încărcat.</p>
                </div>

                <div class="stats-grid">
                    <article class="stat-card">
                        <span class="stat-label">Total înregistrări vehicle_records</span>
                        <div class="stat-value"><?php echo htmlspecialchars(formatAdminNumber($vehicleRecordsCount)); ?></div>
                        <div class="stat-meta">Numărul total de rânduri disponibile în baza de date pentru analiză.</div>
                    </article>

                    <article class="stat-card">
                        <span class="stat-label">Total batch-uri import</span>
                        <div class="stat-value"><?php echo htmlspecialchars(formatAdminNumber($importBatchesCount)); ?></div>
                        <div class="stat-meta">Numărul total de importuri înregistrate în tabela import_batches.</div>
                    </article>

                    <article class="stat-card">
                        <span class="stat-label">Ani disponibili</span>
                        <div class="stat-value"><?php echo htmlspecialchars(formatAdminNumber(count($availableYears))); ?></div>
                        <div class="stat-meta">
                            <?php echo htmlspecialchars(implode(', ', array_map('strval', $availableYears))); ?>
                        </div>
                    </article>

                    <article class="stat-card">
                        <span class="stat-label">Debug activ</span>
                        <div class="stat-value"><?php echo $debugEnabled ? 'DA' : 'NU'; ?></div>
                        <div class="stat-meta">Indicator util pentru comportamentul aplicației în mediul curent.</div>
                    </article>
                </div>
            </section>

            <section class="section">
                <div class="panel-grid">
                    <article class="panel-card">
                        <h2 class="panel-title">Ultimul batch de import</h2>

                        <?php if ($latestBatch !== null): ?>
                            <div class="info-list">
                                <div class="info-row">
                                    <span class="info-key">An sursă</span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['source_year'] ?? '-')); ?></span>
                                </div>

                                <div class="info-row">
                                    <span class="info-key">Fișier sursă</span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['source_file'] ?? '-')); ?></span>
                                </div>

                                <div class="info-row">
                                    <span class="info-key">Importat la</span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['imported_at'] ?? '-')); ?></span>
                                </div>

                                <div class="info-row">
                                    <span class="info-key">Rânduri inserate</span>
                                    <span class="info-value"><?php echo htmlspecialchars(formatAdminNumber((int) ($latestBatch['rows_inserted'] ?? 0))); ?></span>
                                </div>

                                <div class="info-row">
                                    <span class="info-key">Rânduri respinse</span>
                                    <span class="info-value"><?php echo htmlspecialchars(formatAdminNumber((int) ($latestBatch['rows_rejected'] ?? 0))); ?></span>
                                </div>

                                <div class="info-row">
                                    <span class="info-key">Note</span>
                                    <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['notes'] ?? 'Fără note')); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Nu există încă batch-uri de import disponibile.</p>
                        <?php endif; ?>
                    </article>

                    <article class="panel-card">
                        <h2 class="panel-title">Status tehnic</h2>

                        <div class="info-list">
                            <div class="info-row">
                                <span class="info-key">Bază de date</span>
                                <span class="info-value"><?php echo htmlspecialchars($databasePath !== '' ? $databasePath : 'Nedefinit'); ?></span>
                            </div>

                            <div class="info-row">
                                <span class="info-key">Autentificare admin</span>
                                <span class="info-value">
                                    <span class="status-ok">Activă și validată prin sesiune</span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-key">Modul administrare</span>
                                <span class="info-value">Operațional</span>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="section">
                <div class="section-heading">
                    <h2>Secțiuni administrative</h2>
                    <p>Acces rapid către principalele secțiuni administrative ale aplicației.</p>
                </div>

                <div class="quick-links">
                    <a class="quick-link-card" href="/admin/import.php">
                        <h3 class="quick-link-title">Importuri</h3>
                        <p class="quick-link-text">
                            Vizualizare batch-uri, sumar pe ani și detalii despre importurile deja executate.
                        </p>
                    </a>

                    <a class="quick-link-card" href="/admin/logs.php">
                        <h3 class="quick-link-title">Loguri</h3>
                        <p class="quick-link-text">
                            Consultarea fișierelor de log și urmărirea erorilor de import sau a stării interne.
                        </p>
                    </a>

                    <a class="quick-link-card" href="/admin/settings.php">
                        <h3 class="quick-link-title">Setări</h3>
                        <p class="quick-link-text">
                            Vizualizarea configurației aplicației, a căilor și a parametrilor utilizați.
                        </p>
                    </a>

                    <a class="quick-link-card" href="/index.php">
                        <h3 class="quick-link-title">Înapoi la interfața publică</h3>
                        <p class="quick-link-text">
                            Revenire către dashboard-ul public, hartă, căutare și restul funcționalităților vizibile.
                        </p>
                    </a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>