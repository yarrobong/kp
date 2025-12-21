<?php
// Точка входа приложения
session_start();

// Определение константы для корневой директории
define('ROOT_DIR', dirname(__DIR__));
define('PROJECT_ROOT', ROOT_DIR);

// Автозагрузка классов
spl_autoload_register(function ($className) {
    // Конвертируем namespace в путь к файлу
    $file = str_replace('\\', '/', $className);
    $file = ROOT_DIR . '/' . $file . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Подключение зависимостей
require_once ROOT_DIR . '/vendor/autoload.php';

// Инициализация роутера
$router = new Router();

// Маршруты
$router->get('/', 'HomeController@index');
$router->get('/health', 'HomeController@health');

// Маршруты товаров
$router->get('/products', 'ProductController@index');
$router->get('/products/create', 'ProductController@create');
$router->post('/products', 'ProductController@store');
$router->get('/products/{id}', 'ProductController@show');
$router->get('/products/{id}/edit', 'ProductController@edit');
$router->post('/products/{id}', 'ProductController@update');
$router->post('/products/{id}/delete', 'ProductController@delete');
$router->get('/api/products/search', 'ProductController@search');

// Маршруты предложений
$router->get('/proposals', 'ProposalController@index');
$router->get('/proposals/create', 'ProposalController@create');
$router->post('/proposals', 'ProposalController@store');
$router->get('/proposals/{id}', 'ProposalController@show');
$router->get('/proposals/{id}/edit', 'ProposalController@edit');
$router->post('/proposals/{id}', 'ProposalController@update');
$router->post('/proposals/{id}/delete', 'ProposalController@delete');
$router->get('/proposals/{id}/pdf', 'ProposalController@pdf');

// Запуск роутинга
$router->run();

// Вспомогательная функция для определения активной страницы
function isActivePage($pageUri) {
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return ($currentUri === $pageUri) ? 'active' : '';
}
