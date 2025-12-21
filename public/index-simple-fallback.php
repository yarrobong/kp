<?php

// Простая версия без базы данных для диагностики

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Обработка маршрутов
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/');

switch ($uri) {
    case '':
    case '/':
        header('Location: /login');
        exit;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($email === 'admin@example.com' && $password === 'password') {
                session_start();
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = 'Администратор';
                $_SESSION['user_role'] = 'admin';
                header('Location: /dashboard');
                exit;
            } elseif ($email === 'user@example.com' && $password === 'password') {
                session_start();
                $_SESSION['user_id'] = 2;
                $_SESSION['user_name'] = 'Пользователь';
                $_SESSION['user_role'] = 'user';
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Неверный email или пароль';
            }
        }

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
                            <input type="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>Пароль</label>
                            <input type="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>

                    <div style="margin-top: 20px; text-align: center;">
                        <p>Тестовые аккаунты:</p>
                        <p>admin@example.com / password</p>
                        <p>user@example.com / password</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        break;

    case '/dashboard':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
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
                        <a href="/products">Товары</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Добро пожаловать, ' . $_SESSION['user_name'] . '!</h1>
                </div>

                <div class="alert alert-success">
                    Сайт работает! База данных будет подключена позже.
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/products':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Каталог товаров</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Каталог товаров</h1>
                </div>

                <div class="alert alert-info">
                    Каталог товаров. Функционал будет восстановлен после исправления базы данных.
                </div>

                <div class="products-grid">
                    <div class="product-card" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
                        <div class="product-title">Каталог пуст</div>
                        <div class="product-description">База данных будет подключена позже</div>
                    </div>
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/logout':
        session_start();
        session_destroy();
        header('Location: /login');
        exit;

    default:
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div style="text-align: center; padding: 100px 20px;">
                <h1>404 - Страница не найдена</h1>
                <a href="/login" class="btn btn-primary">На главную</a>
            </div>
        </body>
        </html>';
        break;
}
