<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Edge
{
    public static function all(): array
    {
        return Database::connection()->query('SELECT edges.*, a.name AS from_name, b.name AS to_name
            FROM edges
            JOIN places a ON a.id = edges.from_place_id
            JOIN places b ON b.id = edges.to_place_id
            ORDER BY a.name, b.name')->fetchAll();
    }

    public static function raw(): array
    {
        return Database::connection()->query('SELECT * FROM edges')->fetchAll();
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO edges (from_place_id, to_place_id, distance_km, travel_time_minutes, notes, is_bidirectional) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['from_place_id'],
            $data['to_place_id'],
            $data['distance_km'],
            $data['travel_time_minutes'],
            $data['notes'] ?? null,
            !empty($data['is_bidirectional']) ? 1 : 0,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM edges WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM edges')->fetchColumn();
    }
}
