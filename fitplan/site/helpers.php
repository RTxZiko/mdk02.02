<?php
declare(strict_types=1);

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

function is_post_request(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function pull_flash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    if (empty($_SESSION['flash'])) {
        unset($_SESSION['flash']);
    }

    return $message;
}

function query_string(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;

    return is_string($value) ? trim($value) : $default;
}

function query_int(string $key): ?int
{
    if (!isset($_GET[$key]) || $_GET[$key] === '') {
        return null;
    }

    return filter_var($_GET[$key], FILTER_VALIDATE_INT) !== false ? (int) $_GET[$key] : null;
}

function post_string(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    return is_string($value) ? trim($value) : $default;
}

function post_int(string $key): ?int
{
    if (!isset($_POST[$key]) || $_POST[$key] === '') {
        return null;
    }

    return filter_var($_POST[$key], FILTER_VALIDATE_INT) !== false ? (int) $_POST[$key] : null;
}

function build_url(string $path, array $params = []): string
{
    $params = array_filter(
        $params,
        static function ($value): bool {
            return $value !== null && $value !== '';
        }
    );

    return $params === [] ? $path : $path . '?' . http_build_query($params);
}

function safe_redirect_target(?string $target, string $default = 'index.php'): string
{
    if (!is_string($target) || $target === '') {
        return $default;
    }

    foreach (['index.php', 'login.php', 'register.php'] as $prefix) {
        if (strpos($target, $prefix) === 0) {
            return $target;
        }
    }

    return $default;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf_request(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    return is_string($token) && hash_equals(csrf_token(), $token);
}
