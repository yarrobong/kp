<?php
namespace Models;

use PDO;

require_once __DIR__ . '/../core/Model.php';

/**
 * Модель товара
 */
class Product extends \Core\Model {
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
     * Получить все товары с fallback на JSON
     */
    public static function getAllWithFallback($userId = null) {
        try {
            $products = self::getAll($userId);
            if (!empty($products)) {
                return $products;
            }
        } catch (\Exception $e) {
            // Database connection failed, fallback to JSON
        }

        // Fallback to JSON
        $dataFile = __DIR__ . '/../products.json';
        if (file_exists($dataFile)) {
            $products = json_decode(file_get_contents($dataFile), true);
            if (is_array($products)) {
                // Filter by user_id if specified
                if ($userId) {
                    $products = array_filter($products, function($product) use ($userId) {
                        return $product['user_id'] == $userId;
                    });
                }
                return array_values($products);
            }
        }

        return [];
    }

    /**
     * Получить статистику товаров пользователя
     */
    public static function getUserStats($userId) {
        $db = self::getDB();
        if (!$db) return ['total' => 0, 'by_category' => []];

        try {
            // Общее количество товаров
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM " . self::$table . " WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetch()['total'];

            // Статистика по категориям
            $stmt = $db->prepare("SELECT category, COUNT(*) as count FROM " . self::$table . " WHERE user_id = ? AND category IS NOT NULL AND category != '' GROUP BY category");
            $stmt->execute([$userId]);
            $byCategory = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            return [
                'total' => $total,
                'by_category' => $byCategory
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'by_category' => []];
        }
    }

    /**
     * Поиск товаров
     */
    public static function search($query, $userId = null) {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT * FROM " . self::$table . " WHERE user_id = ? AND (name LIKE ? OR description LIKE ?)";
            $params = [$userId, "%$query%", "%$query%"];

            $sql .= " ORDER BY name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Получить товары по категории
     */
    public static function getByCategory($category, $userId = null) {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM " . self::$table . " WHERE user_id = ? AND category = ? ORDER BY created_at DESC");
            $stmt->execute([$userId, $category]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
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
