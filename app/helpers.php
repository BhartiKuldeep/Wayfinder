<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;

function load_env(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $_ENV[trim($key)] = trim($value);
    }
}

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

function url(string $path = ''): string
{
    $base = rtrim((string) env('APP_URL', ''), '/');
    $path = '/' . ltrim($path, '/');
    return $base ? $base . $path : $path;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = dirname(__DIR__) . '/app/views/' . $template . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(500);
        echo 'View not found: ' . e($template);
        return;
    }

    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    require dirname(__DIR__) . '/app/views/layouts/main.php';
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, mixed $value = null): mixed
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $message;
}

function back_with_errors(array $errors, array $old = []): never
{
    $_SESSION['_errors'] = $errors;
    $_SESSION['_old'] = $old;
    $previous = $_SERVER['HTTP_REFERER'] ?? '/';
    header('Location: ' . $previous);
    exit;
}

function errors(): array
{
    $errors = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors'], $_SESSION['_old']);
    return $errors;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

function auth(): Auth
{
    return new Auth();
}
