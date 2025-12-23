<?php
/**
 * Скрипт для тестирования авторизации
 */

// Автозагрузка классов
spl_autoload_register(function ($className) {
    $prefix = 'Controllers\\';
    $base_dir = __DIR__ . '/controllers/';
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        $prefix = 'Models\\';
        $base_dir = __DIR__ . '/models/';
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            $prefix = 'Core\\';
            $base_dir = __DIR__ . '/core/';
            $len = strlen($prefix);
            if (strncmp($prefix, $className, $len) !== 0) {
                return;
            }
        }
    }
    $relativeClass = substr($className, $len);
    $file = $base_dir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Подключение зависимостей
require_once __DIR__ . '/vendor/autoload.php';

// Инициализация сессии
session_start();

echo "<h1>Тест авторизации</h1>\n";

// Попытка авторизации под админом
echo "<h2>Авторизация под админом...</h2>\n";
$_POST['username'] = 'admin';
$_POST['password'] = 'admin123';

$result = \Controllers\AuthController::authenticate();

if ($result) {
    echo "<p style='color: green;'>✅ Авторизация успешна!</p>\n";

    // Проверяем пользователя
    $user = \Controllers\AuthController::getCurrentUser();
    if ($user) {
        echo "<p>Пользователь: {$user['username']} (ID: {$user['id']})</p>\n";

        // Проверяем товары
        $products = \Models\Product::getAll($user['id']);
        echo "<p>Количество товаров: " . count($products) . "</p>\n";

        echo "<h3>Список товаров:</h3>\n";
        echo "<ul>\n";
        foreach ($products as $product) {
            echo "<li>{$product['name']} (ID: {$product['id']})</li>\n";
        }
        echo "</ul>\n";
    }
} else {
    echo "<p style='color: red;'>❌ Авторизация не удалась</p>\n";
}

echo "<hr>\n";
echo "<p><a href='/products'>Перейти к товарам</a></p>\n";
echo "<p><a href='/debug'>Отладка</a></p>\n";
?>
