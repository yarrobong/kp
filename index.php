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

function deleteProduct($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/products.json';
    if (file_exists($dataFile)) {
        $products = json_decode(file_get_contents($dataFile), true) ?: [];
        $newProducts = [];
        foreach ($products as $product) {
            if ($product['id'] != $id) {
                $newProducts[] = $product;
            }
        }
        file_put_contents($dataFile, json_encode($newProducts));
        return true;
    }
    return false;
}

function uploadProductImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Проверяем тип файла по расширению и MIME типу
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Проверяем расширение файла
    if (!in_array($extension, $allowedExtensions)) {
        return null;
    }

    // Проверяем MIME тип (если указан)
    if (!empty($file['type']) && !in_array($file['type'], $allowedTypes)) {
        return null;
    }

    // Проверяем размер файла (макс 5MB)
    if ($file['size'] > 5 * 1024 * 1024 || $file['size'] <= 0) {
        return null;
    }

    // Проверяем, что файл действительно является изображением
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo || !in_array($imageInfo['mime'], $allowedTypes)) {
        return null;
    }

    // Создаем папку для изображений если её нет
    $uploadDir = __DIR__ . '/uploads/products/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return null;
        }
    }

    // Генерируем уникальное имя файла
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

// Функции для работы с коммерческими предложениями
function getProposals($userId = null) {
    $db = getDB();
    if ($db) {
        try {
            if ($userId) {
                $stmt = $db->prepare("SELECT * FROM proposals WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$userId]);
            } else {
                $stmt = $db->query("SELECT * FROM proposals ORDER BY created_at DESC");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/proposals.json';
    if (!file_exists($dataFile)) {
        return [];
    }
    $proposals = json_decode(file_get_contents($dataFile), true);
    if (!is_array($proposals)) {
        return [];
    }

    if ($userId) {
        return array_filter($proposals, function($proposal) use ($userId) {
            return isset($proposal['user_id']) && $proposal['user_id'] == $userId;
        });
    }

    return $proposals;
}

function createProposal($data) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("INSERT INTO proposals (user_id, template_id, title, offer_number, offer_date, client_info, status, total, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $data['user_id'],
                $data['template_id'] ?? null,
                $data['title'],
                $data['offer_number'],
                $data['offer_date'],
                $data['client_info'],
                $data['status'] ?? 'draft',
                $data['total'] ?? 0
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/proposals.json';
    $proposals = [];
    if (file_exists($dataFile)) {
        $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
    }

    $newId = 1;
    if (!empty($proposals)) {
        $maxId = max(array_column($proposals, 'id'));
        $newId = $maxId + 1;
    }

    $proposals[] = [
        'id' => $newId,
        'user_id' => $data['user_id'],
        'template_id' => $data['template_id'] ?? null,
        'title' => $data['title'],
        'offer_number' => $data['offer_number'],
        'offer_date' => $data['offer_date'],
        'client_info' => $data['client_info'],
        'status' => $data['status'] ?? 'draft',
        'total' => $data['total'] ?? 0,
        'created_at' => date('Y-m-d H:i:s')
    ];

    file_put_contents($dataFile, json_encode($proposals));
    return $newId;
}

function getProposal($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM proposals WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/proposals.json';
    if (file_exists($dataFile)) {
        $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
        foreach ($proposals as $proposal) {
            if ($proposal['id'] == $id) {
                return $proposal;
            }
        }
    }
    return null;
}

function updateProposal($id, $data) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE proposals SET title = ?, offer_number = ?, offer_date = ?, client_info = ?, status = ?, total = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $data['title'],
                $data['offer_number'],
                $data['offer_date'],
                $data['client_info'],
                $data['status'] ?? 'draft',
                $data['total'] ?? 0,
                $id
            ]);
            return true;
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/proposals.json';
    if (file_exists($dataFile)) {
        $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
        foreach ($proposals as &$proposal) {
            if ($proposal['id'] == $id) {
                $proposal['title'] = $data['title'];
                $proposal['offer_number'] = $data['offer_number'];
                $proposal['offer_date'] = $data['offer_date'];
                $proposal['client_info'] = $data['client_info'];
                $proposal['status'] = $data['status'] ?? 'draft';
                $proposal['total'] = $data['total'] ?? 0;
                break;
            }
        }
        file_put_contents($dataFile, json_encode($proposals));
        return true;
    }
    return false;
}

function deleteProposal($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM proposals WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = __DIR__ . '/proposals.json';
    if (file_exists($dataFile)) {
        $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
        $newProposals = [];
        foreach ($proposals as $proposal) {
            if ($proposal['id'] != $id) {
                $newProposals[] = $proposal;
            }
        }
        file_put_contents($dataFile, json_encode($newProposals));
        return true;
    }
    return false;
}

function generateOfferNumber() {
    $date = date('Ymd');
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM proposals WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $count = $stmt->fetchColumn();
        } catch (Exception $e) {
            $count = 0;
        }
    } else {
        // Fallback на JSON
        $dataFile = __DIR__ . '/proposals.json';
        $count = 0;
        if (file_exists($dataFile)) {
            $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
            $today = date('Y-m-d');
            foreach ($proposals as $proposal) {
                if (strpos($proposal['created_at'], $today) === 0) {
                    $count++;
                }
            }
        }
    }
    return 'KP-' . $date . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

// Обработка маршрутов (только если это не CLI или прямой вызов)
if (php_sapi_name() !== 'cli' && !defined('CLI_MODE')) {
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

    case '/proposals':
        // Получить коммерческие предложения пользователя
        $userProposals = getProposals($userId);

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
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h1>Коммерческие предложения</h1>
                    <a href="/proposals/create" class="btn btn-primary" style="margin: 0;">+ Создать КП</a>
                </div>';

        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
        }

        echo '<div class="proposals-grid">';

        if (empty($userProposals)) {
            echo '<div class="proposal-card" style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">
                        <div style="font-size: 48px; margin-bottom: 16px;">📄</div>
                        <div class="proposal-title">Нет коммерческих предложений</div>
                        <div class="proposal-description">Создайте первое предложение</div>
                    </div>';
        } else {
            foreach ($userProposals as $proposal) {
                $clientInfo = json_decode($proposal['client_info'], true);
                $clientName = $clientInfo['client_name'] ?? 'Без имени';
                $statusClass = '';
                $statusText = '';

                switch ($proposal['status']) {
                    case 'draft':
                        $statusClass = 'status-draft';
                        $statusText = 'Черновик';
                        break;
                    case 'sent':
                        $statusClass = 'status-sent';
                        $statusText = 'Отправлено';
                        break;
                    case 'accepted':
                        $statusClass = 'status-accepted';
                        $statusText = 'Принято';
                        break;
                    case 'rejected':
                        $statusClass = 'status-rejected';
                        $statusText = 'Отклонено';
                        break;
                }

                echo '<div class="proposal-card">
                        <div class="proposal-header">
                            <div class="proposal-title">' . htmlspecialchars($proposal['title']) . '</div>
                            <div class="proposal-number">№ ' . htmlspecialchars($proposal['offer_number']) . '</div>
                        </div>
                        <div class="proposal-info">
                            <div class="proposal-client">Клиент: ' . htmlspecialchars($clientName) . '</div>
                            <div class="proposal-date">Дата: ' . date('d.m.Y', strtotime($proposal['offer_date'])) . '</div>
                            <div class="proposal-total">Сумма: ₽ ' . number_format($proposal['total'], 2, ',', ' ') . '</div>
                            <div class="proposal-status ' . $statusClass . '">' . $statusText . '</div>
                        </div>
                        <div class="proposal-actions" style="margin-top: 16px; display: flex; gap: 8px;">
                            <a href="/proposals/' . $proposal['id'] . '" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">👁️ Просмотр</a>
                            <a href="/proposals/' . $proposal['id'] . '/edit" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">✏️ Редактировать</a>
                            <form method="POST" action="/proposals/' . $proposal['id'] . '/delete" style="display: inline;" onsubmit="return confirm(\'Вы уверены, что хотите удалить это предложение?\')">
                                <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 6px 12px;">🗑️ Удалить</button>
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
                        <a href="/proposals">КП</a>
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
                            <form method="POST" action="/products/' . $product['id'] . '/delete" style="display: inline;" onsubmit="return confirm(\'Вы уверены, что хотите удалить этот товар?\')">
                                <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 6px 12px;">🗑️ Удалить</button>
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
        $error = '';
        $success = '';

        // Получить все товары для выбора
        $allProducts = getProducts($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientName = trim($_POST['client_name'] ?? '');
            $selectedProducts = $_POST['selected_products'] ?? [];
            $offerDate = $_POST['offer_date'] ?? date('Y-m-d');

            if (empty($clientName)) {
                $error = 'Имя клиента обязательно';
            } elseif (empty($selectedProducts)) {
                $error = 'Выберите хотя бы один товар';
            } else {
                // Рассчитать общую сумму и подготовить данные
                $total = 0;
                $proposalProducts = [];

                foreach ($selectedProducts as $productId) {
                    $product = getProduct($productId);
                    if ($product) {
                        $proposalProducts[] = $product;
                        $total += $product['price'];
                    }
                }

                // Создать предложение
                try {
                    $proposalData = [
                        'user_id' => $userId,
                        'title' => 'Коммерческое предложение для ' . $clientName,
                        'offer_number' => generateOfferNumber(),
                        'offer_date' => $offerDate,
                        'client_info' => json_encode([
                            'client_name' => $clientName,
                            'products' => $proposalProducts
                        ]),
                        'status' => 'draft',
                        'total' => $total
                    ];

                    $proposalId = createProposal($proposalData);
                    header('Location: /proposals/' . $proposalId);
                    exit;
                } catch (Exception $e) {
                    $error = 'Ошибка создания предложения: ' . $e->getMessage();
                }
            }
        }

        echo '<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Создать коммерческое предложение</title>
            <link rel="stylesheet" href="/css/app.css">
            <style>
                .products-selection {
                    max-height: 400px;
                    overflow-y: auto;
                    border: 1px solid #333333;
                    border-radius: 8px;
                    padding: 16px;
                    background: #1e1e1e;
                }
                .product-selection-item {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    padding: 12px;
                    border: 1px solid #333333;
                    border-radius: 8px;
                    margin-bottom: 8px;
                    background: #2d2d2d;
                }
                .product-selection-item img {
                    width: 60px;
                    height: 60px;
                    object-fit: cover;
                    border-radius: 4px;
                }
                .product-selection-info {
                    flex: 1;
                }
                .product-selection-title {
                    font-weight: 600;
                    color: #ffffff;
                    margin-bottom: 4px;
                }
                .product-selection-price {
                    color: #1976d2;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <nav class="navbar">
                <div class="container">
                    <a href="/" class="navbar-brand">КП Генератор</a>
                    <div class="navbar-menu">
                        <a href="/dashboard">Панель</a>
                        <a href="/products">Товары</a>
                        <a href="/proposals">КП</a>
                        <a href="/logout">Выход</a>
                    </div>
                </div>
            </nav>

            <main class="container">
                <div class="page-header">
                    <h1>Создать коммерческое предложение</h1>
                    <a href="/proposals" class="btn btn-secondary">← Назад</a>
                </div>';

        if (!empty($error)) {
            echo '<div class="alert alert-error">' . $error . '</div>';
        }

        echo '<form method="POST">
                    <div class="form-group">
                        <label>Имя клиента</label>
                        <input type="text" name="client_name" placeholder="ООО \"Ромашка\"" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Дата предложения</label>
                            <input type="date" name="offer_date" value="' . date('Y-m-d') . '" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Выберите товары</label>
                        <div class="products-selection">';

        if (empty($allProducts)) {
            echo '<p style="color: #b0b0b0; text-align: center; padding: 40px;">Сначала добавьте товары в каталог</p>';
        } else {
            foreach ($allProducts as $product) {
                echo '<div class="product-selection-item">
                                <input type="checkbox" name="selected_products[]" value="' . $product['id'] . '" id="product_' . $product['id'] . '">
                                <label for="product_' . $product['id'] . '" style="display: flex; align-items: center; gap: 12px; width: 100%; cursor: pointer;">
                                    <img src="' . htmlspecialchars(getProductImage($product['image'])) . '" alt="' . htmlspecialchars($product['name']) . '">
                                    <div class="product-selection-info">
                                        <div class="product-selection-title">' . htmlspecialchars($product['name']) . '</div>
                                        <div class="product-selection-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                                        ' . (!empty($product['description']) ? '<div style="color: #b0b0b0; font-size: 12px; margin-top: 4px;">' . htmlspecialchars(substr($product['description'], 0, 80)) . '...</div>' : '') . '
                                    </div>
                                </label>
                            </div>';
            }
        }

        echo '</div>
                        <small style="color: #b0b0b0; font-size: 12px; margin-top: 8px; display: block;">Выберите товары, которые будут включены в коммерческое предложение</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">📄 Сформировать КП</button>
                        <a href="/proposals" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
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
            }


            if (empty($error)) {
                // Сохраняем товар
                try {
                    $result = createProduct([
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
        // Проверяем, является ли это маршрутом удаления товара /products/{id}/delete
        if (preg_match('#^/products/(\d+)/delete$#', $uri, $matches)) {
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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    // Удаляем файл изображения, если он существует
                    if ($product['image'] && $product['image'] !== '/css/placeholder-product.svg' && file_exists(__DIR__ . $product['image'])) {
                        unlink(__DIR__ . $product['image']);
                    }

                    // Удаляем товар из базы данных
                    deleteProduct($productId);

                    header('Location: /products?success=' . urlencode('Товар "' . $product['name'] . '" успешно удален!'));
                    exit;
                } catch (Exception $e) {
                    $error = 'Ошибка удаления товара: ' . $e->getMessage();
                }
            }
            break;
        }

        // Проверяем, является ли это маршрутом просмотра предложения /proposals/{id}
        if (preg_match('#^/proposals/(\d+)$#', $uri, $matches)) {
            $proposalId = (int)$matches[1];
            $proposal = getProposal($proposalId);

            if (!$proposal) {
                http_response_code(404);
                echo '<!DOCTYPE html>
                <html lang="ru">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Предложение не найдено</title>
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
                                <a href="/logout">Выход</a>
                            </div>
                        </div>
                    </nav>

                    <main class="container">
                        <div style="text-align: center; margin-top: 100px;">
                            <h1>Предложение не найдено</h1>
                            <p>Запрашиваемое предложение не существует.</p>
                            <a href="/proposals" class="btn btn-primary">К предложениям</a>
                        </div>
                    </main>
                </body>
                </html>';
                break;
            }

            $clientInfo = json_decode($proposal['client_info'], true);
            $clientName = $clientInfo['client_name'] ?? 'Без имени';
            $proposalProducts = $clientInfo['products'] ?? [];

            echo '<!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . htmlspecialchars($proposal['title']) . '</title>
                <link rel="stylesheet" href="/css/app.css">
                <style>
                    .proposal-header {
                        text-align: center;
                        margin-bottom: 40px;
                        padding: 32px;
                        background: #1e1e1e;
                        border-radius: 12px;
                        border: 1px solid #333333;
                    }
                    .proposal-company {
                        font-size: 24px;
                        font-weight: 700;
                        color: #ffffff;
                        margin-bottom: 8px;
                    }
                    .proposal-intro {
                        font-size: 18px;
                        color: #b0b0b0;
                        margin-bottom: 24px;
                    }
                    .proposal-details {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                        gap: 24px;
                        margin-bottom: 32px;
                    }
                    .proposal-detail {
                        background: #1e1e1e;
                        padding: 16px;
                        border-radius: 8px;
                        border: 1px solid #333333;
                    }
                    .proposal-detail-label {
                        font-size: 12px;
                        color: #b0b0b0;
                        text-transform: uppercase;
                        margin-bottom: 4px;
                    }
                    .proposal-detail-value {
                        font-size: 16px;
                        color: #ffffff;
                        font-weight: 600;
                    }
                    .products-section {
                        margin-top: 40px;
                    }
                    .products-section h2 {
                        font-size: 24px;
                        margin-bottom: 24px;
                        color: #ffffff;
                    }
                    .proposal-product {
                        display: flex;
                        gap: 20px;
                        padding: 24px;
                        background: #1e1e1e;
                        border: 1px solid #333333;
                        border-radius: 12px;
                        margin-bottom: 16px;
                    }
                    .proposal-product-image {
                        width: 120px;
                        height: 120px;
                        border-radius: 8px;
                        object-fit: cover;
                        flex-shrink: 0;
                    }
                    .proposal-product-info {
                        flex: 1;
                    }
                    .proposal-product-title {
                        font-size: 20px;
                        font-weight: 600;
                        color: #ffffff;
                        margin-bottom: 8px;
                    }
                    .proposal-product-description {
                        color: #b0b0b0;
                        margin-bottom: 16px;
                        line-height: 1.5;
                    }
                    .proposal-product-price {
                        font-size: 24px;
                        font-weight: 700;
                        color: #1976d2;
                    }
                    .proposal-total-section {
                        margin-top: 40px;
                        padding: 24px;
                        background: #1e1e1e;
                        border: 1px solid #333333;
                        border-radius: 12px;
                        text-align: right;
                    }
                    .proposal-total-label {
                        font-size: 18px;
                        color: #b0b0b0;
                        margin-bottom: 8px;
                    }
                    .proposal-total-value {
                        font-size: 32px;
                        font-weight: 700;
                        color: #1976d2;
                    }
                    .proposal-actions {
                        margin-top: 32px;
                        display: flex;
                        gap: 12px;
                        justify-content: center;
                    }
                    @media print {
                        .navbar, .proposal-actions {
                            display: none !important;
                        }
                        body {
                            background: white !important;
                            color: black !important;
                        }
                        .container {
                            max-width: none !important;
                            padding: 0 !important;
                        }
                    }
                </style>
            </head>
            <body>
                <nav class="navbar">
                    <div class="container">
                        <a href="/" class="navbar-brand">КП Генератор</a>
                        <div class="navbar-menu">
                            <a href="/dashboard">Панель</a>
                            <a href="/products">Товары</a>
                            <a href="/proposals">КП</a>
                            <a href="/logout">Выход</a>
                        </div>
                    </div>
                </nav>

                <main class="container">
                    <div class="proposal-header">
                        <div class="proposal-company">Наша Компания</div>
                        <div class="proposal-intro">предоставляет Вам следующее коммерческое предложение</div>
                        <h1>' . htmlspecialchars($proposal['title']) . '</h1>
                    </div>

                    <div class="proposal-details">
                        <div class="proposal-detail">
                            <div class="proposal-detail-label">Номер предложения</div>
                            <div class="proposal-detail-value">' . htmlspecialchars($proposal['offer_number']) . '</div>
                        </div>
                        <div class="proposal-detail">
                            <div class="proposal-detail-label">Дата</div>
                            <div class="proposal-detail-value">' . date('d.m.Y', strtotime($proposal['offer_date'])) . '</div>
                        </div>
                        <div class="proposal-detail">
                            <div class="proposal-detail-label">Клиент</div>
                            <div class="proposal-detail-value">' . htmlspecialchars($clientName) . '</div>
                        </div>
                        <div class="proposal-detail">
                            <div class="proposal-detail-label">Статус</div>
                            <div class="proposal-detail-value">' . ($proposal['status'] === 'draft' ? 'Черновик' : ($proposal['status'] === 'sent' ? 'Отправлено' : ($proposal['status'] === 'accepted' ? 'Принято' : 'Отклонено'))) . '</div>
                        </div>
                    </div>

                    <div class="products-section">
                        <h2>Предлагаемые товары и услуги</h2>';

            if (!empty($proposalProducts)) {
                foreach ($proposalProducts as $product) {
                    echo '<div class="proposal-product">
                                <img src="' . htmlspecialchars(getProductImage($product['image'])) . '" alt="' . htmlspecialchars($product['name']) . '" class="proposal-product-image">
                                <div class="proposal-product-info">
                                    <div class="proposal-product-title">' . htmlspecialchars($product['name']) . '</div>
                                    ' . (!empty($product['description']) ? '<div class="proposal-product-description">' . htmlspecialchars($product['description']) . '</div>' : '') . '
                                    <div class="proposal-product-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                                </div>
                            </div>';
                }
            }

            echo '</div>

                    <div class="proposal-total-section">
                        <div class="proposal-total-label">Общая сумма предложения</div>
                        <div class="proposal-total-value">₽ ' . number_format($proposal['total'], 2, ',', ' ') . '</div>
                    </div>

                    <div class="proposal-actions">
                        <a href="/proposals" class="btn btn-secondary">← К списку предложений</a>
                        <a href="/proposals/' . $proposal['id'] . '/edit" class="btn btn-secondary">✏️ Редактировать</a>
                        <button onclick="window.print()" class="btn btn-primary">🖨️ Печать</button>
                        <form method="POST" action="/proposals/' . $proposal['id'] . '/delete" style="display: inline;" onsubmit="return confirm(\'Вы уверены, что хотите удалить это предложение?\')">
                            <button type="submit" class="btn btn-danger">🗑️ Удалить</button>
                        </form>
                    </div>
                </main>
            </body>
            </html>';
            break;
        }

        // Проверяем, является ли это маршрутом редактирования предложения /proposals/{id}/edit
        if (preg_match('#^/proposals/(\d+)/edit$#', $uri, $matches)) {
            $proposalId = (int)$matches[1];
            $proposal = getProposal($proposalId);

            if (!$proposal) {
                http_response_code(404);
                echo '<!DOCTYPE html>
                <html lang="ru">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Предложение не найдено</title>
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
                                <a href="/logout">Выход</a>
                            </div>
                        </div>
                    </nav>

                    <main class="container">
                        <div style="text-align: center; margin-top: 100px;">
                            <h1>Предложение не найдено</h1>
                            <p>Запрашиваемое предложение не существует.</p>
                            <a href="/proposals" class="btn btn-primary">К предложениям</a>
                        </div>
                    </main>
                </body>
                </html>';
                break;
            }

            $clientInfo = json_decode($proposal['client_info'], true);
            $clientName = $clientInfo['client_name'] ?? '';
            $selectedProducts = array_column($clientInfo['products'] ?? [], 'id');

            $error = '';
            $success = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $clientName = trim($_POST['client_name'] ?? '');
                $selectedProducts = $_POST['selected_products'] ?? [];
                $offerDate = $_POST['offer_date'] ?? $proposal['offer_date'];
                $status = $_POST['status'] ?? $proposal['status'];

                if (empty($clientName)) {
                    $error = 'Имя клиента обязательно';
                } elseif (empty($selectedProducts)) {
                    $error = 'Выберите хотя бы один товар';
                } else {
                    // Получить все товары пользователя для расчета суммы
                    $allProducts = getProducts($userId);

                    // Рассчитать общую сумму и подготовить данные
                    $total = 0;
                    $proposalProducts = [];

                    foreach ($selectedProducts as $productId) {
                        $product = getProduct($productId);
                        if ($product) {
                            $proposalProducts[] = $product;
                            $total += $product['price'];
                        }
                    }

                    // Обновить предложение
                    try {
                        $proposalData = [
                            'title' => 'Коммерческое предложение для ' . $clientName,
                            'offer_number' => $proposal['offer_number'], // Не меняем номер
                            'offer_date' => $offerDate,
                            'client_info' => json_encode([
                                'client_name' => $clientName,
                                'products' => $proposalProducts
                            ]),
                            'status' => $status,
                            'total' => $total
                        ];

                        updateProposal($proposalId, $proposalData);
                        header('Location: /proposals/' . $proposalId);
                        exit;
                    } catch (Exception $e) {
                        $error = 'Ошибка обновления предложения: ' . $e->getMessage();
                    }
                }
            }

            // Получить все товары для выбора
            $allProducts = getProducts($userId);

            echo '<!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Редактировать предложение</title>
                <link rel="stylesheet" href="/css/app.css">
                <style>
                    .products-selection {
                        max-height: 400px;
                        overflow-y: auto;
                        border: 1px solid #333333;
                        border-radius: 8px;
                        padding: 16px;
                        background: #1e1e1e;
                    }
                    .product-selection-item {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        padding: 12px;
                        border: 1px solid #333333;
                        border-radius: 8px;
                        margin-bottom: 8px;
                        background: #2d2d2d;
                    }
                    .product-selection-item.selected {
                        border-color: #1976d2;
                        background: #1e3a5f;
                    }
                    .product-selection-item img {
                        width: 60px;
                        height: 60px;
                        object-fit: cover;
                        border-radius: 4px;
                    }
                    .product-selection-info {
                        flex: 1;
                    }
                    .product-selection-title {
                        font-weight: 600;
                        color: #ffffff;
                        margin-bottom: 4px;
                    }
                    .product-selection-price {
                        color: #1976d2;
                        font-weight: 600;
                    }
                </style>
            </head>
            <body>
                <nav class="navbar">
                    <div class="container">
                        <a href="/" class="navbar-brand">КП Генератор</a>
                        <div class="navbar-menu">
                            <a href="/dashboard">Панель</a>
                            <a href="/products">Товары</a>
                            <a href="/proposals">КП</a>
                            <a href="/logout">Выход</a>
                        </div>
                    </div>
                </nav>

                <main class="container">
                    <div class="page-header">
                        <h1>Редактировать предложение</h1>
                        <a href="/proposals/' . $proposal['id'] . '" class="btn btn-secondary">← Назад</a>
                    </div>';

            if (!empty($error)) {
                echo '<div class="alert alert-error">' . $error . '</div>';
            }

            echo '<form method="POST">
                        <div class="form-group">
                            <label>Имя клиента</label>
                            <input type="text" name="client_name" value="' . htmlspecialchars($clientName) . '" placeholder="ООО \"Ромашка\"" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Дата предложения</label>
                                <input type="date" name="offer_date" value="' . htmlspecialchars($proposal['offer_date']) . '" required>
                            </div>
                            <div class="form-group">
                                <label>Статус</label>
                                <select name="status">
                                    <option value="draft"' . ($proposal['status'] === 'draft' ? ' selected' : '') . '>Черновик</option>
                                    <option value="sent"' . ($proposal['status'] === 'sent' ? ' selected' : '') . '>Отправлено</option>
                                    <option value="accepted"' . ($proposal['status'] === 'accepted' ? ' selected' : '') . '>Принято</option>
                                    <option value="rejected"' . ($proposal['status'] === 'rejected' ? ' selected' : '') . '>Отклонено</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Выберите товары</label>
                            <div class="products-selection">';

            if (empty($allProducts)) {
                echo '<p style="color: #b0b0b0; text-align: center; padding: 40px;">Сначала добавьте товары в каталог</p>';
            } else {
                foreach ($allProducts as $product) {
                    $isSelected = in_array($product['id'], $selectedProducts);
                    echo '<div class="product-selection-item' . ($isSelected ? ' selected' : '') . '">
                                    <input type="checkbox" name="selected_products[]" value="' . $product['id'] . '" id="product_' . $product['id'] . '"' . ($isSelected ? ' checked' : '') . '>
                                    <label for="product_' . $product['id'] . '" style="display: flex; align-items: center; gap: 12px; width: 100%; cursor: pointer;">
                                        <img src="' . htmlspecialchars(getProductImage($product['image'])) . '" alt="' . htmlspecialchars($product['name']) . '">
                                        <div class="product-selection-info">
                                            <div class="product-selection-title">' . htmlspecialchars($product['name']) . '</div>
                                            <div class="product-selection-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                                            ' . (!empty($product['description']) ? '<div style="color: #b0b0b0; font-size: 12px; margin-top: 4px;">' . htmlspecialchars(substr($product['description'], 0, 80)) . '...</div>' : '') . '
                                        </div>
                                    </label>
                                </div>';
                }
            }

            echo '</div>
                            <small style="color: #b0b0b0; font-size: 12px; margin-top: 8px; display: block;">Выберите товары, которые будут включены в коммерческое предложение</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
                            <a href="/proposals/' . $proposal['id'] . '" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </main>
            </body>
            </html>';
            break;
        }

        // Проверяем, является ли это маршрутом удаления предложения /proposals/{id}/delete
        if (preg_match('#^/proposals/(\d+)/delete$#', $uri, $matches)) {
            $proposalId = (int)$matches[1];
            $proposal = getProposal($proposalId);

            if (!$proposal) {
                http_response_code(404);
                break;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    deleteProposal($proposalId);
                    header('Location: /proposals?success=' . urlencode('Предложение "' . $proposal['title'] . '" успешно удалено!'));
                    exit;
                } catch (Exception $e) {
                    $error = 'Ошибка удаления предложения: ' . $e->getMessage();
                }
            }
            break;
        }

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
            }

            if (empty($error)) {
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
}