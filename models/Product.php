<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Модель товара
 */
class Product extends Model {
    protected static $table = 'products';

    /**
     * Получить все товары
     */
    public static function getAll($userId = null) {
        $conditions = [];
        if ($userId) {
            $conditions['user_id'] = $userId;
        }
        return self::all($conditions);
    }

    /**
     * Поиск товаров
     */
    public static function search($query, $userId = null) {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT * FROM " . self::$table . " WHERE (name LIKE ? OR description LIKE ?)";
            $params = ["%$query%", "%$query%"];

            if ($userId) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }

            $sql .= " ORDER BY name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Получить товары по категории
     */
    public static function getByCategory($category, $userId = null) {
        $conditions = ['category' => $category];
        if ($userId) {
            $conditions['user_id'] = $userId;
        }
        return self::all($conditions);
    }

    /**
     * Создать товар
     */
    public static function createProduct($data) {
        // Устанавливаем user_id если не указан
        if (!isset($data['user_id'])) {
            $data['user_id'] = 1;
        }

        // Устанавливаем значения по умолчанию
        $data = array_merge([
            'price' => 0,
            'category' => '',
            'description' => '',
            'image' => ''
        ], $data);

        return self::create($data);
    }

    /**
     * Обновить товар
     */
    public static function updateProduct($id, $data) {
        return self::update($id, $data);
    }

    /**
     * Удалить товар
     */
    public static function deleteProduct($id) {
        return self::delete($id);
    }

    /**
     * Получить товар с fallback на JSON
     */
    public static function findWithFallback($id) {
        $product = self::find($id);
        if ($product) {
            return $product;
        }

        // Fallback to JSON
        $dataFile = __DIR__ . '/../products.json';
        if (file_exists($dataFile)) {
            $products = json_decode(file_get_contents($dataFile), true);
            if (is_array($products)) {
                foreach ($products as $product) {
                    if ($product['id'] == $id) {
                        return $product;
                    }
                }
            }
        }

        return null;
    }
}
