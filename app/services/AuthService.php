<?php

declare(strict_types=1);

class AuthService
{
    private const SESSION_KEY_LOGGED_IN = 'admin_logged_in';
    private const SESSION_KEY_USERNAME = 'admin_username';

    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function startSessionIfNeeded(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getAdminCredentials(): array
    {
        $adminConfig = $this->config['admin'] ?? [];

        $username = isset($adminConfig['username']) ? (string) $adminConfig['username'] : 'admin';
        $password = isset($adminConfig['password']) ? (string) $adminConfig['password'] : 'admin123';

        return [
            'username' => $username,
            'password' => $password,
        ];
    }

    public function login(string $username, string $password): bool
    {
        $this->startSessionIfNeeded();

        $credentials = $this->getAdminCredentials();

        $isValid =
            hash_equals($credentials['username'], $username) &&
            hash_equals($credentials['password'], $password);

        if (!$isValid) {
            return false;
        }

        $_SESSION[self::SESSION_KEY_LOGGED_IN] = true;
        $_SESSION[self::SESSION_KEY_USERNAME] = $username;

        return true;
    }

    public function logout(): void
    {
        $this->startSessionIfNeeded();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        $this->startSessionIfNeeded();

        return isset($_SESSION[self::SESSION_KEY_LOGGED_IN]) && $_SESSION[self::SESSION_KEY_LOGGED_IN] === true;
    }

    public function getLoggedInUsername(): ?string
    {
        $this->startSessionIfNeeded();

        if (!isset($_SESSION[self::SESSION_KEY_USERNAME])) {
            return null;
        }

        return (string) $_SESSION[self::SESSION_KEY_USERNAME];
    }
}