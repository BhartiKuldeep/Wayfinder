<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        return Database::connection()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }

    public static function create(string $name, string $color = '#2563eb'): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO categories (name, color) VALUES (?, ?)');
        $stmt->execute([$name, $color]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }
}
