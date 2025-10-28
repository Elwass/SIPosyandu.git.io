<?php
function redirect(string $path): void
{
    header('Location: ' . $path);
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
