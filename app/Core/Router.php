<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $method = $_POST['_method'] ?? $method;
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            $params = $this->match($route['path'], $path);
            if ($route['method'] === $method && $params !== false) {
                [$controller, $action] = $route['handler'];
                $instance = new $controller();
                $instance->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        view('errors/404', ['title' => 'Page not found']);
    }

    private function match(string $routePath, string $requestPath): array|false
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return false;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
