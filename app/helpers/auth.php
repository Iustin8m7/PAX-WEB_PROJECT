<?php

declare(strict_types=1);

require_once __DIR__ . '/../services/AuthService.php';

function getAuthService(): AuthService
{
    static $authService = null;

    if ($authService instanceof AuthService) {
        return $authService;
    }

    $authService = new AuthService();
    return $authService;
}

function startAdminSession(): void
{
    getAuthService()->startSessionIfNeeded();
}

function isAdminLoggedIn(): bool
{
    return getAuthService()->isLoggedIn();
}

function requireAdminAuth(): void
{
    if (isAdminLoggedIn()) {
        return;
    }

    header('Location: /admin/login.php');
    exit;
}

function redirectIfAdminAlreadyLoggedIn(): void
{
    if (!isAdminLoggedIn()) {
        return;
    }

    header('Location: /admin/index.php');
    exit;
}

function logoutAdmin(): void
{
    getAuthService()->logout();
}

function getAdminUsername(): ?string
{
    return getAuthService()->getLoggedInUsername();
}