<?php

// Современное веб-приложение для генерации КП
// Glassmorphism дизайн, адаптивность, современный UX

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

// Обработка маршрутов
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/');

switch ($uri) {
    case '':
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
                session('user_role', 'admin');
                redirect('/dashboard');
            } elseif ($email === 'user@example.com' && $password === 'password') {
                session('user_id', 2);
                session('user_name', 'Пользователь');
                session('user_role', 'user');
                redirect('/dashboard');
            } else {
                $error = 'Неверный email или пароль. Попробуйте:<br>Админ: admin@example.com / password<br>Пользователь: user@example.com / password';
            }
        }

        // Современная страница входа с glassmorphism
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

                    <div style="text-align: center; margin-top: 24px;">
                        <p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
                    </div>
                </div>

                <div class="test-accounts">
                    <h3 style="text-align: center; margin-bottom: 20px; color: #4a5568;">Тестовые аккаунты</h3>

                    <div class="account-card" onclick="fillForm(\'admin@example.com\', \'password\')">
                        <div class="account-avatar">A</div>
                        <div class="account-info">
                            <div class="account-role">Администратор</div>
                            <div class="account-email">admin@example.com</div>
                        </div>
                        <button class="account-fill">Войти</button>
                    </div>

                    <div class="account-card" onclick="fillForm(\'user@example.com\', \'password\')">
                        <div class="account-avatar">U</div>
                        <div class="account-info">
                            <div class="account-role">Пользователь</div>
                            <div class="account-email">user@example.com</div>
                        </div>
                        <button class="account-fill">Войти</button>
                    </div>
                </div>
            </div>

            <script>
            function fillForm(email, password) {
                document.querySelector(\'input[name="email"]\').value = email;
                document.querySelector(\'input[name="password"]\').value = password;
                setTimeout(() => {
                    document.querySelector(\'form\').submit();
                }, 500);
            }
            </script>
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

            if ($email === 'admin@example.com' || $email === 'user@example.com') {
                $errors[] = 'Этот email уже зарегистрирован';
            }

            if (empty($errors)) {
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

                    <p style="text-align: center; margin-top: 24px;">
                        Уже есть аккаунт? <a href="/login">Войти</a>
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
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Добро пожаловать, ' . session('user_name') . '!</h1>
                </div>

                <div class="dashboard-metrics">
                    <div class="metric-card">
                        <div class="metric-icon">📦</div>
                        <div class="metric-value">';

        // Подсчет товаров пользователя
        $userProductsCount = 0;
        $userId = session('user_id');
        $dataFile = __DIR__ . '/../storage/products.json';
        if (file_exists($dataFile)) {
            $allProducts = json_decode(file_get_contents($dataFile), true) ?: [];
            if (is_array($allProducts)) {
                foreach ($allProducts as $product) {
                    if (isset($product['user_id']) && $product['user_id'] == $userId) {
                        $userProductsCount++;
                    }
                }
            }
        }
        echo $userProductsCount;

        echo '</div>
                        <div class="metric-label">Товаров</div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">📄</div>
                        <div class="metric-value">';

        // Подсчет предложений пользователя
        $userProposalsCount = 0;
        $userId = session('user_id');
        $dataFile = __DIR__ . '/../storage/proposals.json';
        if (file_exists($dataFile)) {
            $allProposals = json_decode(file_get_contents($dataFile), true) ?: [];
            if (is_array($allProposals)) {
                foreach ($allProposals as $proposal) {
                    if (isset($proposal['user_id']) && $proposal['user_id'] == $userId) {
                        $userProposalsCount++;
                    }
                }
            }
        }
        echo $userProposalsCount;

        echo '</div>
                        <div class="metric-label">КП создано</div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">🎨</div>
                        <div class="metric-value">0</div>
                        <div class="metric-label">Шаблонов</div>
                    </div>
                </div>

                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon">📦</div>
                        <div class="action-title">Управление товарами</div>
                        <div class="action-description">Добавляйте товары в каталог</div>
                    </div>

                    <div class="action-card">
                        <div class="action-icon">📄</div>
                        <div class="action-title">Коммерческие предложения</div>
                        <div class="action-description">Создавайте КП из товаров</div>
                    </div>

                    <div class="action-card">
                        <div class="action-icon">🎨</div>
                        <div class="action-title">Шаблоны</div>
                        <div class="action-description">Настраивайте дизайн КП</div>
                    </div>
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/products':
        if (!session('user_id')) {
            redirect('/login');
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
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Каталог товаров</h1>
                    <input type="text" placeholder="🔍 Поиск товаров..." style="padding: 12px 16px; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                </div>';

        // Показать сообщение успеха из URL параметра
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
        }

        // Получить товары из файла (всегда свежие данные)
        $userProducts = [];
        $userId = session('user_id');
        $allProducts = [];

        $dataFile = __DIR__ . '/../storage/products.json';
        if (file_exists($dataFile)) {
            $fileData = json_decode(file_get_contents($dataFile), true);
            if (is_array($fileData)) {
                $allProducts = $fileData;
                // Сохраняем в сессию для быстрого доступа
                session('products', $allProducts);
            }
        }

        $debugInfo = "User ID: $userId, All Products: " . count($allProducts ?? []) . ", Session: " . (is_array($allProducts) ? 'array' : gettype($allProducts));

        if (is_array($allProducts)) {
            foreach ($allProducts as $product) {
                if (isset($product['user_id']) && $product['user_id'] == $userId) {
                    $userProducts[] = $product;
                }
            }
        }

        $debugInfo .= ", User Products: " . count($userProducts);

        echo '

                <!-- Debug Info: ' . $debugInfo . ' -->
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
                            ' . (!empty($product['description']) ? '<div class="product-description">' . htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : '') . '</div>' : '') . '
                        </div>
                        <div class="product-actions">
                            <a href="/products/' . $product['id'] . '" class="btn btn-sm">Просмотр</a>
                            <a href="/products/' . $product['id'] . '/edit" class="btn btn-sm btn-secondary">Редактировать</a>
                            <form method="POST" action="/products/' . $product['id'] . '" style="display: inline;">
                                <input type="hidden" name="_token" value="' . session('_token') . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Удалить товар?\')">Удалить</button>
                            </form>
                        </div>
                    </div>';
            }
        }

        echo '</div>

                <a href="/products/create" class="fab" title="Добавить товар">+</a>

        <!-- Toast Notifications Container -->
        <div id="toast-container"></div>
        </main>
        </body>
        </html>';
        break;

    case '/products/create':
        if (!session('user_id')) {
            redirect('/login');
        }

        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Валидация
            if (empty($name)) {
                $error = 'Название товара обязательно';
            } elseif ($price <= 0) {
                $error = 'Цена должна быть больше 0';
            } else {
                // Загружаем существующие товары из файла
                $dataFile = __DIR__ . '/../storage/products.json';
                $existingProducts = [];
                if (file_exists($dataFile)) {
                    $existingProducts = json_decode(file_get_contents($dataFile), true) ?: [];
                }

                // Определяем следующий ID
                $maxId = 0;
                foreach ($existingProducts as $product) {
                    if (isset($product['id']) && $product['id'] > $maxId) {
                        $maxId = $product['id'];
                    }
                }
                $newId = $maxId + 1;

                // Создаем новый товар
                $newProduct = [
                    'id' => $newId,
                    'user_id' => session('user_id'),
                    'name' => $name,
                    'price' => $price,
                    'category' => $category,
                    'description' => $description,
                    'image' => '/css/placeholder-product.svg',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Добавляем к существующим товарам
                $existingProducts[$newId] = $newProduct;

                // Сохраняем в сессии и файл
                session('products', $existingProducts);
                file_put_contents($dataFile, json_encode($existingProducts));

                $success = 'Товар "' . htmlspecialchars($name) . '" успешно добавлен!';

                // Очистить форму (редирект)
                header('Location: /products?success=' . urlencode($success));
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
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
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
            echo '<div class="alert alert-success">' . $success . '</div>';
        }
        if (!empty($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

        echo '<div class="alert alert-info">
                    Форма добавления товаров. После настройки базы данных товары будут сохраняться.
                </div>

                <form method="POST" enctype="multipart/form-data">
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
                        <label>Фото товара</label>
                        <input type="file" name="photo" accept="image/*">
                        <div class="hint">Поддерживаются JPG, PNG, GIF (макс. 5MB)</div>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                        <textarea name="description" rows="4" placeholder="Подробное описание товара..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Сохранить товар</button>
                        <a href="/products" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
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
            <title>Коммерческие предложения</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Коммерческие предложения</h1>
                    <a href="/proposals/create" class="btn btn-primary">Создать КП</a>
                </div>';

        // Показать сообщение успеха из URL параметра
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
        }

        // Получить предложения из файла (всегда свежие данные)
        $userProposals = [];
        $userId = session('user_id');
        $allProposals = [];

        $dataFile = __DIR__ . '/../storage/proposals.json';
        if (file_exists($dataFile)) {
            $fileData = json_decode(file_get_contents($dataFile), true);
            if (is_array($fileData)) {
                $allProposals = $fileData;
                session('proposals', $allProposals);
            }
        }

        if (is_array($allProposals)) {
            foreach ($allProposals as $proposal) {
                if (isset($proposal['user_id']) && $proposal['user_id'] == $userId) {
                    $userProposals[] = $proposal;
                }
            }
        }

        echo '

                <div class="proposals-list">';

        if (empty($userProposals)) {
            echo '<div class="proposal-card" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📄</div>
                        <div class="proposal-header">
                            <h3>Нет предложений</h3>
                        </div>
                        <div class="proposal-meta">
                            <span>Создайте первое коммерческое предложение</span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a href="/proposals/create" class="btn btn-primary">+ Создать КП</a>
                        </div>
                    </div>';
        } else {
            foreach ($userProposals as $proposal) {
                echo '<div class="proposal-card">
                        <div class="proposal-header">
                            <h3><a href="/proposals/' . $proposal['id'] . '">' . htmlspecialchars($proposal['title']) . '</a></h3>
                            <span class="badge badge-secondary">Черновик</span>
                        </div>
                        <div class="proposal-meta">
                            <span>Дата: ' . date('d.m.Y', strtotime($proposal['offer_date'])) . '</span>
                            <span>Номер: ' . htmlspecialchars($proposal['offer_number']) . '</span>
                        </div>
                        <div class="proposal-actions">
                            <a href="/proposals/' . $proposal['id'] . '" class="btn btn-sm">Просмотр</a>
                            <a href="/proposals/' . $proposal['id'] . '/edit" class="btn btn-sm">Редактировать</a>
                            <a href="/proposals/' . $proposal['id'] . '/pdf" class="btn btn-sm btn-primary" target="_blank">PDF</a>
                            <form method="POST" action="/proposals/' . $proposal['id'] . '" style="display: inline;">
                                <input type="hidden" name="_token" value="' . session('_token') . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Удалить КП?\')">Удалить</button>
                            </form>
                        </div>
                    </div>';
            }
        }

        echo '</div>

            </main>
        </body>
        </html>';
        break;

    case '/proposals/create':
        if (!session('user_id')) {
            redirect('/login');
        }

        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $template = trim($_POST['template_id'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $clientInfo = trim($_POST['client_info'] ?? '');

            // Валидация
            if (empty($title)) {
                $error = 'Название КП обязательно';
            } elseif (empty($template)) {
                $error = 'Выберите шаблон';
            } elseif (empty($date)) {
                $error = 'Укажите дату';
            } else {
                // Загружаем существующие предложения из файла
                $dataFile = __DIR__ . '/../storage/proposals.json';
                $existingProposals = [];
                if (file_exists($dataFile)) {
                    $existingProposals = json_decode(file_get_contents($dataFile), true) ?: [];
                }

                // Определяем следующий ID
                $maxId = 0;
                foreach ($existingProposals as $proposal) {
                    if (isset($proposal['id']) && $proposal['id'] > $maxId) {
                        $maxId = $proposal['id'];
                    }
                }
                $newId = $maxId + 1;

                // Создаем новое предложение
                $newProposal = [
                    'id' => $newId,
                    'user_id' => session('user_id'),
                    'title' => $title,
                    'template_id' => $template,
                    'offer_date' => $date,
                    'offer_number' => 'КП-' . date('Y') . '-' . str_pad($newId, 3, '0', STR_PAD_LEFT),
                    'client_info' => $clientInfo,
                    'status' => 'draft',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Добавляем к существующим предложениям
                $existingProposals[$newId] = $newProposal;

                // Сохраняем в сессии и файл
                session('proposals', $existingProposals);
                file_put_contents($dataFile, json_encode($existingProposals));

                $success = 'Коммерческое предложение "' . htmlspecialchars($title) . '" успешно создано!';

                // Очистить форму (редирект)
                header('Location: /proposals?success=' . urlencode($success));
                exit;
            }
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
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Создать коммерческое предложение</h1>
                    <a href="/proposals" class="btn btn-secondary">← Назад</a>
                </div>';

        if (!empty($success)) {
            echo '<div class="alert alert-success">' . $success . '</div>';
        }
        if (!empty($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

        echo '<div class="alert alert-info">
                    Мастер создания КП. Выберите шаблон и добавьте товары из каталога.
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label>Название КП</label>
                        <input type="text" name="title" placeholder="КП для ООО Ромашка" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Шаблон</label>
                            <select name="template_id">
                                <option>Стандартный шаблон</option>
                                <option>Минималистичный</option>
                                <option>Корпоративный</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Дата</label>
                            <input type="date" name="date" value="' . date('Y-m-d') . '" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Информация о клиенте</label>
                        <textarea name="client_info" rows="3" placeholder="Название компании, контактное лицо..."></textarea>
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
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Шаблоны коммерческих предложений</h1>
                    <a href="/templates/create" class="btn btn-primary">Создать шаблон</a>
                </div>

                <div class="templates-list">
                    <div class="template-card" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">🎨</div>
                        <div class="template-header">
                            <h3>Нет шаблонов</h3>
                        </div>
                        <p>Создайте свой первый шаблон для КП</p>
                        <div style="margin-top: 20px;">
                            <a href="/templates/create" class="btn btn-primary">+ Создать шаблон</a>
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
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/templates">Шаблоны</a>
                        ' . (session('user_role') === 'admin' ? '<a href="/admin">Админ</a>' : '') . '
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Создать шаблон КП</h1>
                    <a href="/templates" class="btn btn-secondary">← Назад</a>
                </div>

                <div class="alert alert-info">
                    HTML-редактор шаблонов. Используйте переменные: {{client.name}}, {{product.price}} и т.д.
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label>Название шаблона</label>
                        <input type="text" name="title" placeholder="Стандартный шаблон" required>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                        <textarea name="description" rows="2" placeholder="Краткое описание шаблона..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>HTML-контент</label>
                        <textarea name="body_html" rows="15" placeholder="<h1>Коммерческое предложение</h1>
<p>Уважаемый {{client.name}}!</p>
<p>Предлагаем вам наши услуги по цене {{product.price}} ₽</p>
<p>Товар: {{product.name}}</p>"></textarea>
                        <div class="hint">Поддерживаются переменные: {{client.name}}, {{product.name}}, {{product.price}}, {{date}}</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Сохранить шаблон</button>
                        <a href="/templates" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
        break;

    case '/admin':
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
                        <h3>👥 Пользователи</h3>
                        <div class="stat-number">2</div>
                    </div>
                    <div class="stat-card">
                        <h3>Товары</h3>
                        <div class="stat-number">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>КП</h3>
                        <div class="stat-number">0</div>
                    </div>
                </div>

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
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Администратор</td>
                                    <td>admin@example.com</td>
                                    <td><span class="badge badge-success">Админ</span></td>
                                    <td>
                                        <select onchange="changeRole(1, this.value)">
                                            <option value="admin" selected>Админ</option>
                                            <option value="user">Пользователь</option>
                                            <option value="guest">Гость</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Пользователь</td>
                                    <td>user@example.com</td>
                                    <td><span class="badge badge-secondary">Пользователь</span></td>
                                    <td>
                                        <select onchange="changeRole(2, this.value)">
                                            <option value="admin">Админ</option>
                                            <option value="user" selected>Пользователь</option>
                                            <option value="guest">Гость</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

            <script>
            function changeRole(userId, newRole) {
                alert(`Роль пользователя ${userId} изменена на: ${newRole}`);
                // Здесь будет AJAX запрос для изменения роли
            }
            </script>
        </body>
        </html>';
        break;

    case '/logout':
        session_destroy();
        redirect('/login');
        break;

    case '/debug':
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Отладка</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div style="padding: 20px; font-family: monospace;">
                <h1>🔧 Отладка</h1>
                <pre>';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        echo 'PHP Version: ' . PHP_VERSION . "\n";
        echo 'URI: ' . $uri . "\n";
        echo 'Session: ' . print_r($_SESSION ?? [], true) . "\n";
        echo 'Files in public/: ' . implode(', ', scandir(__DIR__)) . "\n";

        echo '</pre>
                <a href="/" class="btn btn-primary">← На главную</a>
            </div>
        </body>
        </html>';
        break;

    default:
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 - Страница не найдена</title>
            <link rel="stylesheet" href="/css/app.css">
        </head>
        <body>
            <div style="text-align: center; padding: 100px 20px;">
                <div style="font-size: 72px; margin-bottom: 20px;">😵</div>
                <h1>404 - Страница не найдена</h1>
                <p>Запрашиваемая страница не существует</p>
                <a href="/" class="btn btn-primary" style="margin-top: 20px;">← На главную</a>
            </div>
        </body>
        </html>';
        break;
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}

// Добавляем JavaScript для страниц, которые его используют
$pagesWithToast = ['/products', '/products/create', '/proposals', '/proposals/create'];

if (in_array($uri, $pagesWithToast)) {
    echo '<div id="toast-container"></div>
<script>
// Toast notifications
function showToast(message, type) {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = "toast " + type;
    toast.innerHTML =
        "<div class=\"toast-title\">" + (type === "success" ? "Успех" : type === "error" ? "Ошибка" : "Информация") + "</div>" +
        "<div class=\"toast-message\">" + message + "</div>";

    container.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(function() {
        toast.remove();
    }, 5000);
}

// Show toast for success messages
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const successMsg = urlParams.get("success");
    const errorMsg = urlParams.get("error");

    if (successMsg) {
        showToast(successMsg, "success");
    }
    if (errorMsg) {
        showToast(errorMsg, "error");
    }
});
</script>
</body>
</html>';
} else {
    echo '</body></html>';
}