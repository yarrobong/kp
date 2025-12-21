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

    case '/proposals':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Мои КП</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/proposals">Мои КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/dashboard">Панель</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Мои коммерческие предложения</h1>
                    <a href="/proposals/create" class="btn btn-primary">Создать новое КП</a>
                </div>

                <div class="alert alert-info">
                    Функционал управления КП находится в разработке.
                    Пока что это демо-версия с базовым интерфейсом.
                </div>

                <div class="proposals-list">
                    <div class="proposal-card">
                        <div class="proposal-header">
                            <h3>Пример коммерческого предложения</h3>
                            <span class="badge badge-secondary">Черновик</span>
                        </div>
                        <div class="proposal-meta">
                            <span>Дата: ' . date('d.m.Y') . '</span>
                            <span>Клиент: Пример ООО</span>
                        </div>
                        <div class="proposal-actions">
                            <a href="#" class="btn btn-sm btn-primary">Редактировать</a>
                            <a href="#" class="btn btn-sm btn-secondary">Просмотр</a>
                        </div>
                    </div>
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/proposals/create':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Создать КП</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/proposals">Мои КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/dashboard">Панель</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Создать коммерческое предложение</h1>
                    <a href="/proposals" class="btn btn-secondary">← Назад к списку</a>
                </div>

                <div class="alert alert-info">
                    Форма создания КП находится в разработке.
                    Это демо-версия с базовым интерфейсом.
                </div>

                <form method="POST" class="proposal-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Название КП</label>
                            <input type="text" name="title" placeholder="Например: Предложение для ООО Ромашка" required>
                        </div>
                        <div class="form-group">
                            <label>Номер предложения</label>
                            <input type="text" name="offer_number" placeholder="КП-001">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Дата предложения</label>
                            <input type="date" name="offer_date" value="' . date('Y-m-d') . '" required>
                        </div>
                        <div class="form-group">
                            <label>Валюта</label>
                            <select name="currency">
                                <option value="₽">₽ Рубль</option>
                                <option value="$">$ Доллар</option>
                                <option value="€">€ Евро</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Информация о продавце</label>
                        <textarea name="seller_info" rows="3" placeholder="Название компании, адрес, контакты..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Информация о покупателе</label>
                        <textarea name="buyer_info" rows="3" placeholder="Название компании клиента, адрес, контакты..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Создать КП</button>
                        <a href="/proposals" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
        break;

    case '/templates':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Шаблоны</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/proposals">Мои КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/dashboard">Панель</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Шаблоны коммерческих предложений</h1>
                    <a href="/templates/create" class="btn btn-primary">Создать шаблон</a>
                </div>

                <div class="alert alert-info">
                    Функционал управления шаблонами находится в разработке.
                    Пока что это демо-версия с базовым интерфейсом.
                </div>

                <div class="templates-list">
                    <div class="template-card">
                        <div class="template-header">
                            <h3>Стандартный шаблон КП</h3>
                            <span class="badge badge-success">Системный</span>
                        </div>
                        <p>Универсальный шаблон для создания коммерческих предложений</p>
                        <div class="template-actions">
                            <a href="#" class="btn btn-sm btn-primary">Использовать</a>
                            <a href="#" class="btn btn-sm btn-secondary">Просмотр</a>
                        </div>
                    </div>
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/templates/create':
        if (!session('user_id')) {
            redirect('/login');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Создать шаблон</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/proposals">Мои КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/dashboard">Панель</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Создать шаблон КП</h1>
                    <a href="/templates" class="btn btn-secondary">← Назад к списку</a>
                </div>

                <div class="alert alert-info">
                    Форма создания шаблонов находится в разработке.
                    Это демо-версия с базовым интерфейсом.
                </div>

                <form method="POST" class="template-form">
                    <div class="form-group">
                        <label>Название шаблона</label>
                        <input type="text" name="title" placeholder="Например: Стандартный шаблон для IT-компаний" required>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                        <textarea name="description" rows="3" placeholder="Краткое описание назначения шаблона..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>HTML-контент шаблона</label>
                        <textarea name="body_html" rows="10" placeholder="<h1>Коммерческое предложение</h1>
<p>Уважаемый {{client_name}}!</p>
<p>Предлагаем вам наши услуги...</p>"></textarea>
                        <small class="hint">Используйте переменные в формате {{variable_name}}</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Создать шаблон</button>
                        <a href="/templates" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
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


