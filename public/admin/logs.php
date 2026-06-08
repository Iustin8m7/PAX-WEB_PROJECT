<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/services/AdminService.php';

startAdminSession();
requireAdminAuth();

$adminService = new AdminService();
$logData = $adminService->getLogData();

$config = require __DIR__ . '/../../app/config/config.php';

if (isset($config['app_name'])) {
    $appName = (string) $config['app_name'];
} else {
    $appName = 'Pax';
}

$adminUsername = getAdminUsername();

if (isset($logData['log_path'])) {
    $logPath = (string) $logData['log_path'];
} else {
    $logPath = '';
}

if (isset($logData['log_exists'])) {
    $logExists = (bool) $logData['log_exists'];
} else {
    $logExists = false;
}

if (isset($logData['log_size_bytes'])) {
    $logSizeBytes = (int) $logData['log_size_bytes'];
} else {
    $logSizeBytes = 0;
}

if (isset($logData['lines']) && is_array($logData['lines'])) {
    $lines = $logData['lines'];
} else {
    $lines = [];
}

if (isset($adminUsername)) {
    $displayAdminUsername = (string) $adminUsername;
} else {
    $displayAdminUsername = 'admin';
}

if ($logPath !== '') {
    $displayLogPath = $logPath;
} else {
    $displayLogPath = 'Nedefinit';
}

if ($logExists) {
    $logStatusHtml = '<span class="status-pill ok">Disponibil</span>';
} else {
    $logStatusHtml = '<span class="status-pill error">Inexistent</span>';
}

if ($logExists) {
    $logExistsText = 'Da';
} else {
    $logExistsText = 'Nu';
}

function formatLogNumber(int $value): string
{
    return number_format($value, 0, ',', '.');
}

function formatLogSize(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    if ($bytes < 1024 * 1024) {
        return number_format($bytes / 1024, 2, ',', '.') . ' KB';
    }

    if ($bytes < 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
    }

    return number_format($bytes / (1024 * 1024 * 1024), 2, ',', '.') . ' GB';
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Admin Loguri</title>

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
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
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

        .log-viewer {
            background: rgba(2, 6, 23, 0.85);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 18px;
            max-height: 620px;
            overflow: auto;
        }

        .log-line {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.88rem;
            line-height: 1.6;
            color: #e2e8f0;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            white-space: pre-wrap;
            word-break: break-word;
        }

        .log-line:last-child {
            border-bottom: none;
        }

        .footer-note {
            margin-top: 12px;
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.6;
        }

        @media (max-width: 900px) {
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
                <a href="/admin/logs.php" class="active">Loguri</a>
                <a href="/admin/settings.php">Setări</a>
                <a href="/index.php">Site public</a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <section class="hero-card">
            <p class="hero-kicker">Administrare loguri</p>
            <h1 class="hero-title">Monitorizarea fișierelor de log și a ultimelor mesaje înregistrate</h1>
            <p class="hero-text">
                Această pagină centralizează informațiile despre fișierul de log folosit de aplicație și afișează
                ultimele linii disponibile, pentru a permite verificarea rapidă a erorilor de import și a stării
                operaționale a componentelor interne.
            </p>
        </section>

        <section class="section">
            <div class="section-heading">
                <h2>Indicatori rapizi</h2>
                <p>Starea generală a fișierului de log și a sesiunii administrative curente.</p>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Administrator autentificat</span>
                    <div class="stat-value"><?php echo htmlspecialchars($displayAdminUsername); ?></div>
                    <div class="stat-meta">Utilizatorul activ din sesiunea curentă.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Fișier de log</span>
                    <div class="stat-value">
                        <?php echo $logStatusHtml; ?>
                    </div>
                    <div class="stat-meta">Verificare rapidă a existenței logului configurat pentru aplicație.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Dimensiune log</span>
                    <div class="stat-value"><?php echo htmlspecialchars(formatLogSize($logSizeBytes)); ?></div>
                    <div class="stat-meta">Dimensiunea curentă a fișierului de log.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Linii încărcate</span>
                    <div class="stat-value"><?php echo htmlspecialchars(formatLogNumber(count($lines))); ?></div>
                    <div class="stat-meta">Numărul de linii afișate în viewer-ul de mai jos.</div>
                </article>
            </div>
        </section>

        <section class="section">
            <article class="panel-card">
                <div class="section-heading">
                    <h2>Informații despre fișierul de log</h2>
                    <p>Detalii utile pentru diagnostic și verificare rapidă.</p>
                </div>

                <div class="info-list">
                    <div class="info-row">
                        <span class="info-key">Cale fișier log</span>
                        <span class="info-value"><?php echo htmlspecialchars($displayLogPath); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-key">Există fizic pe disc</span>
                        <span class="info-value"><?php echo $logExistsText; ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-key">Dimensiune în bytes</span>
                        <span class="info-value"><?php echo htmlspecialchars(formatLogNumber($logSizeBytes)); ?></span>
                    </div>
                </div>
            </article>
        </section>

        <section class="section">
            <article class="panel-card">
                <div class="section-heading">
                    <h2>Ultimele linii din log</h2>
                    <p>Viewer simplu pentru verificarea ultimelor mesaje disponibile.</p>
                </div>

                <div class="log-viewer">
                    <?php
                    if ($lines !== []) {
                        foreach ($lines as $line) {
                            echo '<div class="log-line">' . htmlspecialchars((string) $line) . '</div>';
                        }
                    } else {
                        echo '<div class="log-line">Nu există linii disponibile pentru afișare.</div>';
                    }
                    ?>
                </div>

                <p class="footer-note">
                    Viewer-ul afișează ultimele linii citite din fișierul de log. Dacă logul nu există sau nu poate fi citit,
                    această secțiune rămâne goală sau afișează mesajul de fallback.
                </p>
            </article>
        </section>
    </main>
</body>
</html>