<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/services/AdminService.php';

startAdminSession();
requireAdminAuth();

$adminService = new AdminService();
$importData = $adminService->getImportData();

$config = require __DIR__ . '/../../app/config/config.php';
$appName = isset($config['app_name']) ? (string) $config['app_name'] : 'Pax';

$adminUsername = getAdminUsername();
$recentBatches = isset($importData['recent_batches']) && is_array($importData['recent_batches'])
    ? $importData['recent_batches']
    : [];
$summaryByYear = isset($importData['summary_by_year']) && is_array($importData['summary_by_year'])
    ? $importData['summary_by_year']
    : [];
$latestBatch = isset($importData['latest_batch']) && is_array($importData['latest_batch'])
    ? $importData['latest_batch']
    : null;

function formatImportNumber(int $value): string
{
    return number_format($value, 0, ',', '.');
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Admin Importuri</title>

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
        .panel-card {
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

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 22px;
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

        .table-shell {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid var(--border-soft);
            background: rgba(255, 255, 255, 0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
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

        @media (max-width: 960px) {
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
                <a href="/admin/import.php" class="active">Importuri</a>
                <a href="/admin/logs.php">Loguri</a>
                <a href="/admin/settings.php">Setări</a>
                <a href="/index.php">Site public</a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <section class="hero-card">
            <p class="hero-kicker">Administrare importuri</p>
            <h1 class="hero-title">Monitorizarea batch-urilor și a stării importurilor</h1>
            <p class="hero-text">
                Această zonă centralizează istoricul importurilor disponibile în aplicație. Poți consulta ultimul batch,
                sumarul pe ani și lista recentă a execuțiilor pentru a verifica dacă datele au fost încărcate corect.
            </p>
        </section>

        <section class="section">
            <div class="section-heading">
                <h2>Indicatori rapizi</h2>
                <p>Indicatori sintetici pentru starea actuală a modulului de import.</p>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Administrator autentificat</span>
                    <div class="stat-value"><?php echo htmlspecialchars((string) ($adminUsername ?? 'admin')); ?></div>
                    <div class="stat-meta">Utilizatorul activ din sesiunea curentă de administrare.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Batch-uri recente afișate</span>
                    <div class="stat-value"><?php echo htmlspecialchars(formatImportNumber(count($recentBatches))); ?></div>
                    <div class="stat-meta">Numărul de intrări listate în tabelul de mai jos.</div>
                </article>

                <article class="stat-card">
                    <span class="stat-label">Ani cu batch-uri</span>
                    <div class="stat-value"><?php echo htmlspecialchars(formatImportNumber(count($summaryByYear))); ?></div>
                    <div class="stat-meta">Numărul de ani sursă pentru care există importuri înregistrate.</div>
                </article>
            </div>
        </section>

        <section class="section">
            <article class="panel-card">
                <div class="section-heading">
                    <h2>Ultimul import</h2>
                    <p>Detaliile celei mai recente execuții înregistrate în `import_batches`.</p>
                </div>

                <?php if ($latestBatch !== null): ?>
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-key">ID batch</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['id'] ?? '-')); ?></span>
                        </div>

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
                            <span class="info-value"><?php echo htmlspecialchars(formatImportNumber((int) ($latestBatch['rows_inserted'] ?? 0))); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Rânduri respinse</span>
                            <span class="info-value"><?php echo htmlspecialchars(formatImportNumber((int) ($latestBatch['rows_rejected'] ?? 0))); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Note</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) ($latestBatch['notes'] ?? 'Fără note')); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Nu există batch-uri de import disponibile.</p>
                <?php endif; ?>
            </article>
        </section>

        <section class="section">
            <div class="section-heading">
                <h2>Sumar importuri pe ani</h2>
                <p>Agregare a batch-urilor înregistrate pentru fiecare an sursă.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>An sursă</th>
                            <th>Număr batch-uri</th>
                            <th>Total rânduri inserate</th>
                            <th>Total rânduri respinse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($summaryByYear !== []): ?>
                            <?php foreach ($summaryByYear as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string) ($row['source_year'] ?? '-')); ?></td>
                                    <td><?php echo htmlspecialchars(formatImportNumber((int) ($row['batches_count'] ?? 0))); ?></td>
                                    <td><?php echo htmlspecialchars(formatImportNumber((int) ($row['total_rows_inserted'] ?? 0))); ?></td>
                                    <td><?php echo htmlspecialchars(formatImportNumber((int) ($row['total_rows_rejected'] ?? 0))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Nu există date agregate pentru importuri.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <div class="section-heading">
                <h2>Batch-uri recente</h2>
                <p>Lista celor mai recente importuri disponibile în sistem.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>An sursă</th>
                            <th>Fișier sursă</th>
                            <th>Importat la</th>
                            <th>Rânduri inserate</th>
                            <th>Rânduri respinse</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentBatches !== []): ?>
                            <?php foreach ($recentBatches as $batch): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string) ($batch['id'] ?? '-')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($batch['source_year'] ?? '-')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($batch['source_file'] ?? '-')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($batch['imported_at'] ?? '-')); ?></td>
                                    <td><?php echo htmlspecialchars(formatImportNumber((int) ($batch['rows_inserted'] ?? 0))); ?></td>
                                    <td><?php echo htmlspecialchars(formatImportNumber((int) ($batch['rows_rejected'] ?? 0))); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($batch['notes'] ?? '')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nu există batch-uri de import disponibile.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>