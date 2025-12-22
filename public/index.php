<?php
// Точка входа приложения
session_start();

// Определение константы для корневой директории
define('ROOT_DIR', dirname(__DIR__));
define('PROJECT_ROOT', ROOT_DIR);

// Автозагрузка классов
spl_autoload_register(function ($className) {
    $prefix = 'Controllers\\';
    $base_dir = ROOT_DIR . '/controllers/';
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        $prefix = 'Models\\';
        $base_dir = ROOT_DIR . '/models/';
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            $prefix = 'Core\\';
            $base_dir = ROOT_DIR . '/core/';
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
require_once ROOT_DIR . '/vendor/autoload.php';

use Core\Router;

// Инициализация роутера
$router = new Router();

// Маршруты
$router->get('/', 'HomeController@index');
$router->get('/health', 'HomeController@health');

// Обработка ботов и сканеров
$router->post('/webhook.php', function() {
    http_response_code(404);
    exit;
});
$router->get('/webhook.php', function() {
    http_response_code(404);
    exit;
});

$router->get('/test', function() {
    echo "Test route works!";
});
$router->post('/test-post', function() {
    $data = $_POST;
    echo "POST data received: " . json_encode($data);
});

// Маршруты аутентификации
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@store');
$router->post('/logout', 'AuthController@logout');

// Маршруты админ панели
$router->get('/admin', 'AdminController@index');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/{id}/role', 'AdminController@changeUserRole');
$router->post('/admin/users/{id}/delete', 'AdminController@deleteUser');
$router->get('/admin/users/{id}/products', 'AdminController@userProducts');
$router->get('/admin/users/{id}/proposals', 'AdminController@userProposals');

// Маршруты личного кабинета
$router->get('/user', 'UserController@index');
$router->get('/user/edit', 'UserController@edit');
$router->post('/user', 'UserController@update');
$router->get('/user/products', 'UserController@products');
$router->get('/user/proposals', 'UserController@proposals');

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

// Вспомогательная функция для определения активной страницы
function isActivePage($pageUri) {
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return ($currentUri === $pageUri) ? 'active' : '';
}

// Запуск роутинга
$router->run();
