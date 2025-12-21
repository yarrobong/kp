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
                session('user_name', 'Администратор');
                session('user_role', 'admin'); // Устанавливаем роль админа
                redirect('/dashboard');
            } elseif ($email === 'user@example.com' && $password === 'password') {
                session('user_id', 2);
                session('user_name', 'Пользователь');
                session('user_role', 'user'); // Устанавливаем роль пользователя
                redirect('/dashboard');
            } else {
                $error = 'Неверный email или пароль. Попробуйте:<br>Админ: admin@example.com / password<br>Пользователь: user@example.com / password';
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
                    <div class="text-center">
                        <p><strong>Тестовые аккаунты:</strong></p>
                        <p>Админ: admin@example.com / password</p>
                        <p>Пользователь: user@example.com / password</p>
                        <p><a href="/register">Регистрация</a> | <a href="/debug">Отладка</a></p>
                    </div>
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
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Мои товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <h1>Добро пожаловать, ' . session('user_name') . '!</h1>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Мои товары</h3>
                        <div class="stat-number">0</div>
                        <a href="/products" class="btn btn-sm btn-primary">Управлять</a>
                    </div>
                    <div class="stat-card">
                        <h3>Коммерческие предложения</h3>
                        <div class="stat-number">0</div>
                        <a href="/proposals" class="btn btn-sm btn-primary">Управлять</a>
                    </div>
                    <div class="stat-card">
                        <h3>Шаблоны</h3>
                        <div class="stat-number">0</div>
                        <a href="/templates" class="btn btn-sm btn-primary">Управлять</a>
                    </div>
                </div>

                <div class="dashboard-sections">
                    <div class="dashboard-card">
                        <h2>📦 Управление товарами</h2>
                        <p>Добавляйте товары в свой каталог оборудования</p>
                        <div class="form-actions">
                            <a href="/products" class="btn btn-primary">Мои товары</a>
                            <a href="/products/create" class="btn btn-secondary">Добавить товар</a>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h2>📄 Коммерческие предложения</h2>
                        <p>Создавайте КП на основе вашего каталога товаров</p>
                        <div class="form-actions">
                            <a href="/proposals" class="btn btn-primary">Мои КП</a>
                            <a href="/proposals/create" class="btn btn-secondary">Создать КП</a>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h2>🎨 Шаблоны</h2>
                        <p>Настраивайте внешний вид ваших предложений</p>
                        <div class="form-actions">
                            <a href="/templates" class="btn btn-primary">Мои шаблоны</a>
                            <a href="/templates/create" class="btn btn-secondary">Создать шаблон</a>
                        </div>
                    </div>
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];

            // Валидация
            if (empty($name)) {
                $errors[] = 'Имя обязательно для заполнения';
            }
            if (empty($email)) {
                $errors[] = 'Email обязателен для заполнения';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Неверный формат email';
            }
            if (empty($password)) {
                $errors[] = 'Пароль обязателен для заполнения';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Пароль должен содержать минимум 6 символов';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Пароли не совпадают';
            }

            // Проверка существующего email (пока что простая проверка)
            if ($email === 'admin@example.com' || $email === 'user@example.com') {
                $errors[] = 'Этот email уже зарегистрирован';
            }

            if (empty($errors)) {
                // В будущем: сохранить в базу данных
                // Пока что просто редирект на логин с сообщением
                session('success', 'Регистрация успешна! Теперь вы можете войти.');
                redirect('/login');
            } else {
                $errorMessage = implode('<br>', $errors);
            }
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Регистрация</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-card">
                    <h1>Регистрация</h1>';

        if (isset($errorMessage)) {
            echo '<div class="alert alert-error">' . $errorMessage . '</div>';
        }

        echo '
                    <form method="POST">
                        <div class="form-group">
                            <label>Имя</label>
                            <input type="text" name="name" required autofocus>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>Пароль</label>
                            <input type="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>Подтверждение пароля</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </form>

                    <p class="text-center">
                        Уже есть аккаунт? <a href="/login">Войти</a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
        break;

    case '/admin':
        // Проверка на админа (простая проверка по роли)
        if (!session('user_id') || session('user_role') !== 'admin') {
            redirect('/dashboard');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Админ панель</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор - Админ</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/admin">Админка</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Админ панель</h1>
                </div>

                <div class="admin-stats">
                    <div class="stat-card">
                        <h3>Всего пользователей</h3>
                        <div class="stat-number">2</div>
                    </div>
                    <div class="stat-card">
                        <h3>Всего товаров</h3>
                        <div class="stat-number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Всего КП</h3>
                        <div class="stat-number">0</div>
                    </div>
                </div>

                <div class="admin-content">
                    <div class="admin-section">
                        <h2>👥 Управление пользователями</h2>

                        <div class="users-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Имя</th>
                                        <th>Email</th>
                                        <th>Роль</th>
                                        <th>Дата регистрации</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Администратор</td>
                                        <td>admin@example.com</td>
                                        <td><span class="badge badge-success">Админ</span></td>
                                        <td>' . date('d.m.Y') . '</td>
                                        <td>
                                            <form method="POST" action="/admin/users/1/role" style="display: inline;">
                                                <input type="hidden" name="_token" value="demo">
                                                <select name="role" onchange="this.form.submit()">
                                                    <option value="admin" selected>Админ</option>
                                                    <option value="user">Пользователь</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Пользователь</td>
                                        <td>user@example.com</td>
                                        <td><span class="badge badge-secondary">Пользователь</span></td>
                                        <td>' . date('d.m.Y') . '</td>
                                        <td>
                                            <form method="POST" action="/admin/users/2/role" style="display: inline;">
                                                <input type="hidden" name="_token" value="demo">
                                                <select name="role" onchange="this.form.submit()">
                                                    <option value="admin">Админ</option>
                                                    <option value="user" selected>Пользователь</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-actions" style="margin-top: 20px;">
                            <a href="/admin/users" class="btn btn-secondary">Детальное управление</a>
                        </div>

                        <div class="alert alert-info" style="margin-top: 20px;">
                            <strong>Примечание:</strong> Изменение ролей пользователей будет работать после настройки базы данных.
                            Сейчас показаны демо-данные для тестирования интерфейса.
                        </div>
                    </div>

                    <div class="admin-section">
                        <h2>📊 Статистика системы</h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3>Активных пользователей</h3>
                                <div class="stat-number">2</div>
                            </div>
                            <div class="stat-card">
                                <h3>Всего товаров</h3>
                                <div class="stat-number">0</div>
                            </div>
                            <div class="stat-card">
                                <h3>Создано КП</h3>
                                <div class="stat-number">0</div>
                            </div>
                            <div class="stat-card">
                                <h3>Шаблонов</h3>
                                <div class="stat-number">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <style>
                .admin-stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin: 30px 0;
                }

                .admin-sections {
                    display: grid;
                    gap: 20px;
                    margin-top: 40px;
                }

                .admin-card {
                    background: #fff;
                    padding: 25px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .admin-card h2 {
                    margin: 0 0 10px 0;
                    color: #333;
                }

                .admin-card p {
                    color: #666;
                    margin-bottom: 20px;
                }
            </style>
        </body>
        </html>';
        break;

    case '/admin/users':
        // Проверка на админа
        if (!session('user_id') || session('user_role') !== 'admin') {
            redirect('/dashboard');
        }

        // Обработка изменения роли
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
            $userId = (int)$_POST['user_id'];
            $newRole = $_POST['role'];

            if (in_array($newRole, ['admin', 'user', 'guest'])) {
                // В будущем: обновить в базе данных
                session('success', "Роль пользователя ID {$userId} изменена на '{$newRole}'");
                redirect('/admin');
            }
        }

        // Показать страницу управления пользователями
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Управление пользователями</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор - Админ</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/admin">Админка</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Управление пользователями</h1>
                    <a href="/admin" class="btn btn-secondary">← Назад в админку</a>
                </div>

                <div class="users-management">
                    <div class="alert alert-info">
                        Управление ролями пользователей. Здесь можно назначать администраторов и изменять роли.
                    </div>

                    <div class="users-list">
                        <h3>Список пользователей</h3>

                        <div class="user-item">
                            <div class="user-info">
                                <strong>Администратор</strong><br>
                                <span class="user-email">admin@example.com</span><br>
                                <span class="user-role badge badge-success">Администратор</span>
                            </div>
                            <div class="user-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="1">
                                    <select name="role" onchange="this.form.submit()">
                                        <option value="admin" selected>Администратор</option>
                                        <option value="user">Пользователь</option>
                                        <option value="guest">Гость</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="user-item">
                            <div class="user-info">
                                <strong>Пользователь</strong><br>
                                <span class="user-email">user@example.com</span><br>
                                <span class="user-role badge badge-secondary">Пользователь</span>
                            </div>
                            <div class="user-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="2">
                                    <select name="role" onchange="this.form.submit()">
                                        <option value="admin">Администратор</option>
                                        <option value="user" selected>Пользователь</option>
                                        <option value="guest">Гость</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <style>
                .users-management {
                    max-width: 800px;
                    margin: 0 auto;
                }

                .users-list h3 {
                    margin-bottom: 20px;
                    color: #333;
                }

                .user-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    margin-bottom: 15px;
                    background: #fff;
                }

                .user-info {
                    flex-grow: 1;
                }

                .user-info strong {
                    font-size: 18px;
                    color: #333;
                }

                .user-email {
                    color: #666;
                    font-size: 14px;
                }

                .user-role {
                    font-size: 12px;
                    margin-top: 5px;
                }

                .user-actions {
                    flex-shrink: 0;
                }

                .user-actions select {
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    background: #fff;
                    font-size: 14px;
                }

                .user-actions select:focus {
                    outline: none;
                    border-color: #007bff;
                }
            </style>
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

    case '/products':
        if (!session('user_id')) {
            redirect('/login');
        }

        // Получаем товары пользователя (пока что пустой массив, так как нет базы)
        $products = []; // В будущем: Product::getByUserId(session('user_id'))

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Мои товары</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Мои товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Мои товары</h1>
                    <a href="/products/create" class="btn btn-primary">Добавить товар</a>
                </div>

                <div class="alert alert-info">
                    Каталог товаров находится в разработке.
                    Функционал добавления товаров будет доступен после настройки базы данных.
                </div>

                <div class="products-grid">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="/css/placeholder-product.png" alt="Пример товара" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                        </div>
                        <div class="product-info">
                            <h3>Пример товара</h3>
                            <p class="product-price">₽ 10,000</p>
                            <p>Описание товара будет здесь</p>
                        </div>
                        <div class="product-actions">
                            <a href="#" class="btn btn-sm btn-primary">Редактировать</a>
                            <a href="#" class="btn btn-sm btn-danger">Удалить</a>
                        </div>
                    </div>
                </div>
            </main>

            <style>
                .products-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }

                .product-card {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    overflow: hidden;
                }

                .product-image {
                    padding: 15px;
                }

                .product-info {
                    padding: 15px;
                }

                .product-info h3 {
                    margin: 0 0 10px 0;
                    font-size: 18px;
                }

                .product-price {
                    font-size: 20px;
                    font-weight: bold;
                    color: #007bff;
                    margin: 10px 0;
                }

                .product-info p {
                    color: #666;
                    margin: 10px 0;
                }

                .product-actions {
                    padding: 15px;
                    border-top: 1px solid #eee;
                    display: flex;
                    gap: 10px;
                }
            </style>
        </body>
        </html>';
        break;

    case '/products/create':
        if (!session('user_id')) {
            redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Обработка формы создания товара
            // В будущем: сохранить в базу данных
            redirect('/products');
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Добавить товар</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Мои товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Добавить товар</h1>
                    <a href="/products" class="btn btn-secondary">← Назад к товарам</a>
                </div>

                <div class="alert alert-info">
                    Форма добавления товаров находится в разработке.
                    После настройки базы данных можно будет добавлять товары с фото.
                </div>

                <form method="POST" enctype="multipart/form-data" class="product-form">
                    <div class="form-group">
                        <label>Название товара</label>
                        <input type="text" name="name" placeholder="Например: Ноутбук Lenovo ThinkPad" required>
                    </div>

                    <div class="form-group">
                        <label>Цена (₽)</label>
                        <input type="number" name="price" step="0.01" placeholder="10000.00" required>
                    </div>

                    <div class="form-group">
                        <label>Фото товара</label>
                        <input type="file" name="photo" accept="image/*">
                        <small class="hint">Поддерживаются форматы: JPG, PNG, GIF (макс. 5MB)</small>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                        <textarea name="description" rows="4" placeholder="Подробное описание товара, характеристики..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Добавить товар</button>
                        <a href="/products" class="btn btn-secondary">Отмена</a>
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


