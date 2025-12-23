<?php
/**
 * Генератор sitemap.xml для КП Генератор
 * Возвращает XML карту сайта
 */

// Автозагрузка классов
spl_autoload_register(function ($className) {
    $prefix = 'Controllers\\';
    $base_dir = __DIR__ . '/../controllers/';
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        $prefix = 'Models\\';
        $base_dir = __DIR__ . '/../models/';
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            $prefix = 'Core\\';
            $base_dir = __DIR__ . '/../core/';
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
require_once __DIR__ . '/../vendor/autoload.php';

// Инициализация сессии
session_start();

// Функция для генерации URL
function generateUrl($path, $priority = '0.5', $changefreq = 'weekly') {
    $baseUrl = 'https://' . $_SERVER['HTTP_HOST']; // Замените на ваш домен
    $lastmod = date('Y-m-d');

    return [
        'loc' => $baseUrl . $path,
        'lastmod' => $lastmod,
        'changefreq' => $changefreq,
        'priority' => $priority
    ];
}

// Основные страницы сайта
$sitemap = [
    // Главная страница
    generateUrl('/', '1.0', 'daily'),

    // Секции главной страницы
    generateUrl('/#features', '0.8', 'monthly'),
    generateUrl('/#how-to-use', '0.8', 'monthly'),
    generateUrl('/#stats', '0.8', 'monthly'),
    generateUrl('/#get-started', '0.8', 'monthly'),

    // Основные разделы
    generateUrl('/products', '0.9', 'daily'),
    generateUrl('/proposals', '0.8', 'daily'),

    // Страницы аутентификации (низкий приоритет)
    generateUrl('/login', '0.3', 'yearly'),
    generateUrl('/register', '0.4', 'yearly'),
];

// Получаем товары из базы данных
try {
    $products = \Models\Product::getAllWithFallback();
    foreach ($products as $product) {
        $sitemap[] = generateUrl('/products/' . $product['id'], '0.6', 'weekly');
    }
} catch (Exception $e) {
    // Если не удалось получить товары, пропускаем
}

// Получаем предложения из базы данных
try {
    $proposals = \Models\Proposal::getAllWithFallback();
    foreach ($proposals as $proposal) {
        $sitemap[] = generateUrl('/proposals/' . $proposal['id'], '0.4', 'weekly');
    }
} catch (Exception $e) {
    // Если не удалось получить предложения, пропускаем
}

// Устанавливаем заголовки для XML
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // Кэшируем на 1 час

// Генерируем XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
echo '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

foreach ($sitemap as $url) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
    echo '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
    echo '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $url['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>' . "\n";
?>
