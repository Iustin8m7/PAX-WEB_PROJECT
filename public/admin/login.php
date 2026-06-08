<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/auth.php';

startAdminSession();
redirectIfAdminAlreadyLoggedIn();

$errorMessage = '';
$usernameValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameValue = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $passwordValue = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($usernameValue === '' || $passwordValue === '') {
        $errorMessage = 'Utilizatorul și parola sunt obligatorii.';
    } else {
        $authService = getAuthService();
        $loginSucceeded = $authService->login($usernameValue, $passwordValue);

        if ($loginSucceeded) {
            header('Location: /admin/index.php');
            exit;
        }

        $errorMessage = 'Datele de autentificare sunt incorecte.';
    }
}

$config = require __DIR__ . '/../../app/config/config.php';
$appName = isset($config['app_name']) ? (string) $config['app_name'] : 'Pax';

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> - Admin Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.92);
            --border-soft: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --accent: #60a5fa;
            --accent-strong: #3b82f6;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: rgba(239, 68, 68, 0.32);
            --danger-text: #fecaca;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top, rgba(59, 130, 246, 0.16), transparent 32%),
                linear-gradient(180deg, #020617 0%, #0f172a 55%, #111827 100%);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-shell {
            width: 100%;
            max-width: 460px;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(10px);
        }

        .login-kicker {
            margin: 0 0 10px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .login-title {
            margin: 0 0 12px;
            font-size: 1.9rem;
            line-height: 1.15;
        }

        .login-subtitle {
            margin: 0 0 28px;
            color: var(--text-muted);
            line-height: 1.65;
            font-size: 0.98rem;
        }

        .error-box {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            font-size: 0.94rem;
        }

        .form-field {
            margin-bottom: 18px;
        }

        .form-field label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.92rem;
            color: var(--text-muted);
        }

        .form-field input {
            width: 100%;
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            padding: 13px 15px;
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-main);
            font: inherit;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-field input:focus {
            border-color: rgba(96, 165, 250, 0.7);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        .login-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .btn {
            appearance: none;
            border: none;
            border-radius: 14px;
            padding: 13px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease, opacity 0.15s ease, background 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: white;
            flex: 1 1 180px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            flex: 1 1 140px;
        }

        .login-footer {
            margin-top: 22px;
            font-size: 0.88rem;
            color: var(--text-muted);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-card">
            <p class="login-kicker">Modul administrare</p>
            <h1 class="login-title">Autentificare admin</h1>
            <p class="login-subtitle">
                Introdu datele de autentificare pentru a accesa zona internă de administrare a aplicației
                <?php echo htmlspecialchars($appName); ?>.
            </p>

            <?php if ($errorMessage !== ''): ?>
                <div class="error-box">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-field">
                    <label for="username">Utilizator</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($usernameValue); ?>"
                        autocomplete="username"
                        required>
                </div>

                <div class="form-field">
                    <label for="password">Parolă</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required>
                </div>

                <div class="login-actions">
                    <button type="submit" class="btn btn-primary">Autentificare</button>
                    <a href="/index.php" class="btn btn-secondary">Înapoi la site</a>
                </div>
            </form>

            <div class="login-footer">
                Accesul este permis doar administratorului aplicației.
            </div>
        </div>
    </div>
</body>
</html>