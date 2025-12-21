<?php
session_start();
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/');

switch ($uri) {
    case '':
    case '/':
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
        } else {
            header('Location: /login');
        }
        exit;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($email === 'admin@example.com' && $password === 'password') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = 'Администратор';
                header('Location: /dashboard');
                exit;
            }
        }

        echo '<!DOCTYPE html>
        <html>
        <head><title>Вход</title><link rel="stylesheet" href="/css/app.css"></head>
        <body>
            <div class="auth-container">
                <div class="auth-card">
                    <h1>Вход</h1>
                    <form method="POST">
                        <input type="email" name="email" placeholder="Email" required><br>
                        <input type="password" name="password" placeholder="Пароль" required><br>
                        <button type="submit">Войти</button>
                    </form>
                    <p>admin@example.com / password</p>
                </div>
            </div>
        </body>
        </html>';
        break;

    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        echo '<!DOCTYPE html>
        <html>
        <head><title>Панель</title><link rel="stylesheet" href="/css/app.css"></head>
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
                <h1>Добро пожаловать, ' . htmlspecialchars($_SESSION['user_name']) . '</h1>
                <div class="alert alert-success">Система работает! Проект максимально упрощен.</div>
            </main>
        </body>
        </html>';
        break;

    case '/products':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        echo '<!DOCTYPE html>
        <html>
        <head><title>Товары</title><link rel="stylesheet" href="/css/app.css"></head>
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
                <h1>Каталог товаров</h1>
                <p>Каталог пуст. <a href="/products/create">Добавить товар</a></p>
            </main>
        </body>
        </html>';
        break;

    case '/logout':
        session_destroy();
        header('Location: /login');
        exit;

    default:
        echo '<h1>404</h1>';
}
?>
