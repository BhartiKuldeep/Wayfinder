<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Place
{
    public static function all(array $filters = []): array
    {
        $sql = 'SELECT places.*, categories.name AS category_name, categories.color AS category_color
                FROM places
                LEFT JOIN categories ON categories.id = places.category_id
                WHERE 1 = 1';
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= ' AND (places.name LIKE ? OR places.address LIKE ? OR places.description LIKE ?)';
            $term = '%' . $filters['q'] . '%';
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND places.category_id = ?';
            $params[] = $filters['category_id'];
        }

        $sql .= ' ORDER BY places.name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT places.*, categories.name AS category_name, categories.color AS category_color
            FROM places
            LEFT JOIN categories ON categories.id = places.category_id
            WHERE places.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO places (category_id, name, slug, description, address, latitude, longitude, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['category_id'],
            $data['name'],
            self::slug($data['name']),
            $data['description'] ?? null,
            $data['address'] ?? null,
            $data['latitude'] ?: null,
            $data['longitude'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare('UPDATE places SET category_id = ?, name = ?, slug = ?, description = ?, address = ?, latitude = ?, longitude = ? WHERE id = ?');
        $stmt->execute([
            $data['category_id'],
            $data['name'],
            self::slug($data['name']),
            $data['description'] ?? null,
            $data['address'] ?? null,
            $data['latitude'] ?: null,
            $data['longitude'] ?: null,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM places WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM places')->fetchColumn();
    }

    private static function slug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        return $slug . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    }
}
