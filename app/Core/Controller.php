<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        view($view, $data);
    }

    protected function requireAuth(): void
    {
        if (!auth()->check()) {
            flash('error', 'Please login first.');
            redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!auth()->isAdmin()) {
            http_response_code(403);
            view('errors/403', ['title' => 'Forbidden']);
            exit;
        }
    }

    protected function validateCsrf(): void
    {
        Csrf::validate($_POST['_token'] ?? '');
    }
}
