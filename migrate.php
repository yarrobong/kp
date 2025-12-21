<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Database\Schema\Schema;

// Запуск миграций
$migrations = [
    __DIR__ . '/database/migrations/2024_01_01_000001_create_users_table.php',
    __DIR__ . '/database/migrations/2024_01_01_000002_create_templates_table.php',
    __DIR__ . '/database/migrations/2024_01_01_000003_create_proposals_table.php',
    __DIR__ . '/database/migrations/2024_01_01_000004_create_proposal_files_table.php',
    __DIR__ . '/database/migrations/2024_01_01_000005_create_personal_access_tokens_table.php',
    __DIR__ . '/database/migrations/2024_01_01_000006_create_products_table.php',
];

foreach ($migrations as $migration) {
    if (file_exists($migration)) {
        echo "Running migration: " . basename($migration) . "\n";
        $migrationInstance = require $migration;
        if (is_callable([$migrationInstance, 'up'])) {
            $migrationInstance->up();
            echo "Migration completed successfully\n";
        } else {
            echo "Migration failed - no up() method\n";
        }
    } else {
        echo "Migration file not found: " . basename($migration) . "\n";
    }
}

echo "All migrations completed!\n";
