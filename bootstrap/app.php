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

// Загрузка вспомогательных функций
require_once __DIR__ . '/helpers.php';

