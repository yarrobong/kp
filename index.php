<?php
/**
 * КП Генератор - Система автоматизации коммерческих предложений
 *
 * Полнофункциональное веб-приложение для:
 * - Управления каталогом товаров
 * - Создания коммерческих предложений
 * - Генерации PDF документов
 * - Отслеживания статусов предложений
 *
 * Автор: Yaroslav
 * Версия: 1.0.0
 * Дата: 2025
 */

// Определяем корневую директорию проекта
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__FILE__));
}

// Функция для генерации PDF коммерческого предложения
function generateProposalPDF($proposal) {
    require_once PROJECT_ROOT . '/vendor/autoload.php';

    // Создаем новый PDF документ
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Устанавливаем информацию о документе
    $pdf->SetCreator('КП Генератор');
    $pdf->SetAuthor('КП Генератор');
    $pdf->SetTitle('Коммерческое предложение');

    // Настраиваем footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->SetFooterMargin(15);

    // Кастомный footer
    $pdf->setFooterFont(Array('dejavusans', '', 8));
    $pdf->setFooterData(array(25, 118, 210), array(0, 0, 0)); // Синий цвет

    // Устанавливаем margins
    $pdf->SetMargins(20, 20, 20);
    $pdf->SetAutoPageBreak(true, 20);

    // Добавляем страницу
    $pdf->AddPage();

    // Получаем данные клиента
    $clientInfo = json_decode($proposal['client_info'], true);
    $clientName = $clientInfo['client_name'] ?? 'Клиент';
    $products = $clientInfo['products'] ?? [];

    // Устанавливаем шрифт
    $pdf->SetFont('dejavusans', '', 12);

    // Логотип/заголовок
    $pdf->SetFont('dejavusans', 'B', 24);
    $pdf->SetTextColor(25, 118, 210); // Синий цвет
    $pdf->Cell(0, 20, 'КП ГЕНЕРАТОР', 0, 1, 'C');
    $pdf->Ln(5);

    // Основной заголовок
    $pdf->SetFont('dejavusans', 'B', 18);
    $pdf->SetTextColor(33, 33, 33);
    $pdf->Cell(0, 12, 'КОММЕРЧЕСКОЕ ПРЕДЛОЖЕНИЕ', 0, 1, 'C');
    $pdf->Ln(8);

    // Номер предложения и дата
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, '№ ' . $proposal['offer_number'], 0, 1, 'R');
    $pdf->Cell(0, 10, 'от ' . date('d.m.Y', strtotime($proposal['offer_date'])), 0, 1, 'R');
    $pdf->Ln(10);

    // Информация о клиенте
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(0, 12, 'Уважаемый клиент: ' . $clientName, 1, 1, 'L', true);
    $pdf->Ln(5);

    // Введение
    $pdf->SetFont('dejavusans', '', 11);
    $pdf->MultiCell(0, 8, 'Мы рады представить Вам наше коммерческое предложение на поставку товаров и услуг. Предложение действительно в течение 30 дней с момента выставления.', 0, 'L');
    $pdf->Ln(10);

    // Таблица товаров
    $pdf->SetFont('dejavusans', 'B', 11);
    $pdf->SetFillColor(25, 118, 210);
    $pdf->SetTextColor(255, 255, 255);

    // Заголовки таблицы
    $pdf->Cell(80, 12, 'Наименование товара', 1, 0, 'C', true);
    $pdf->Cell(20, 12, 'Кол-во', 1, 0, 'C', true);
    $pdf->Cell(30, 12, 'Цена за ед.', 1, 0, 'C', true);
    $pdf->Cell(30, 12, 'Сумма', 1, 1, 'C', true);

    // Данные товаров
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $fill = false;
    foreach ($products as $product) {
        $quantity = $product['quantity'] ?? 1;
        $price = $product['price'] ?? 0;
        $lineTotal = $quantity * $price;

        // Чередуем цвета строк
        if ($fill) {
            $pdf->SetFillColor(248, 248, 248);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }

        // Наименование товара (с переносами)
        $pdf->MultiCell(80, 8, $product['name'], 1, 'L', $fill, 0);
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Количество
        $pdf->SetXY($x + 80, $y - 8);
        $pdf->Cell(20, 8, $quantity, 1, 0, 'C', $fill);

        // Цена
        $pdf->Cell(30, 8, number_format($price, 2, ',', ' ') . ' ₽', 1, 0, 'R', $fill);

        // Сумма
        $pdf->Cell(30, 8, number_format($lineTotal, 2, ',', ' ') . ' ₽', 1, 1, 'R', $fill);

        $fill = !$fill;
    }

    // Итого
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetFillColor(76, 175, 80); // Зеленый цвет для итого
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(130, 12, 'ИТОГО К ОПЛАТЕ:', 1, 0, 'R', true);
    $pdf->Cell(30, 12, number_format($proposal['total'], 2, ',', ' ') . ' ₽', 1, 1, 'R', true);

    $pdf->Ln(10);

    // Условия сотрудничества
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetTextColor(25, 118, 210);
    $pdf->Cell(0, 10, 'УСЛОВИЯ СОТРУДНИЧЕСТВА', 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $conditions = [
        '💳 Условия оплаты: 100% предоплата или в соответствии с договором поставки',
        '🚚 Условия доставки: Самовывоз или доставка транспортной компанией (тарифы отдельно)',
        '⏱️ Срок поставки: 3-7 рабочих дней после подтверждения заказа и оплаты',
        '📋 Все цены указаны без НДС. Возможны скидки при оптовых заказах',
        '📞 По вопросам обращайтесь к вашему менеджеру'
    ];

    foreach ($conditions as $condition) {
        $pdf->MultiCell(0, 6, $condition, 0, 'L');
        $pdf->Ln(1);
    }

    $pdf->Ln(8);

    // Контактная информация
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetTextColor(25, 118, 210);
    $pdf->Cell(0, 10, 'КОНТАКТНАЯ ИНФОРМАЦИЯ', 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 8, '📞 Телефон: +7 (495) 123-45-67', 0, 1, 'L');
    $pdf->Cell(0, 8, '📧 Email: info@kpgenerator.ru', 0, 1, 'L');
    $pdf->Cell(0, 8, '🌐 Сайт: www.kpgenerator.ru', 0, 1, 'L');

    $pdf->Ln(10);

    // Подпись
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'С уважением,', 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Команда КП Генератор', 0, 1, 'L');

    // Дата и подпись
    $pdf->Ln(10);
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(128, 128, 128);
    $pdf->Cell(0, 6, 'Предложение действительно до: ' . date('d.m.Y', strtotime($proposal['offer_date'] . ' +30 days')), 0, 1, 'R');
    $pdf->Cell(0, 6, 'Документ сформирован: ' . date('d.m.Y H:i'), 0, 1, 'R');

    // Кастомный footer
    $pdf->SetY(-20);
    $pdf->SetFont('dejavusans', '', 8);
    $pdf->SetTextColor(128, 128, 128);
    $pdf->Cell(0, 6, 'КП Генератор - автоматизация коммерческих предложений', 0, 0, 'C');
    $pdf->Cell(0, 6, 'Стр. ' . $pdf->getAliasNumPage() . ' из ' . $pdf->getAliasNbPages(), 0, 1, 'R');

    // Устанавливаем заголовки для скачивания
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="KP_' . $proposal['offer_number'] . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    // Выводим PDF в браузер
    $pdf->Output('KP_' . $proposal['offer_number'] . '.pdf', 'I');
}

// Функция для красивого отображения ошибок
function handleError($message, $code = 500, $title = 'Произошла ошибка') {
    http_response_code($code);
    $icon = match($code) {
        404 => '🔍',
        403 => '🚫',
        400 => '⚠️',
        default => '❌'
    };

    echo '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
        <link rel="stylesheet" href="/css/app.css">
        <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #121212;
            color: #e0e0e0;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background: #1e1e1e;
            border-radius: 16px;
            border: 1px solid #333;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 24px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #ffffff;
        }
        .error-message {
            font-size: 16px;
            color: #b0b0b0;
            margin-bottom: 32px;
            line-height: 1.5;
        }
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-actions .btn {
            padding: 12px 24px;
        }
        </style>
    </head>
    <body>
        <div class="error-page">
            <div class="error-container">
                <div class="error-icon">' . $icon . '</div>
                <div class="error-title">' . htmlspecialchars($title) . '</div>
                <div class="error-message">' . htmlspecialchars($message) . '</div>
                <div class="error-actions">
                    <a href="javascript:history.back()" class="btn btn-secondary">← Назад</a>
                    <a href="/" class="btn btn-primary">На главную</a>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// Оптимизация производительности - кэширование данных
$cache = [];
function getCachedData($key, $callback, $ttl = 300) {
    global $cache;
    $now = time();

    if (!isset($cache[$key]) || ($now - $cache[$key]['time']) > $ttl) {
        $cache[$key] = [
            'data' => $callback(),
            'time' => $now
        ];
    }

    return $cache[$key]['data'];
}

// Функция очистки кэша при изменениях
function clearCache() {
    global $cache;
    $cache = [];
}

// Вспомогательная функция для активного состояния навигации
function isActivePage($page) {
    global $uri;
    return strpos($uri, $page) === 0 ? 'active' : '';
}

// Вспомогательная функция для безопасного вывода
function safeHtml($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Вспомогательная функция для форматирования цены
function formatPrice($price) {
    return number_format((float)$price, 2, ',', ' ') . ' ₽';
}

// Вспомогательная функция для отображения уведомлений
function showNotification($message, $type = 'info') {
    $class = match($type) {
        'success' => 'alert-success',
        'error' => 'alert-error',
        'warning' => 'alert-warning',
        default => 'alert-info'
    };

    return "<div class='alert {$class}' role='alert'>{$message}</div>";
}

// Вспомогательная функция для проверки прав доступа
function checkUserAccess($resourceUserId, $currentUserId = 1) {
    return $resourceUserId == $currentUserId;
}

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
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $results = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
            }
            // Если в БД есть результаты, возвращаем их
            if (!empty($results)) {
                return $results;
            }
            // Если в БД нет результатов, переходим к JSON fallback
        } catch (Exception $e) {
            // Fallback на JSON при ошибке БД
        }
    }

    // Fallback на JSON файл
    $dataFile = PROJECT_ROOT . '/products.json';
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
    $dataFile = PROJECT_ROOT . '/products.json';
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
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            }
            // Если в БД нет результата, переходим к JSON fallback
        } catch (Exception $e) {
            // Fallback на JSON при ошибке БД
        }
    }

    // Fallback на JSON файл
    $dataFile = PROJECT_ROOT . '/products.json';
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
    $dataFile = PROJECT_ROOT . '/products.json';
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
    $dataFile = PROJECT_ROOT . '/products.json';
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
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $results = $db->query("SELECT * FROM proposals ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
            }
            // Если в базе данных есть результаты, возвращаем их
            if (!empty($results)) {
                return $results;
            }
            // Если результатов нет, переходим к JSON fallback
        } catch (Exception $e) {
            // Fallback на JSON при ошибке БД
        }
    }

    // Fallback на JSON файл
    $dataFile = PROJECT_ROOT . '/proposals.json';
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
            $dbId = $db->lastInsertId();
            if ($dbId) {
                error_log("Proposal saved to DB with ID: $dbId");
                return $dbId;
            }
        } catch (Exception $e) {
            error_log("DB save failed: " . $e->getMessage() . " - falling back to JSON");
        }
    }

    // Fallback на JSON файл
    $dataFile = PROJECT_ROOT . '/proposals.json';
    error_log("Saving to JSON file: $dataFile");

    $proposals = [];
    if (file_exists($dataFile)) {
        $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
        error_log("Loaded " . count($proposals) . " existing proposals from JSON");
    }

    $newId = 1;
    if (!empty($proposals)) {
        $maxId = max(array_column($proposals, 'id'));
        $newId = $maxId + 1;
    }

    $newProposal = [
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

    $proposals[] = $newProposal;
    $jsonResult = file_put_contents($dataFile, json_encode($proposals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    error_log("JSON save result: " . ($jsonResult ? "SUCCESS" : "FAILED") . ", new ID: $newId");
    error_log("New proposal data: " . json_encode($newProposal));

    return $newId;
}

function getProposal($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM proposals WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            }
        } catch (Exception $e) {
            // Fallback на JSON
        }
    }

    // Fallback на JSON файл
    $dataFile = PROJECT_ROOT . '/proposals.json';
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
    $dataFile = PROJECT_ROOT . '/proposals.json';
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
    $dataFile = PROJECT_ROOT . '/proposals.json';
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

    try {
    switch ($uri) {
    case '':
    case '/':
        include 'home.php';
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
                        <a href="/dashboard" class="<?php echo isActivePage('/dashboard'); ?>">Панель</a>
                        <a href="/products" class="<?php echo isActivePage('/products'); ?>">Товары</a>
                        <a href="/proposals" class="<?php echo isActivePage('/proposals'); ?>">КП</a>
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

        // Получить все товары для автокомплита
        $allProducts = getProducts($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientName = trim($_POST['client_name'] ?? '');
            $proposalItems = $_POST['proposal_items'] ?? [];
            $offerDate = $_POST['offer_date'] ?? date('Y-m-d');


            // Валидация имени клиента
            if (empty($clientName)) {
                $error = 'Имя клиента обязательно для заполнения';
            } elseif (strlen($clientName) < 2) {
                $error = 'Имя клиента должно содержать минимум 2 символа';
            } elseif (strlen($clientName) > 100) {
                $error = 'Имя клиента не должно превышать 100 символов';
            } elseif (!preg_match('/^[\p{L}\p{N}\s\-\.\(\)\[\]\"\'«»]+$/u', $clientName)) {
                $error = 'Имя клиента содержит недопустимые символы';
            } elseif (empty($proposalItems) || !is_array($proposalItems)) {
                $error = 'Добавьте хотя бы один товар в предложение';
            } elseif (count($proposalItems) > 50) {
                $error = 'Слишком много товаров в предложении (максимум 50)';
            } else {
                // Проверить и подготовить товары
                $total = 0;
                $proposalProducts = [];
                $validItems = 0;

                foreach ($proposalItems as $index => $item) {
                    $productId = trim($item['product_id'] ?? '');
                    $quantity = floatval($item['quantity'] ?? 0);

                    // Пропускаем пустые строки
                    if (empty($productId) && $quantity <= 0) {
                        continue;
                    }

                    // Валидация ID товара
                    if (empty($productId) || !is_numeric($productId)) {
                        $error = 'Некорректный ID товара в строке ' . ($index + 1);
                        break;
                    }

                    // Валидация количества
                    if ($quantity <= 0) {
                        $error = 'Количество товара должно быть больше 0 в строке ' . ($index + 1);
                        break;
                    }

                    if ($quantity > 999999) {
                        $error = 'Количество товара слишком большое в строке ' . ($index + 1);
                        break;
                    }

                    // Проверяем существование товара
                    $product = getProduct($productId);
                    if (!$product) {
                        $error = 'Товар не найден (ID: ' . $productId . ')';
                        break;
                    }

                    // Проверяем доступность товара
                    if ($product['user_id'] != $userId) {
                        $error = 'У вас нет доступа к этому товару';
                        break;
                    }

                    $product['quantity'] = $quantity;
                    $product['line_total'] = $product['price'] * $quantity;
                    $proposalProducts[] = $product;
                    $total += $product['line_total'];
                    $validItems++;
                }

                if ($validItems === 0) {
                    $error = 'Выберите товары и укажите количество';
                } else {
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

                        if ($proposalId) {
                            // Проверяем, что предложение действительно сохранено
                            $savedProposal = getProposal($proposalId);
                            if ($savedProposal) {
                                header('Location: /proposals/' . $proposalId);
                                exit;
                            } else {
                                $error = 'Предложение создано, но не найдено при проверке (ID: ' . $proposalId . ')';
                            }
                        } else {
                            $error = 'Не удалось создать предложение - ошибка генерации ID';
                        }
                    } catch (Exception $e) {
                        $error = 'Ошибка создания предложения: ' . $e->getMessage();
                    }
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
                        <a href="/dashboard" class="<?php echo isActivePage('/dashboard'); ?>">Панель</a>
                        <a href="/products" class="<?php echo isActivePage('/products'); ?>">Товары</a>
                        <a href="/proposals" class="<?php echo isActivePage('/proposals'); ?>">КП</a>
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

        // Подготовить данные товаров для JavaScript
        $productsJson = json_encode(array_map(function($product) {
            return [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'description' => $product['description'] ?? '',
                'category' => $product['category'] ?? '',
                'image' => getProductImage($product['image'])
            ];
        }, $allProducts));

        echo '<form method="POST" id="proposal-form">
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
                        <div class="products-table-container">
                            <table class="products-table" id="products-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Наименование товара</th>
                                        <th style="width: 15%;">Количество</th>
                                        <th style="width: 15%;">Цена за ед.</th>
                                        <th style="width: 15%;">Сумма</th>
                                        <th style="width: 10%;">Действия</th>
                                    </tr>
                                </thead>
                                <tbody id="products-tbody">
                                    <!-- Строки будут добавляться динамически -->
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="3" style="text-align: right; font-weight: bold;">Итого:</td>
                                        <td id="total-amount">₽ 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <button type="button" class="btn btn-secondary" id="add-product-btn" style="margin-top: 16px;">
                                ➕ Добавить товар
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">📄 Сформировать КП</button>
                        <a href="/proposals" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>

                <script>
                    const productsData = ' . $productsJson . ';

                    let rowCounter = 0;

                    function addProductRow(productId = "", quantity = 1) {
                        rowCounter++;
                        const rowId = "row_" + rowCounter;
                        const tbody = document.getElementById("products-tbody");

                        const row = document.createElement("tr");
                        row.id = rowId;
                        row.innerHTML = `
                            <td>
                                <input type="text" class="product-search" placeholder="Начните вводить название товара..." autocomplete="off">
                                <input type="hidden" name="proposal_items[${rowCounter}][product_id]" value="${productId}">
                                <div class="autocomplete-results" style="display: none;"></div>
                            </td>
                            <td>
                                <input type="number" name="proposal_items[${rowCounter}][quantity]" value="${quantity}" min="0.01" step="0.01" class="quantity-input">
                            </td>
                            <td class="unit-price">₽ 0.00</td>
                            <td class="line-total">₽ 0.00</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(\'${rowId}\')">🗑️</button>
                            </td>
                        `;

                        tbody.appendChild(row);

                        // Инициализировать автокомплит для новой строки
                        initAutocomplete(row.querySelector(".product-search"));

                        // Если передан productId, заполнить строку
                        if (productId) {
                            const product = productsData.find(p => p.id == productId);
                            if (product) {
                                fillProductRow(row, product, quantity);
                            }
                        }

                        // Инициализировать обработчик изменения количества
                        row.querySelector(".quantity-input").addEventListener("input", function() {
                            updateRowTotal(row);
                            updateTotal();
                        });

                        updateTotal();
                    }

                    function initAutocomplete(inputElement) {
                        const resultsDiv = inputElement.nextElementSibling.nextElementSibling;

                        inputElement.addEventListener("input", function() {
                            const query = this.value.toLowerCase();
                            if (query.length < 2) {
                                resultsDiv.style.display = "none";
                                return;
                            }

                            const matches = productsData.filter(product =>
                                product.name.toLowerCase().includes(query) ||
                                (product.description && product.description.toLowerCase().includes(query)) ||
                                (product.category && product.category.toLowerCase().includes(query))
                            );

                            if (matches.length > 0) {
                                resultsDiv.innerHTML = matches.map(product => `
                                    <div class="autocomplete-item" data-product-id="${product.id}">
                                        <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                        <div>
                                            <div style="font-weight: bold;">${product.name}</div>
                                            <div style="color: #666; font-size: 12px;">₽ ${product.price.toLocaleString()}</div>
                                            ${product.description ? `<div style="color: #999; font-size: 11px;">${product.description.substring(0, 50)}...</div>` : ""}
                                        </div>
                                    </div>
                                `).join("");
                                resultsDiv.style.display = "block";
                            } else {
                                resultsDiv.style.display = "none";
                            }
                        });

                        inputElement.addEventListener("blur", function() {
                            setTimeout(() => {
                                resultsDiv.style.display = "none";
                            }, 200);
                        });

                        resultsDiv.addEventListener("click", function(e) {
                            const item = e.target.closest(".autocomplete-item");
                            if (item) {
                                const productId = item.dataset.productId;
                                const product = productsData.find(p => p.id == productId);
                                const row = inputElement.closest("tr");
                                fillProductRow(row, product);
                                resultsDiv.style.display = "none";
                            }
                        });
                    }

                    function fillProductRow(row, product, quantity = 1) {
                        row.querySelector(".product-search").value = product.name;
                        row.querySelector("input[type=\"hidden\"]").value = product.id;
                        row.querySelector(".quantity-input").value = quantity;
                        row.querySelector(".unit-price").textContent = "₽ " + product.price.toLocaleString();
                        updateRowTotal(row);
                    }

                    function updateRowTotal(row) {
                        const quantity = parseFloat(row.querySelector(".quantity-input").value) || 0;
                        const unitPriceText = row.querySelector(".unit-price").textContent;
                        const unitPrice = parseFloat(unitPriceText.replace("₽ ", "").replace(/\s/g, "").replace(",", ".")) || 0;
                        const lineTotal = quantity * unitPrice;
                        row.querySelector(".line-total").textContent = "₽ " + lineTotal.toLocaleString();
                    }

                    function updateTotal() {
                        let total = 0;
                        document.querySelectorAll(".line-total").forEach(function(element) {
                            const amount = parseFloat(element.textContent.replace("₽ ", "").replace(/\s/g, "").replace(",", ".")) || 0;
                            total += amount;
                        });
                        document.getElementById("total-amount").textContent = "₽ " + total.toLocaleString();
                    }

                    function removeProductRow(rowId) {
                        const row = document.getElementById(rowId);
                        if (row) {
                            row.remove();
                            updateTotal();
                        }
                    }

                        // Инициализация
                        document.getElementById("add-product-btn").addEventListener("click", function() {
                            addProductRow();
                        });

                        // Добавить первую пустую строку при загрузке
                        document.addEventListener("DOMContentLoaded", function() {
                            addProductRow();
                        });

                        // Отладка формы перед отправкой
                        document.getElementById("proposal-form").addEventListener("submit", function(e) {
                            console.log("Form data before submit:");
                            const formData = new FormData(this);
                            for (let [key, value] of formData.entries()) {
                                console.log(key + ": " + value);
                            }

                            // Проверить что есть хотя бы один товар
                            const productInputs = formData.getAll("proposal_items[1][product_id]");
                            if (productInputs.length === 0 || !productInputs[0]) {
                                alert("Пожалуйста, выберите хотя бы один товар!");
                                e.preventDefault();
                                return false;
                            }
                        });
                </script>
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
        // В демо-версии просто перенаправляем на главную страницу
        header('Location: /');
        exit;

    case '/health':
        // Проверка состояния системы
        header('Content-Type: application/json');
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => '1.0.0',
            'php' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'database' => getDB() ? 'connected' : 'json_fallback',
            'files' => [
                'products.json' => file_exists(PROJECT_ROOT . '/products.json'),
                'proposals.json' => file_exists(PROJECT_ROOT . '/proposals.json'),
            ]
        ];
        echo json_encode($health, JSON_PRETTY_PRINT);
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

        // Проверяем, является ли это маршрутом скачивания PDF /proposals/{id}/pdf
        if (preg_match('#^/proposals/(\d+)/pdf$#', $uri, $matches)) {
            $proposalId = (int)$matches[1];
            $proposal = getProposal($proposalId);

            if (!$proposal) {
                handleError('Предложение не найдено', 404, 'Предложение не найдено');
            }

            // Проверяем права доступа
            if ($proposal['user_id'] != $userId) {
                handleError('У вас нет доступа к этому предложению', 403, 'Доступ запрещен');
            }

            // Генерируем PDF
            generateProposalPDF($proposal);
            exit;
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
                    .proposal-product-details {
                        margin-top: 12px;
                    }
                    .proposal-product-quantity,
                    .proposal-product-unit-price {
                        font-size: 14px;
                        color: #b0b0b0;
                        margin-bottom: 4px;
                    }
                    .proposal-product-line-total {
                        font-size: 18px;
                        font-weight: 600;
                        color: #1976d2;
                        margin-top: 8px;
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
                    $quantity = $product['quantity'] ?? 1;
                    $lineTotal = $product['line_total'] ?? ($product['price'] * $quantity);
                    echo '<div class="proposal-product">
                                <img src="' . htmlspecialchars(getProductImage($product['image'])) . '" alt="' . htmlspecialchars($product['name']) . '" class="proposal-product-image">
                                <div class="proposal-product-info">
                                    <div class="proposal-product-title">' . htmlspecialchars($product['name']) . '</div>
                                    ' . (!empty($product['description']) ? '<div class="proposal-product-description">' . htmlspecialchars($product['description']) . '</div>' : '') . '
                                    <div class="proposal-product-details">
                                        <div class="proposal-product-quantity">Количество: ' . number_format($quantity, 2, ',', ' ') . '</div>
                                        <div class="proposal-product-unit-price">Цена за ед.: ₽ ' . number_format($product['price'], 2, ',', ' ') . '</div>
                                        <div class="proposal-product-line-total">Сумма: ₽ ' . number_format($lineTotal, 2, ',', ' ') . '</div>
                                    </div>
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
                        <a href="/proposals/' . $proposal['id'] . '/pdf" class="btn btn-success" target="_blank">📄 Скачать PDF</a>
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
            $selectedProducts = $clientInfo['products'] ?? [];

            $error = '';
            $success = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $clientName = trim($_POST['client_name'] ?? '');
                $proposalItems = $_POST['proposal_items'] ?? [];
                $offerDate = $_POST['offer_date'] ?? $proposal['offer_date'];
                $status = $_POST['status'] ?? $proposal['status'];

                if (empty($clientName)) {
                    $error = 'Имя клиента обязательно';
                } elseif (empty($proposalItems) || !is_array($proposalItems)) {
                    $error = 'Добавьте хотя бы один товар';
                } else {
                    // Проверить и подготовить товары
                    $total = 0;
                    $proposalProducts = [];
                    $validItems = 0;

                    foreach ($proposalItems as $item) {
                        $productId = $item['product_id'] ?? '';
                        $quantity = floatval($item['quantity'] ?? 0);

                        if (!empty($productId) && $quantity > 0) {
                            $product = getProduct($productId);
                            if ($product) {
                                $product['quantity'] = $quantity;
                                $product['line_total'] = $product['price'] * $quantity;
                                $proposalProducts[] = $product;
                                $total += $product['line_total'];
                                $validItems++;
                            }
                        }
                    }

                    if ($validItems === 0) {
                        $error = 'Выберите товары и укажите количество';
                    } else {
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
            }

            // Получить все товары для выбора
            $allProducts = getProducts($userId);

            // Подготовить данные товаров для JavaScript
            $productsJson = json_encode(array_map(function($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'description' => $product['description'] ?? '',
                    'category' => $product['category'] ?? '',
                    'image' => getProductImage($product['image'])
                ];
            }, $allProducts));

            echo '<!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Редактировать предложение</title>
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
                    <div class="page-header">
                        <h1>Редактировать предложение</h1>
                        <a href="/proposals/' . $proposal['id'] . '" class="btn btn-secondary">← Назад</a>
                    </div>';

            if (!empty($error)) {
                echo '<div class="alert alert-error">' . $error . '</div>';
            }

            echo '<form method="POST" id="proposal-form">
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
                            <div class="products-table-container">
                                <table class="products-table" id="products-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Наименование товара</th>
                                            <th style="width: 15%;">Количество</th>
                                            <th style="width: 15%;">Цена за ед.</th>
                                            <th style="width: 15%;">Сумма</th>
                                            <th style="width: 10%;">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-tbody">';

            // Заполнить таблицу существующими товарами
            if (!empty($selectedProducts)) {
                $counter = 0;
                foreach ($selectedProducts as $product) {
                    $counter++;
                    $quantity = $product['quantity'] ?? 1;
                    echo '<tr id="row_' . $counter . '">
                                <td>
                                    <input type="text" class="product-search" value="' . htmlspecialchars($product['name']) . '" placeholder="Начните вводить название товара..." autocomplete="off">
                                    <input type="hidden" name="proposal_items[' . $counter . '][product_id]" value="' . $product['id'] . '">
                                    <div class="autocomplete-results" style="display: none;"></div>
                                </td>
                                <td>
                                    <input type="number" name="proposal_items[' . $counter . '][quantity]" value="' . $quantity . '" min="0.01" step="0.01" class="quantity-input">
                                </td>
                                <td class="unit-price">₽ ' . number_format($product['price'], 2, ',', ' ') . '</td>
                                <td class="line-total">₽ ' . number_format($product['price'] * $quantity, 2, ',', ' ') . '</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(\'row_' . $counter . '\')">🗑️</button>
                                </td>
                            </tr>';
                }
            }

            echo '</tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="3" style="text-align: right; font-weight: bold;">Итого:</td>
                                            <td id="total-amount">₽ ' . number_format($proposal['total'], 2, ',', ' ') . '</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <button type="button" class="btn btn-secondary" id="add-product-btn" style="margin-top: 16px;">
                                    ➕ Добавить товар
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
                            <a href="/proposals/' . $proposal['id'] . '" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>

                    <script>
                        const productsData = ' . $productsJson . ';

                        let rowCounter = ' . (count($selectedProducts) ?: 0) . ';

                        function addProductRow(productId = "", quantity = 1) {
                            rowCounter++;
                            const rowId = "row_" + rowCounter;
                            const tbody = document.getElementById("products-tbody");

                            const row = document.createElement("tr");
                            row.id = rowId;
                            row.innerHTML = `
                                <td>
                                    <input type="text" class="product-search" placeholder="Начните вводить название товара..." autocomplete="off">
                                    <input type="hidden" name="proposal_items[${rowCounter}][product_id]" value="${productId}">
                                    <div class="autocomplete-results" style="display: none;"></div>
                                </td>
                                <td>
                                    <input type="number" name="proposal_items[${rowCounter}][quantity]" value="${quantity}" min="0.01" step="0.01" class="quantity-input">
                                </td>
                                <td class="unit-price">₽ 0.00</td>
                                <td class="line-total">₽ 0.00</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(\'${rowId}\')">🗑️</button>
                                </td>
                            `;

                            tbody.appendChild(row);

                            // Инициализировать автокомплит для новой строки
                            initAutocomplete(row.querySelector(".product-search"));

                            // Если передан productId, заполнить строку
                            if (productId) {
                                const product = productsData.find(p => p.id == productId);
                                if (product) {
                                    fillProductRow(row, product, quantity);
                                }
                            }

                            // Инициализировать обработчик изменения количества
                            row.querySelector(".quantity-input").addEventListener("input", function() {
                                updateRowTotal(row);
                                updateTotal();
                            });

                            updateTotal();
                        }

                        function initAutocomplete(inputElement) {
                            const resultsDiv = inputElement.nextElementSibling.nextElementSibling;

                            inputElement.addEventListener("input", function() {
                                const query = this.value.toLowerCase();
                                if (query.length < 2) {
                                    resultsDiv.style.display = "none";
                                    return;
                                }

                                const matches = productsData.filter(product =>
                                    product.name.toLowerCase().includes(query) ||
                                    (product.description && product.description.toLowerCase().includes(query)) ||
                                    (product.category && product.category.toLowerCase().includes(query))
                                );

                                if (matches.length > 0) {
                                    resultsDiv.innerHTML = matches.map(product => `
                                        <div class="autocomplete-item" data-product-id="${product.id}">
                                            <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                            <div>
                                                <div style="font-weight: bold;">${product.name}</div>
                                                <div style="color: #666; font-size: 12px;">₽ ${product.price.toLocaleString()}</div>
                                                ${product.description ? `<div style="color: #999; font-size: 11px;">${product.description.substring(0, 50)}...</div>` : ""}
                                            </div>
                                        </div>
                                    `).join("");
                                    resultsDiv.style.display = "block";
                                } else {
                                    resultsDiv.style.display = "none";
                                }
                            });

                            inputElement.addEventListener("blur", function() {
                                setTimeout(() => {
                                    resultsDiv.style.display = "none";
                                }, 200);
                            });

                            resultsDiv.addEventListener("click", function(e) {
                                const item = e.target.closest(".autocomplete-item");
                                if (item) {
                                    const productId = item.dataset.productId;
                                    const product = productsData.find(p => p.id == productId);
                                    const row = inputElement.closest("tr");
                                    fillProductRow(row, product);
                                    resultsDiv.style.display = "none";
                                }
                            });
                        }

                        function fillProductRow(row, product, quantity = 1) {
                            row.querySelector(".product-search").value = product.name;
                            row.querySelector("input[type=\"hidden\"]").value = product.id;
                            row.querySelector(".quantity-input").value = quantity;
                            row.querySelector(".unit-price").textContent = "₽ " + product.price.toLocaleString();
                            updateRowTotal(row);
                        }

                        function updateRowTotal(row) {
                            const quantity = parseFloat(row.querySelector(".quantity-input").value) || 0;
                            const unitPriceText = row.querySelector(".unit-price").textContent;
                            const unitPrice = parseFloat(unitPriceText.replace("₽ ", "").replace(/\s/g, "").replace(",", ".")) || 0;
                            const lineTotal = quantity * unitPrice;
                            row.querySelector(".line-total").textContent = "₽ " + lineTotal.toLocaleString();
                        }

                        function updateTotal() {
                            let total = 0;
                            document.querySelectorAll(".line-total").forEach(function(element) {
                                const amount = parseFloat(element.textContent.replace("₽ ", "").replace(/\s/g, "").replace(",", ".")) || 0;
                                total += amount;
                            });
                            document.getElementById("total-amount").textContent = "₽ " + total.toLocaleString();
                        }

                        function removeProductRow(rowId) {
                            const row = document.getElementById(rowId);
                            if (row) {
                                row.remove();
                                updateTotal();
                            }
                        }

                        // Инициализация
                        document.getElementById("add-product-btn").addEventListener("click", function() {
                            addProductRow();
                        });

                        // Инициализировать существующие строки
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelectorAll("#products-tbody tr").forEach(function(row) {
                                initAutocomplete(row.querySelector(".product-search"));
                                row.querySelector(".quantity-input").addEventListener("input", function() {
                                    updateRowTotal(row);
                                    updateTotal();
                                });
                            });
                        });
                    </script>
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
        handleError(
            "Запрашиваемая страница не существует. Проверьте правильность URL адреса.",
            404,
            "Страница не найдена"
        );
        break;
    }
    } catch (Exception $e) {
        error_log("Application error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        handleError(
            "Произошла непредвиденная ошибка. Пожалуйста, попробуйте еще раз или обратитесь к администратору.",
            500,
            "Внутренняя ошибка сервера"
        );
    } catch (Error $e) {
        error_log("PHP Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        handleError(
            "Произошла критическая ошибка. Пожалуйста, обратитесь к администратору.",
            500,
            "Критическая ошибка"
        );
    }
}