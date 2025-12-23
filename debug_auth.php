<?php
/**
 * Отладочный скрипт для проверки авторизации
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

// Проверяем сессию
echo "<h2>Отладка авторизации</h2>\n";
echo "<pre>\n";

echo "=== СЕССИЯ ===\n";
var_dump($_SESSION);

echo "\n=== COOKIE ===\n";
var_dump($_COOKIE);

echo "\n=== ПОЛЬЗОВАТЕЛЬ ===\n";
$user = \Controllers\AuthController::getCurrentUser();
if ($user) {
    var_dump($user);
} else {
    echo "Пользователь не авторизован\n";
}

echo "\n=== ТОВАРЫ ===\n";
if ($user) {
    $products = \Models\Product::getAll($user['id']);
    echo "Товаров для пользователя {$user['id']}: " . count($products) . "\n";
    foreach ($products as $product) {
        echo "  - {$product['name']} (ID: {$product['id']})\n";
    }
} else {
    echo "Невозможно проверить товары - пользователь не авторизован\n";
}

echo "\n=== ВСЕ ТОВАРЫ ===\n";
$allProducts = \Models\Product::getAllWithFallback();
echo "Всего товаров в БД: " . count($allProducts) . "\n";
foreach ($allProducts as $product) {
    echo "  - {$product['name']} (ID: {$product['id']}, UserID: {$product['user_id']})\n";
}

echo "</pre>\n";

echo "<h3>Тестовые ссылки:</h3>\n";
echo "<a href='/products'>Перейти к товарам</a><br>\n";
echo "<a href='/login'>Войти в систему</a><br>\n";
?>
