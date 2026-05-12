<?php

declare(strict_types=1);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/';
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

require_once __DIR__ . '/helpers.php';

load_env(dirname(__DIR__) . '/.env');

$sessionName = env('SESSION_NAME', 'wayfinder_session');
if (session_status() === PHP_SESSION_NONE) {
    session_name($sessionName);
    session_start();
}
