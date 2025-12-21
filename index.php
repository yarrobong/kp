<?php

// Простое приложение для управления товарами
// Все в одном файле - максимально просто

session_start();

// Функции для работы с товарами
function getProducts() {
    $dataFile = __DIR__ . '/products.json';
    if (!file_exists($dataFile)) {
        return [];
    }
    $products = json_decode(file_get_contents($dataFile), true);
    return is_array($products) ? $products : [];
}

function saveProducts($products) {
    file_put_contents(__DIR__ . '/products.json', json_encode($products));
}

// Обработка маршрутов
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
            } elseif ($email === 'user@example.com' && $password === 'password') {
                $_SESSION['user_id'] = 2;
                $_SESSION['user_name'] = 'Пользователь';
                header('Location: /dashboard');
                exit;
            }
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Вход - КП Генератор</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-card">
                    <h1>Вход в систему</h1>';

        if (isset($_GET['error'])) {
            echo '<div class="alert alert-error">Неверный email или пароль</div>';
        }

        echo '<form method="POST">
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

                    <div style="margin-top: 20px; text-align: center;">
                        <p><strong>Тестовые аккаунты:</strong></p>
                        <p>admin@example.com / password</p>
                        <p>user@example.com / password</p>
                    </div>
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

        // Подсчет товаров
        $userProductsCount = 0;
        $allProducts = getProducts();
        if (is_array($allProducts)) {
            foreach ($allProducts as $product) {
                if (isset($product['user_id']) && $product['user_id'] == $_SESSION['user_id']) {
                    $userProductsCount++;
                }
            }
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
                    <h1>Добро пожаловать, ' . htmlspecialchars($_SESSION['user_name']) . '!</h1>
                </div>

                <div class="dashboard-metrics">
                    <div class="metric-card">
                        <div class="metric-icon">📦</div>
                        <div class="metric-value">' . $userProductsCount . '</div>
                        <div class="metric-label">Товаров</div>
                    </div>
                </div>

                <div class="alert alert-success">
                    Система работает! Товары хранятся в файле products.json
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/products':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Получить товары пользователя
        $userProducts = [];
        $allProducts = getProducts();
        if (is_array($allProducts)) {
            foreach ($allProducts as $product) {
                if (isset($product['user_id']) && $product['user_id'] == $_SESSION['user_id']) {
                    $userProducts[] = $product;
                }
            }
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

                <div class="products-grid">';

        if (empty($userProducts)) {
            echo '<div class="product-card" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
                        <div class="product-title">Каталог пуст</div>
                        <div class="product-description">Добавьте первый товар</div>
                        <div style="margin-top: 20px;">
                            <a href="/products/create" class="btn btn-primary">+ Добавить товар</a>
                        </div>
                    </div>';
        } else {
            foreach ($userProducts as $product) {
                echo '<div class="product-card">
                        <div class="product-image-container">
                            <img src="' . htmlspecialchars($product['image'] ?? '/css/placeholder-product.svg') . '" alt="' . htmlspecialchars($product['name']) . '" class="product-image">
                        </div>
                        <div class="product-info">
                            <div class="product-title">' . htmlspecialchars($product['name']) . '</div>
                            <div class="product-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                            ' . (!empty($product['description']) ? '<div class="product-description">' . htmlspecialchars(substr($product['description'], 0, 100)) . '</div>' : '') . '
                        </div>
                    </div>';
            }
        }

        echo '</div>

                <a href="/products/create" class="fab" title="Добавить товар">+</a>
            </main>
        </body>
        </html>';
        break;

    case '/products/create':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $error = 'Название товара обязательно';
            } elseif ($price <= 0) {
                $error = 'Цена должна быть больше 0';
            } else {
                // Сохраняем товар
                $products = getProducts();
                if (!is_array($products)) {
                    $products = [];
                }
                $maxId = 0;
                foreach ($products as $product) {
                    if (isset($product['id']) && $product['id'] > $maxId) {
                        $maxId = $product['id'];
                    }
                }
                $newId = $maxId + 1;

                $products[$newId] = [
                    'id' => $newId,
                    'user_id' => $_SESSION['user_id'],
                    'name' => $name,
                    'price' => $price,
                    'category' => $category,
                    'description' => $description,
                    'image' => '/css/placeholder-product.svg',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                saveProducts($products);
                header('Location: /products?success=' . urlencode('Товар "' . $name . '" успешно добавлен!'));
                exit;
            }
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
                        <a href="/products">Товары</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Добавить товар</h1>
                    <a href="/products" class="btn btn-secondary">← Назад</a>
                </div>';

        if (!empty($success)) {
            echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
        }
        if (!empty($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

        echo '<form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Название товара</label>
                        <input type="text" name="name" placeholder="Ноутбук Lenovo ThinkPad" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Цена (₽)</label>
                            <input type="number" name="price" step="0.01" placeholder="10000.00" required>
                        </div>
                        <div class="form-group">
                            <label>Категория</label>
                            <select name="category">
                                <option>Электроника</option>
                                <option>Оборудование</option>
                                <option>Программное обеспечение</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                        <textarea name="description" rows="4" placeholder="Подробное описание товара..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Сохранить товар</button>
                        <a href="/products" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
        break;

    case '/logout':
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
                <a href="/" class="btn btn-primary">На главную</a>
            </div>
        </body>
        </html>';
        break;
}