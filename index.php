<?php

// Простое приложение для управления товарами
// Хранение в базе данных

// Подключение к БД (с fallback на JSON)
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=commercial_proposals;charset=utf8', 'appuser', 'apppassword');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $db = false; // Отключаем БД если ошибка
        }
    }
    return $db;
}

// Функции для работы с товарами
function getProducts($userId = null) {
    $db = getDB();
    if ($db) {
        try {
            if ($userId) {
                $stmt = $db->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$userId]);
            } else {
                $stmt = $db->query("SELECT * FROM products ORDER BY created_at DESC");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/products.json';
    if (!file_exists($dataFile)) {
        return [];
    }
    $products = json_decode(file_get_contents($dataFile), true);
    if (!is_array($products)) {
        return [];
    }

    if ($userId) {
        return array_filter($products, function($product) use ($userId) {
            return isset($product['user_id']) && $product['user_id'] == $userId;
        });
    }

    return $products;
}

function createProduct($data) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("INSERT INTO products (user_id, name, description, price, category, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $data['user_id'],
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['category'] ?? '',
                $data['image'] ?? '/css/placeholder-product.svg'
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/products.json';
    $products = [];
    if (file_exists($dataFile)) {
        $products = json_decode(file_get_contents($dataFile), true) ?: [];
    }

    $newId = 1;
    if (!empty($products)) {
        $maxId = max(array_column($products, 'id'));
        $newId = $maxId + 1;
    }

    $products[] = [
        'id' => $newId,
        'user_id' => $data['user_id'],
        'name' => $data['name'],
        'description' => $data['description'] ?? '',
        'price' => $data['price'],
        'category' => $data['category'] ?? '',
        'image' => $data['image'] ?? '/css/placeholder-product.svg',
        'created_at' => date('Y-m-d H:i:s')
    ];

    file_put_contents($dataFile, json_encode($products));
    return $newId;
}

function getProduct($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/products.json';
    if (file_exists($dataFile)) {
        $products = json_decode(file_get_contents($dataFile), true) ?: [];
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
    }
    return null;
}

function updateProduct($id, $data) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['category'] ?? '',
                $data['image'] ?? '/css/placeholder-product.svg',
                $id
            ]);
            return true;
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/products.json';
    if (file_exists($dataFile)) {
        $products = json_decode(file_get_contents($dataFile), true) ?: [];
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product['name'] = $data['name'];
                $product['description'] = $data['description'] ?? '';
                $product['price'] = $data['price'];
                $product['category'] = $data['category'] ?? '';
                $product['image'] = $data['image'] ?? '/css/placeholder-product.svg';
                break;
            }
        }
        file_put_contents($dataFile, json_encode($products));
        return true;
    }
    return false;
}

function uploadProductImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Проверяем тип файла
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }

    // Проверяем размер файла (макс 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    // Создаем папку для изображений если её нет
    $uploadDir = __DIR__ . '/uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Генерируем уникальное имя файла
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_', true) . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Перемещаем файл
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return '/uploads/products/' . $filename;
    }

    return null;
}

function getProductImage($imagePath) {
    if (!$imagePath || $imagePath === '/css/placeholder-product.svg') {
        // Используем сервис для генерации изображений товаров
        return 'https://picsum.photos/300/200?random=' . rand(1, 1000);
    }
    return $imagePath;
}

// Обработка маршрутов
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Простая демо-версия без аутентификации
$userId = 1; // Фиксированный пользователь для демо


switch ($uri) {
    case '':
    case '/':
        header('Location: /products');
        exit;

    case '/login':
        header('Location: /products');
        exit;

    case '/dashboard':
        // Подсчет товаров
        $userProducts = getProducts($userId);
        $userProductsCount = count($userProducts);

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
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Добро пожаловать в демо-версию!</h1>
                </div>

                <div class="dashboard-metrics">
                    <div class="metric-card">
                        <div class="metric-icon">📦</div>
                        <div class="metric-value">' . $userProductsCount . '</div>
                        <div class="metric-label">Товаров</div>
                    </div>
                    </div>

                <div class="alert alert-success">
                    Демо-версия работает! Товары хранятся в JSON файле.
                </div>
            </main>
        </body>
        </html>';
        break;

    case '/products':
        // Получить товары пользователя
        $userProducts = getProducts($userId);

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
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h1>Каталог товаров</h1>
                    <a href="/products/create" class="btn btn-primary" style="margin: 0;">+ Добавить товар</a>
                </div>';

        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
        }

        echo '<div class="products-grid">';

        if (empty($userProducts)) {
            echo '<div class="product-card" style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
                        <div class="product-title">Каталог пуст</div>
                        <div class="product-description">Добавьте первый товар</div>
                    </div>';
        } else {
            foreach ($userProducts as $product) {
                echo '<div class="product-card">
                        <div class="product-image-container">
                            <img src="' . htmlspecialchars(getProductImage($product['image'])) . '" alt="' . htmlspecialchars($product['name']) . '" class="product-image">
                        </div>
                        <div class="product-info">
                            <div class="product-title">' . htmlspecialchars($product['name']) . '</div>
                            <div class="product-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                            ' . (!empty($product['description']) ? '<div class="product-description">' . htmlspecialchars(substr($product['description'], 0, 100)) . '</div>' : '') . '
                            <div class="product-category" style="font-size: 12px; color: #666; margin-top: 8px;">' . htmlspecialchars($product['category'] ?? 'Без категории') . '</div>
                        </div>
                        <div class="product-actions" style="margin-top: 16px; display: flex; gap: 8px;">
                            <a href="/products/' . $product['id'] . '/edit" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">✏️ Редактировать</a>
                        </div>
                    </div>';
            }
        }

        echo '</div>
        </main>
        </body>
        </html>';
        break;

    case '/products/create':

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Обрабатываем загруженное изображение
            $imagePath = '/css/placeholder-product.svg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadedImage = uploadProductImage($_FILES['image']);
                if ($uploadedImage) {
                    $imagePath = $uploadedImage;
                } else {
                    $error = 'Ошибка загрузки изображения. Проверьте формат (JPEG, PNG, GIF, WebP) и размер (до 5MB).';
                }
            }

            if (empty($name)) {
                $error = 'Название товара обязательно';
            } elseif ($price <= 0) {
                $error = 'Цена должна быть больше 0';
            } elseif (!isset($error)) {
                // Сохраняем товар
                try {
                    createProduct([
                        'user_id' => $userId,
                            'name' => $name,
                            'price' => $price,
                            'category' => $category,
                            'description' => $description,
                        'image' => $imagePath
                    ]);
                    header('Location: /products?success=' . urlencode('Товар "' . $name . '" успешно добавлен!'));
                    exit;
                } catch (Exception $e) {
                    $error = 'Ошибка сохранения товара: ' . $e->getMessage();
                }
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

                    <div class="form-group">
                        <label>Изображение товара (необязательно)</label>
                        <input type="file" name="image" accept="image/*">
                        <small style="color: #b0b0b0; font-size: 12px;">Поддерживаемые форматы: JPEG, PNG, GIF, WebP. Максимальный размер: 5MB.</small>
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
        header('Location: /products');
        exit;

    default:
        // Проверяем, является ли это маршрутом редактирования товара /products/{id}/edit
        if (preg_match('#^/products/(\d+)/edit$#', $uri, $matches)) {
            $productId = (int)$matches[1];
            $product = getProduct($productId);

            if (!$product) {
                http_response_code(404);
        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Товар не найден</title>
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
                        <div style="text-align: center; margin-top: 100px;">
                            <h1>Товар не найден</h1>
                            <p>Запрашиваемый товар не существует.</p>
                            <a href="/products" class="btn btn-primary">К товарам</a>
                        </div>
            </main>
        </body>
        </html>';
        break;
        }

        $error = '';
            $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Обрабатываем загруженное изображение
            $imagePath = $product['image']; // Сохраняем существующее изображение по умолчанию
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadedImage = uploadProductImage($_FILES['image']);
                if ($uploadedImage) {
                    $imagePath = $uploadedImage;
            } else {
                    $error = 'Ошибка загрузки изображения. Проверьте формат (JPEG, PNG, GIF, WebP) и размер (до 5MB).';
                }
            }

            if (empty($name)) {
                $error = 'Название товара обязательно';
            } elseif ($price <= 0) {
                $error = 'Цена должна быть больше 0';
            } elseif (!isset($error)) {
                // Обновляем товар
                try {
                    updateProduct($productId, [
                        'name' => $name,
                        'price' => $price,
                        'category' => $category,
                        'description' => $description,
                        'image' => $imagePath
                    ]);
                    header('Location: /products?success=' . urlencode('Товар "' . $name . '" успешно обновлен!'));
                exit;
                } catch (Exception $e) {
                    $error = 'Ошибка обновления товара: ' . $e->getMessage();
                }
            }
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Редактировать товар</title>
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
                        <h1>Редактировать товар</h1>
                        <a href="/products" class="btn btn-secondary">← Назад</a>
                </div>';

        if (!empty($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

            echo '<form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                            <label>Название товара</label>
                            <input type="text" name="name" value="' . htmlspecialchars($product['name']) . '" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                                <label>Цена (₽)</label>
                                <input type="number" name="price" step="0.01" value="' . htmlspecialchars($product['price']) . '" required>
                        </div>
                        <div class="form-group">
                                <label>Категория</label>
                                <select name="category">
                                    <option value="">Без категории</option>
                                    <option value="Электроника"' . ($product['category'] === 'Электроника' ? ' selected' : '') . '>Электроника</option>
                                    <option value="Оборудование"' . ($product['category'] === 'Оборудование' ? ' selected' : '') . '>Оборудование</option>
                                    <option value="Программное обеспечение"' . ($product['category'] === 'Программное обеспечение' ? ' selected' : '') . '>Программное обеспечение</option>
                                    <option value="Услуги"' . ($product['category'] === 'Услуги' ? ' selected' : '') . '>Услуги</option>
                                </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Описание</label>
                            <textarea name="description" rows="4">' . htmlspecialchars($product['description'] ?? '') . '</textarea>
                    </div>

                    <div class="form-group">
                        <label>Изображение товара</label>
                        <input type="file" name="image" accept="image/*">
                        <small style="color: #b0b0b0; font-size: 12px;">Оставьте пустым, чтобы сохранить текущее изображение. Поддерживаемые форматы: JPEG, PNG, GIF, WebP. Максимальный размер: 5MB.</small>
                    </div>

                    <div class="form-actions">
                            <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
                            <a href="/products" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </main>
        </body>
        </html>';
        break;
        }

        // 404 - Страница не найдена
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
                <div style="text-align: center; margin-top: 100px;">
                    <h1>404 - Страница не найдена</h1>
                    <p>Запрашиваемая страница не существует.</p>
                    <a href="/" class="btn btn-primary">На главную</a>
                </div>
            </main>
        </body>
        </html>';
        break;
}