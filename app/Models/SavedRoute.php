<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SavedRoute
{
    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO saved_routes (user_id, source_place_id, destination_place_id, path_json, distance_km, travel_time_minutes) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['source_place_id'],
            $data['destination_place_id'],
            json_encode($data['path']),
            $data['distance_km'],
            $data['travel_time_minutes'],
        ]);
    }

    public static function forUser(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT saved_routes.*, a.name AS source_name, b.name AS destination_name
            FROM saved_routes
            JOIN places a ON a.id = saved_routes.source_place_id
            JOIN places b ON b.id = saved_routes.destination_place_id
            WHERE saved_routes.user_id = ?
            ORDER BY saved_routes.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function deleteForUser(int $id, int $userId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM saved_routes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    public static function countAll(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM saved_routes')->fetchColumn();
    }
}
