<?php

// Простая рабочая версия для отладки

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Автозагрузка
spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
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

// Вспомогательные функции
if (!function_exists('session')) {
    function session($key = null, $value = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($key === null) {
            return $_SESSION ?? [];
        }

        if ($value === null) {
            return $_SESSION[$key] ?? null;
        }

        $_SESSION[$key] = $value;
    }
}

if (!function_exists('view')) {
    function view($template, $data = []) {
        extract($data);
        $file = __DIR__ . '/../resources/views/' . str_replace('.', '/', $template) . '.php';
        if (file_exists($file)) {
            ob_start();
            include $file;
            return ob_get_clean();
        }
        return "<h1>View not found: $template</h1>";
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        header('Location: ' . $path);
        exit;
    }
}

// Простая маршрутизация
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

switch ($uri) {
    case '/':
        if (session('user_id')) {
            redirect('/dashboard');
        } else {
            redirect('/login');
        }
        break;

    case '/login':
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Вход</title>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .form-group { margin: 10px 0; }
                input { padding: 8px; width: 200px; }
                button { padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; }
                .error { color: red; }
            </style>
        </head>
        <body>
            <h1>Вход в систему</h1>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Простая проверка для демо
            if ($email === 'admin@example.com' && $password === 'password') {
                session('user_id', 1);
                session('user_name', 'Admin');
                redirect('/dashboard');
            } else {
                echo '<div class="error">Неверный email или пароль</div>';
            }
        }

        echo '
            <form method="POST">
                <div class="form-group">
                    <label>Email:</label><br>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Пароль:</label><br>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Войти</button>
            </form>
            <p><a href="/debug">Отладка</a></p>
        </body>
        </html>';
        break;

    case '/dashboard':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Панель управления</title>
            <meta charset="utf-8">
        </head>
        <body>
            <h1>Добро пожаловать, ' . session('user_name') . '!</h1>
            <p><a href="/proposals">Управление КП</a></p>
            <p><a href="/templates">Шаблоны</a></p>
            <p><a href="/logout">Выход</a></p>
        </body>
        </html>';
        break;

    case '/logout':
        session_destroy();
        redirect('/login');
        break;

    case '/debug':
        echo '<h1>Отладка</h1>';
        echo '<pre>';
        echo 'PHP Version: ' . PHP_VERSION . "\n";
        echo 'URI: ' . $uri . "\n";
        // Ensure session is started before accessing it
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        echo 'Session: ' . print_r($_SESSION ?? [], true) . "\n";
        echo 'Files in public/: ' . implode(', ', scandir(__DIR__)) . "\n";
        echo '</pre>';
        break;

    default:
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        echo '<p>URI: ' . $uri . '</p>';
        echo '<p><a href="/">Главная</a></p>';
        break;
}


