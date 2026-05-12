<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
Database::connection()->exec($sql);
echo "Migrations completed.\n";
