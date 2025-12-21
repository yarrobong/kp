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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Простая проверка для демо
            if ($email === 'admin@example.com' && $password === 'password') {
                session('user_id', 1);
                session('user_name', 'Admin');
                redirect('/dashboard');
            } else {
                $error = 'Неверный email или пароль';
            }
        }

        // HTML с CSS
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Вход</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-card">
                    <h1>Вход</h1>';

        if (isset($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

        echo '
                    <form method="POST">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Пароль</label>
                            <input type="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>
                    <p class="text-center">
                        <a href="/debug">Отладка</a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
        break;

    case '/dashboard':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Панель управления</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/proposals">Мои КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <h1>Добро пожаловать, ' . session('user_name') . '!</h1>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Коммерческие предложения</h3>
                        <div class="stat-number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Шаблоны</h3>
                        <div class="stat-number">0</div>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="/proposals" class="btn btn-primary">Управление КП</a>
                    <a href="/templates" class="btn btn-secondary">Шаблоны</a>
                </div>
            </main>
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


