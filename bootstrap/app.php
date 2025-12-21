<?php

// Автозагрузка классов
spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
        'Database\\Seeders\\' => __DIR__ . '/../database/seeders/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Загрузка .env файла
if (file_exists(__DIR__ . '/../.env')) {
    $envContent = file_get_contents(__DIR__ . '/../.env');
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Загрузка вспомогательных функций
require_once __DIR__ . '/helpers.php';

