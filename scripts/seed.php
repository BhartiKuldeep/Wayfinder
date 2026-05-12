<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$pdo = Database::connection();

function insertIgnore(string $table, array $data): void
{
    $pdo = Database::connection();
    $columns = array_keys($data);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $sql = 'INSERT IGNORE INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($data));
}

insertIgnore('users', [
    'name' => 'Wayfinder Admin',
    'email' => 'admin@wayfinder.test',
    'password' => password_hash('admin123', PASSWORD_BCRYPT),
    'role' => 'admin',
]);

$categories = [
    ['name' => 'Hospital', 'color' => '#dc2626'],
    ['name' => 'Education', 'color' => '#2563eb'],
    ['name' => 'Transport', 'color' => '#16a34a'],
    ['name' => 'Food', 'color' => '#f97316'],
    ['name' => 'Office', 'color' => '#7c3aed'],
];

foreach ($categories as $category) {
    insertIgnore('categories', $category);
}

$categoryIds = [];
foreach ($pdo->query('SELECT id, name FROM categories')->fetchAll() as $category) {
    $categoryIds[$category['name']] = $category['id'];
}

$adminId = (int) $pdo->query("SELECT id FROM users WHERE email = 'admin@wayfinder.test'")->fetchColumn();

$places = [
    ['Education', 'Central Library', 'central-library', 'Public library with study rooms and digital resources.', 'Sector 12 Main Road', '28.61390000', '77.20900000'],
    ['Transport', 'City Bus Stand', 'city-bus-stand', 'Primary public bus terminal.', 'Station Road', '28.61450000', '77.21500000'],
    ['Hospital', 'Metro Hospital', 'metro-hospital', 'Emergency care and outpatient services.', 'Health Avenue', '28.61800000', '77.22000000'],
    ['Food', 'Market Square', 'market-square', 'Food court and shopping area.', 'Market Road', '28.62000000', '77.21000000'],
    ['Office', 'Tech Park', 'tech-park', 'Business and startup office campus.', 'Innovation Street', '28.62500000', '77.21800000'],
];

foreach ($places as [$category, $name, $slug, $description, $address, $lat, $lng]) {
    insertIgnore('places', [
        'category_id' => $categoryIds[$category] ?? null,
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'address' => $address,
        'latitude' => $lat,
        'longitude' => $lng,
        'created_by' => $adminId,
    ]);
}

$placeIds = [];
foreach ($pdo->query('SELECT id, name FROM places')->fetchAll() as $place) {
    $placeIds[$place['name']] = $place['id'];
}

$edges = [
    ['Central Library', 'City Bus Stand', 1.20, 8, 'Main road connection'],
    ['City Bus Stand', 'Metro Hospital', 2.10, 12, 'Fastest transit corridor'],
    ['Central Library', 'Market Square', 1.80, 10, 'Walkable route'],
    ['Market Square', 'Tech Park', 2.40, 14, 'Commercial road'],
    ['Metro Hospital', 'Tech Park', 1.50, 9, 'Ring road shortcut'],
];

foreach ($edges as [$from, $to, $distance, $time, $notes]) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM edges WHERE from_place_id = ? AND to_place_id = ?');
    $stmt->execute([$placeIds[$from], $placeIds[$to]]);
    if ((int) $stmt->fetchColumn() === 0) {
        $insert = $pdo->prepare('INSERT INTO edges (from_place_id, to_place_id, distance_km, travel_time_minutes, notes, is_bidirectional) VALUES (?, ?, ?, ?, ?, 1)');
        $insert->execute([$placeIds[$from], $placeIds[$to], $distance, $time, $notes]);
    }
}

echo "Seed data inserted.\n";
echo "Admin: admin@wayfinder.test / admin123\n";
