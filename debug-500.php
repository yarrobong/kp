<?php
/**
 * Диагностика 500 ошибки
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Тест 1: Проверка PHP
echo "<h1>PHP работает</h1>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "Working directory: " . getcwd() . "<br><hr>";

// Тест 2: Проверка файлов
echo "<h2>Проверка файлов</h2>";
$files = [
    'bootstrap/app.php',
    'routes/web.php',
    'public/index.php',
    'app/Http/Request.php',
    'app/Support/Session.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/../' . $file;
    $exists = file_exists($path);
    $readable = is_readable($path);
    echo "$file: " . ($exists ? ($readable ? '✅' : '❌ not readable') : '❌ not found') . "<br>";
}
echo "<hr>";

// Тест 3: Проверка автозагрузки
echo "<h2>Проверка автозагрузки</h2>";
try {
    require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Bootstrap загружен<br>";
} catch (Throwable $e) {
    echo "❌ Bootstrap error: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Тест 4: Проверка сессии
echo "<h2>Проверка сессии</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ Сессия работает<br>";
    echo "Session ID: " . session_id() . "<br>";
} catch (Throwable $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Тест 5: Проверка маршрутов
echo "<h2>Проверка маршрутов</h2>";
try {
    require_once __DIR__ . '/../routes/web.php';
    echo "✅ Маршруты загружены<br>";
} catch (Throwable $e) {
    echo "❌ Routes error: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Тест 6: Проверка диспетчеризации
echo "<h2>Проверка диспетчеризации</h2>";
try {
    // Имитируем GET /
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = '178.209.127.17';

    $request = \App\Http\Request::capture();
    echo "✅ Request создан<br>";

    $method = $request->method();
    $path = $request->path();
    echo "Method: $method, Path: $path<br>";

} catch (Throwable $e) {
    echo "❌ Dispatch error: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Тест 7: Проверка БД
echo "<h2>Проверка БД</h2>";
try {
    if (file_exists(__DIR__ . '/../.env')) {
        echo "✅ .env файл найден<br>";
        $env = file_get_contents(__DIR__ . '/../.env');
        if (strpos($env, 'DB_HOST=') !== false) {
            echo "✅ DB настройки найдены<br>";
        } else {
            echo "❌ DB настройки не найдены<br>";
        }
    } else {
        echo "❌ .env файл не найден<br>";
    }
} catch (Throwable $e) {
    echo "❌ DB error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

echo "<h2>Диагностика завершена</h2>";
echo "Если видите ошибки выше, они указывают на проблему.<br>";
echo "Если все ✅, проблема может быть в middleware или контроллерах.";

