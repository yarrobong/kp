<?php

// Диагностический скрипт для выявления проблем с PHP
echo "=== PHP Диагностика ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Current Directory: " . __DIR__ . "\n";

// Проверка расширений
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'session'];
echo "\n=== Проверка расширений ===\n";
foreach ($required_extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? 'OK' : 'MISSING') . "\n";
}

// Проверка файлов
echo "\n=== Проверка файлов ===\n";
$files_to_check = [
    'bootstrap/app.php',
    'config/database.php',
    'app/Models/Product.php',
    'public/css/app.css'
];

foreach ($files_to_check as $file) {
    echo "$file: " . (file_exists(__DIR__ . '/../' . $file) ? 'EXISTS' : 'MISSING') . "\n";
}

// Проверка базы данных
echo "\n=== Проверка базы данных ===\n";
try {
    if (file_exists(__DIR__ . '/../.env')) {
        echo ".env file: EXISTS\n";

        // Попытка загрузки .env
        $env_content = file_get_contents(__DIR__ . '/../.env');
        if (preg_match('/DB_HOST=(.+)/', $env_content, $matches)) {
            echo "DB_HOST: " . trim($matches[1]) . "\n";
        }
        if (preg_match('/DB_DATABASE=(.+)/', $env_content, $matches)) {
            echo "DB_DATABASE: " . trim($matches[1]) . "\n";
        }
    } else {
        echo ".env file: MISSING\n";
    }

    // Попытка подключения к БД
    require_once __DIR__ . '/../bootstrap/app.php';
    echo "Bootstrap: LOADED\n";

    $db = \App\Database\Database::connection();
    echo "Database connection: SUCCESS\n";

    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database: " . implode(', ', $tables) . "\n";

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== Конец диагностики ===\n";
?>
