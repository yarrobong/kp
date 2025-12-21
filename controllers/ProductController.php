<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';

/**
 * Контроллер товаров
 */
class ProductController extends Controller {

    /**
     * Список всех товаров
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        if ($search) {
            $products = Product::search($search);
        } elseif ($category) {
            $products = Product::getByCategory($category);
        } else {
            $products = Product::getAll();
        }

        $this->render('products/index', [
            'title' => 'Товары',
            'products' => $products,
            'search' => $search,
            'category' => $category
        ]);
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
        $data = $this->getPostData();

        // Валидация
        if (empty($data['name'])) {
            $this->redirect('/products/create', 'Название товара обязательно', 'error');
            return;
        }

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

        $this->render('products/show', [
            'title' => $product['name'],
            'product' => $product
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
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
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
