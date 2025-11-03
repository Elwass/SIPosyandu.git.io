<?php

function app_config(): array
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    return $config;
}

function config(?string $key = null, $default = null)
{
    $config = app_config();
    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function app_base_path(): string
{
    $base = trim((string) config('app.base_url', ''));
    if ($base === '' || $base === '/') {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $dir = str_replace('\\', '/', dirname($scriptName));
        if ($dir === '/' || $dir === '\\' || $dir === '.') {
            return '';
        }
        return rtrim($dir, '/');
    }

    if (preg_match('#^https?://#i', $base)) {
        return rtrim($base, '/');
    }

    return '/' . ltrim(rtrim($base, '/'), '/');
}

function app_entry_script(): string
{
    $base = app_base_path();

    if (preg_match('#^https?://#i', $base)) {
        return $base . '/index.php';
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $script = '/' . ltrim(str_replace('\\', '/', $scriptName), '/');

    if ($base === '') {
        return $script;
    }

    return $base . '/index.php';
}

function url(string $path = ''): string
{
    $base = app_base_path();

    if ($path === '' || $path === '/') {
        return $base === '' ? '/' : $base;
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    if ($path[0] === '?') {
        return app_entry_script() . $path;
    }

    if (preg_match('#^https?://#i', $base)) {
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    $prefix = $base === '' ? '' : rtrim($base, '/');

    return $prefix . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_role(array $roles): void
{
    $user = user();
    if (!$user) {
        redirect('?page=login');
    }

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        include __DIR__ . '/Views/errors/403.php';
        exit;
    }
}

function flash(string $key, ?string $value = null)
{
    if ($value === null) {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    $_SESSION['flash'][$key] = $value;
}
