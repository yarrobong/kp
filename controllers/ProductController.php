<?php
namespace Controllers;

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';

use Models\Product;

/**
 * Контроллер товаров
 */
class ProductController extends \Core\Controller {

    public function __construct() {
        // Проверяем авторизацию для создания/редактирования товаров
        $action = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($action, '/products/create') !== false ||
            strpos($action, '/products/') !== false && strpos($action, '/edit') !== false) {
            AuthController::requireAuth();
        }
    }

    /**
     * Список всех товаров
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        // Только авторизованные пользователи могут видеть товары
        $user = AuthController::getCurrentUser();
        if (!$user) {
            $this->render('auth/redirect', [
                'title' => 'Требуется авторизация',
                'redirectUrl' => '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']),
                'message' => 'Для просмотра товаров необходимо войти в систему.'
            ]);
            return;
        }


        // Для авторизованных пользователей показываем их товары
        if ($search) {
            $products = Product::search($search, $user['id']);
        } elseif ($category) {
            $products = Product::getByCategory($category, $user['id']);
        } else {
            $products = Product::getAll($user['id']);
        }

        $pageTitle = 'Товары';
        if ($search) {
            $pageTitle = "Поиск товаров: $search";
        } elseif ($category) {
            $pageTitle = "Товары категории: $category";
        }

        $description = 'Управление каталогом товаров в системе КП Генератор. Добавляйте, редактируйте и удаляйте товары для создания коммерческих предложений.';
        $keywords = 'товары, каталог товаров, управление товарами, добавление товаров, редактирование товаров, категории товаров';

        if ($search) {
            $description = "Результаты поиска товаров по запросу '$search'. Найдено " . count($products) . " товаров.";
            $keywords .= ", поиск товаров, $search";
        }

        $this->render('products/index', [
            'title' => $pageTitle . ' - КП Генератор',
            'description' => $description,
            'keywords' => $keywords,
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => $pageTitle,
            'og_description' => $description,
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'user' => $user
        ]);
    }

    /**
     * Получить публичные товары (админов)
     */
    private function getPublicProducts() {
        // Используем прямое подключение к БД для публичных товаров
        try {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $db = new PDO($dsn, $config['username'], $config['password'], $config['options']);

            $stmt = $db->query("
                SELECT p.* FROM products p
                JOIN users u ON p.user_id = u.id
                WHERE u.role = 'admin'
                ORDER BY p.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Форма создания товара
     */
    public function create() {
        $this->render('products/create', [
            'title' => 'Добавить товар'
        ]);
    }

    /**
     * Сохранение нового товара
     */
    public function store() {
        $user = AuthController::getCurrentUser();
        $data = $this->getPostData();

        // Валидация
        if (empty($data['name'])) {
            $this->redirect('/products/create', 'Название товара обязательно', 'error');
            return;
        }

        // Добавляем ID пользователя
        $data['user_id'] = $user['id'];

        // Обработка файла изображения
        $data['image'] = $this->handleImageUpload();

        $productId = Product::createProduct($data);

        if ($productId) {
            $this->redirect('/products', 'Товар успешно добавлен', 'success');
        } else {
            $this->redirect('/products/create', 'Ошибка при добавлении товара', 'error');
        }
    }

    /**
     * Просмотр товара
     */
    public function show($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $product = Product::findWithFallback($id);
        if (!$product) {
            $this->error404('Товар не найден');
            return;
        }

        // Проверяем права доступа (владелец или админ)
        $user = AuthController::getCurrentUser();
        if (!$this->canAccessProduct($product, $user)) {
            $this->error403('Доступ к товару запрещен');
            return;
        }

        // Структурированные данные для товара
        $structuredData = [
            "@type" => "Product",
            "name" => $product['name'],
            "description" => $product['description'],
            "category" => $product['category'],
            "offers" => [
                "@type" => "Offer",
                "price" => $product['price'],
                "priceCurrency" => "RUB",
                "availability" => "https://schema.org/InStock"
            ],
            "image" => $product['image'] ?: '/css/placeholder-product.svg'
        ];

        $this->render('products/show', [
            'title' => $product['name'] . ' - Купить в КП Генератор',
            'description' => $product['description'] . ' Цена: ' . number_format($product['price'], 0, ',', ' ') . ' ₽. ' . ($product['category'] ? 'Категория: ' . $product['category'] . '.' : ''),
            'keywords' => $product['name'] . ', ' . $product['category'] . ', купить, цена, ' . number_format($product['price'], 0, ',', ' ') . ' руб',
            'robots' => 'index, follow',
            'og_type' => 'product',
            'og_title' => $product['name'],
            'og_description' => $product['description'] . ' - ' . number_format($product['price'], 0, ',', ' ') . ' ₽',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'og_image' => $product['image'] ?: '/css/placeholder-product.svg',
            'structured_data' => $structuredData,
            'product' => $product,
            'user' => $user
        ]);
    }

    /**
     * Форма редактирования товара
     */
    public function edit($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $product = Product::findWithFallback($id);
        if (!$product) {
            $this->error404('Товар не найден');
            return;
        }

        $user = AuthController::getCurrentUser();
        if (!$this->canEditProduct($product, $user)) {
            $this->error403('Доступ к редактированию товара запрещен');
            return;
        }

        $this->render('products/edit', [
            'title' => 'Редактировать товар',
            'product' => $product
        ]);
    }

    /**
     * Обновление товара
     */
    public function update($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $product = Product::findWithFallback($id);
        if (!$product) {
            $this->error404('Товар не найден');
            return;
        }

        $user = AuthController::getCurrentUser();
        if (!$this->canEditProduct($product, $user)) {
            $this->error403('Доступ к редактированию товара запрещен');
            return;
        }

        $data = $this->getPostData();

        // Валидация
        if (empty($data['name'])) {
            $this->redirect("/products/{$id}/edit", 'Название товара обязательно', 'error');
            return;
        }

        // Обработка файла изображения
        $imagePath = $this->handleImageUpload();
        if ($imagePath !== '/css/placeholder-product.svg') {
            $data['image'] = $imagePath;
        }

        $success = Product::updateProduct($id, $data);

        if ($success) {
            $this->redirect('/products', 'Товар успешно обновлен', 'success');
        } else {
            $this->redirect("/products/{$id}/edit", 'Ошибка при обновлении товара', 'error');
        }
    }

    /**
     * Удаление товара
     */
    public function delete($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $product = Product::findWithFallback($id);
        if (!$product) {
            $this->error404('Товар не найден');
            return;
        }

        $user = AuthController::getCurrentUser();
        if (!$this->canEditProduct($product, $user)) {
            $this->error403('Доступ к удалению товара запрещен');
            return;
        }

        $success = Product::deleteProduct($id);

        if ($success) {
            $this->redirect('/products', 'Товар успешно удален', 'success');
        } else {
            $this->redirect('/products', 'Ошибка при удалении товара', 'error');
        }
    }

    /**
     * API для поиска товаров (AJAX)
     */
    public function search() {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            $this->json([]);
            return;
        }

        $products = Product::search($query);
        $this->json($products);
    }

    /**
     * Проверить права доступа к товару
     */
    private function canAccessProduct($product, $user) {
        if (!$user) return false;

        // Админ может видеть все товары
        if ($user['role'] === 'admin') {
            return true;
        }

        // Пользователь может видеть только свои товары
        return $product['user_id'] == $user['id'];
    }

    /**
     * Проверить права редактирования товара
     */
    private function canEditProduct($product, $user) {
        if (!$user) return false;

        // Админ может редактировать все товары
        if ($user['role'] === 'admin') {
            return true;
        }

        // Пользователь может редактировать только свои товары
        return $product['user_id'] == $user['id'];
    }

    /**
     * Обработка загрузки изображения товара
     */
    private function handleImageUpload() {
        // Проверяем, был ли загружен файл
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return '/css/placeholder-product.svg'; // Возвращаем заглушку если файл не загружен
        }

        $file = $_FILES['image'];

        // Проверяем на ошибки загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->redirect('/products/create', 'Ошибка при загрузке файла', 'error');
            return '/css/placeholder-product.svg';
        }

        // Проверяем тип файла
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->redirect('/products/create', 'Недопустимый тип файла. Разрешены: JPG, PNG, GIF, WebP', 'error');
            return '/css/placeholder-product.svg';
        }

        // Проверяем размер файла (5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            $this->redirect('/products/create', 'Файл слишком большой. Максимальный размер: 5MB', 'error');
            return '/css/placeholder-product.svg';
        }

        // Создаем директорию для загрузок если не существует
        $uploadDir = '/var/www/html/public/uploads/products/';
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
        } else {
            $this->redirect('/products/create', 'Ошибка при сохранении файла', 'error');
            return '/css/placeholder-product.svg';
        }
    }
}
