<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'user',
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
