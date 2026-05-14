<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    public function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        session_regenerate_id(true);
        return true;
    }

    public function login(int $id): void
    {
        $_SESSION['user_id'] = $id;
        session_regenerate_id(true);
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
    }

    public function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return User::find((int) $_SESSION['user_id']);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function isAdmin(): bool
    {
        return ($this->user()['role'] ?? null) === 'admin';
    }
}
