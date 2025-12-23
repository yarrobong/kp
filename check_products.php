<?php
/**
 * Скрипт для проверки товаров в базе данных
 */

require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/controllers/AuthController.php';

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

echo "🔍 Проверка товаров в базе данных...\n\n";

// Проверяем все товары
echo "📦 Все товары в базе данных:\n";
$allProducts = \Models\Product::getAllWithFallback();
echo "Всего товаров: " . count($allProducts) . "\n";

foreach ($allProducts as $product) {
    echo "  ID: {$product['id']}, Name: {$product['name']}, UserID: {$product['user_id']}\n";
}

echo "\n";

// Проверяем товары для разных user_id
for ($userId = 1; $userId <= 3; $userId++) {
    echo "📦 Товары пользователя (ID: {$userId}):\n";
    $userProducts = \Models\Product::getAll($userId);
    echo "Всего товаров: " . count($userProducts) . "\n";

    foreach ($userProducts as $product) {
        echo "  ID: {$product['id']}, Name: {$product['name']}\n";
    }
    echo "\n";
}

// Проверяем работу без фильтрации по пользователю
echo "📦 Товары без фильтрации (getAll(null)):\n";
$unfilteredProducts = \Models\Product::getAll(null);
echo "Всего товаров: " . count($unfilteredProducts) . "\n";

foreach ($unfilteredProducts as $product) {
    echo "  ID: {$product['id']}, Name: {$product['name']}, UserID: {$product['user_id']}\n";
}

echo "\n✅ Проверка завершена\n";
?>
